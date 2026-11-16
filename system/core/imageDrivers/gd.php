<?php

namespace icms\core\imageDrivers;

use icms\core\imageDrivers\imagesDriverInterface;

class gd implements imagesDriverInterface {

    private $image;
    private $width  = 0;
    private $height = 0;

    public static function isAvailable() {
        return extension_loaded('gd');
    }

    public function title() {
        return 'GD';
    }

    public function load(string $filename, string $image_type) {

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $this->image = $this->loadJpeg($filename);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_WEBP:
                $this->image = @imagecreatefromwebp($filename);
                break;
            default:
                throw new \Exception('Unsupported image type for GD');
        }

        if (!$this->image) {
            throw new \Exception('GD could not load image');
        }

        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    public function isSourceAnimate() {
        return false;
    }

    private function loadJpeg(string $filename) {

        $image = imagecreatefromjpeg($filename);
        if (!$image) {
            return false;
        }

        if (!function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($filename);
        if (empty($exif['Orientation'])) {
            return $image;
        }

        $orientation = (int)$exif['Orientation'];

        switch ($orientation) {
            case 3:
            case 4:
                $image = imagerotate($image, 180, 0);
                break;
            case 5:
            case 6:
                $image = imagerotate($image, 270, 0);
                break;
            case 7:
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
        switch ($orientation) {
            case 4:
            case 5:
            case 7:
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
        }

        return $image;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function save(string $filename, int $image_type, ?int $quality, array $geometry, array $options) {

        if ($image_type == IMAGETYPE_PNG && !imageistruecolor($this->image)) {
            $dest_image = imagecreate($geometry['dest_w'], $geometry['dest_h']);
        } else {
            $dest_image = imagecreatetruecolor($geometry['dest_w'], $geometry['dest_h']);
        }

        switch ($image_type) {
            case IMAGETYPE_GIF:

                $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 1);

                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);
                imagesavealpha($dest_image, true);

                break;
            case IMAGETYPE_JPEG:

                $background = imagecolorallocate($dest_image, 255, 255, 255);

                imagefilledrectangle($dest_image, 0, 0, $geometry['dest_w'], $geometry['dest_h'], $background);

                break;
            case IMAGETYPE_WEBP:

                $background = imagecolorallocate($dest_image, 255, 255, 255);

                imagefilledrectangle($dest_image, 0, 0, $geometry['dest_w'], $geometry['dest_h'], $background);

                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);

                break;
            case IMAGETYPE_PNG:

                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);

                $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);

                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);

                break;
        }

        if (!empty($options['gamma_correct'])) {
            imagegammacorrect($this->image, 2.2, 1.0);
        }

        imagecopyresampled(
            $dest_image,
            $this->image,
            $geometry['dest_x'], $geometry['dest_y'],
            $geometry['source_x'], $geometry['source_y'],
            $geometry['dest_w'], $geometry['dest_h'],
            $geometry['source_w'], $geometry['source_h']
        );

        if (!empty($options['gamma_correct'])) {
            imagegammacorrect($dest_image, 1.0, 2.2);
        }

        if (!empty($options['watermark']['file'])) {
            $this->addWatermark($dest_image, $options['watermark']['file'], $options['watermark']['x'], $options['watermark']['y']);
        }

        imageinterlace($dest_image, !empty($options['interlace']));

        $success = false;

        switch ($image_type) {
            case IMAGETYPE_GIF:
                $success = imagegif($dest_image, $filename);
                break;
            case IMAGETYPE_JPEG:

                if ($quality === null || $quality > 100) {
                    $quality = self::DEFAULT_QLTY_JPG;
                }

                $success = imagejpeg($dest_image, $filename, $quality);

                break;
            case IMAGETYPE_WEBP:

                if ($quality === null) {
                    $quality = self::DEFAULT_QLTY_WEBP;
                }

                $success = imagewebp($dest_image, $filename, $quality);

                break;
            case IMAGETYPE_PNG:

                if ($quality === null || $quality > 100) {
                    $quality = self::DEFAULT_QLTY_PNG;
                } else {
                    $quality = 9 - (int)round((9 * max(0, min(100, $quality))) / 100);
                }
                $quality = max(0, min(9, $quality));

                $success = imagepng($dest_image, $filename, $quality, PNG_ALL_FILTERS);

                break;
        }

        return $success;
    }

    private function addWatermark($dest_image, $filename, $x, $y) {

        $info = getimagesize($filename);
        if (!$info) {
            return;
        }

        switch ($info[2]) {
            case IMAGETYPE_JPEG: $wm = imagecreatefromjpeg($filename); break;
            case IMAGETYPE_PNG:  $wm = imagecreatefrompng($filename); break;
            case IMAGETYPE_GIF:  $wm = imagecreatefromgif($filename); break;
            case IMAGETYPE_WEBP: $wm = imagecreatefromwebp($filename); break;
            default: $wm = false;
        }

        if (!$wm) {
            return;
        }

        imagealphablending($dest_image, true);
        imagecopy($dest_image, $wm, $x, $y, 0, 0, imagesx($wm), imagesy($wm));
    }

    public function destroy() {
        $this->width  = 0;
        $this->height = 0;
        $this->image  = null;
    }

}
