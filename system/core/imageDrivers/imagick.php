<?php

namespace icms\core\imageDrivers;

use icms\core\imageDrivers\imagesDriverInterface;

class imagick implements imagesDriverInterface {

    private $image;
    private $width  = 0;
    private $height = 0;
    private $is_source_animate_format = false;

    public static function isAvailable() {
        return extension_loaded('imagick') && class_exists('\Imagick');
    }

    public function title() {
        return 'Imagick';
    }

    public function load(string $filename, string $image_type) {

        try {

            $this->image = new \Imagick();
            $this->image->readImage($filename);

            if ($image_type === IMAGETYPE_JPEG) {
                $this->orientJpeg();
            }

            $this->is_source_animate_format = in_array($image_type, [IMAGETYPE_GIF, IMAGETYPE_WEBP]) && $this->image->getNumberImages() > 1;

            if ($this->is_source_animate_format) {
                $this->image = $this->image->coalesceImages();
            }

            $this->image->setFirstIterator();

            $this->width  = $this->image->getImageWidth();
            $this->height = $this->image->getImageHeight();

        } catch (\ImagickException $e) {

            $this->destroy();

            throw new \Exception('Imagick could not load image');
        }
    }

    public function isSourceAnimate() {
        return $this->is_source_animate_format;
    }

    private function orientJpeg() {

        $orientation = $this->image->getImageOrientation();

        switch ($orientation) {
            case \Imagick::ORIENTATION_TOPRIGHT:
                $this->image->flopImage();
                break;
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $this->image->rotateImage(new \ImagickPixel('none'), 180);
                break;
            case \Imagick::ORIENTATION_BOTTOMLEFT:
                $this->image->rotateImage(new \ImagickPixel('none'), 180);
                $this->image->flopImage();
                break;
            case \Imagick::ORIENTATION_LEFTTOP:
                $this->image->rotateImage(new \ImagickPixel('none'), 90);
                $this->image->flopImage();
                break;
            case \Imagick::ORIENTATION_RIGHTTOP:
                $this->image->rotateImage(new \ImagickPixel('none'), 90);
                break;
            case \Imagick::ORIENTATION_RIGHTBOTTOM:
                $this->image->rotateImage(new \ImagickPixel('none'), -90);
                $this->image->flopImage();
                break;
            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $this->image->rotateImage(new \ImagickPixel('none'), -90);
                break;
        }

        $this->image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
    }

    private function stripFrame($frame) {
        $frame->stripImage();
    }

    private function restorePreservedProfiles($frame, array $profiles) {
        foreach ($profiles as $key => $profile) {
            $frame->profileImage($key, $profile);
        }
    }

    private function collectPreservedProfiles($frame) {

        $profiles = [];

        foreach (['icc'] as $key) {
            $result = $frame->getImageProfiles($key);
            if (array_key_exists($key, $result)) {
                $profiles[$key] = $result[$key];
            }
        }

        return $profiles;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    private function getFormatName($image_type) {
        switch ($image_type) {
            case IMAGETYPE_GIF:  return 'gif';
            case IMAGETYPE_JPEG: return 'jpeg';
            case IMAGETYPE_PNG:  return 'png';
            case IMAGETYPE_WEBP: return 'webp';
            default: return false;
        }
    }

    private function prepareQuality($image_type, $quality) {

        if ($quality === null || $quality > 100) {
            switch ($image_type) {
                case IMAGETYPE_JPEG: $quality = self::DEFAULT_QLTY_JPG; break;
                case IMAGETYPE_WEBP: $quality = self::DEFAULT_QLTY_WEBP; break;
                default: $quality = 90;
            }
        } else {
            $quality = max(0, min(100, $quality));
        }

        return $quality;
    }

    private function renderFrame($source, $background, $geometry, $options) {

        if (!empty($options['gamma_correct'])) {
            $source->gammaImage(1 / 2.2);
        }

        $source->cropImage(
            $geometry['source_w'],
            $geometry['source_h'],
            $geometry['source_x'],
            $geometry['source_y']
        );

        $source->setImagePage(0, 0, 0, 0);

        $source->resizeImage(
            $geometry['dest_w'],
            $geometry['dest_h'],
            \Imagick::FILTER_LANCZOS,
            1
        );

        // Создание холста
        $dest = new \Imagick();

        $dest->newImage($geometry['dest_w'], $geometry['dest_h'], $background);

        // Компоновка
        $dest->compositeImage(
            $source,
            \Imagick::COMPOSITE_OVER,
            $geometry['dest_x'],
            $geometry['dest_y']
        );

        // Обратная гамма-коррекция
        if (!empty($options['gamma_correct'])) {
            $dest->gammaImage(2.2);
        }

        if ($geometry['dest_w'] < $geometry['source_w'] || $geometry['dest_h'] < $geometry['source_h']) {
            $dest->unsharpMaskImage(0, 0.5, 0.5, 0.02);
        }

        // Удаляем EXIF/IPTC/XMP
        $profiles = $this->collectPreservedProfiles($source);
        $this->stripFrame($dest);
        $this->restorePreservedProfiles($dest, $profiles);

        return $dest;
    }

    public function save(string $filename, int $image_type, ?int $quality, array $geometry, array $options) {

        $format = $this->getFormatName($image_type);
        if (!$format) {
            return false;
        }

        $is_dest_animate_format = in_array($image_type, [IMAGETYPE_GIF, IMAGETYPE_WEBP]) && $this->is_source_animate_format;

        $quality = $this->prepareQuality($image_type, $quality);

        $frames = clone $this->image;

        $frames->setFirstIterator();

        $background = $image_type === IMAGETYPE_JPEG ?
                new \ImagickPixel('white') :
                new \ImagickPixel('transparent');

        // Сохраняем параметры анимации
        $iterations = $this->image->getImageIterations();

        $dest_image = new \Imagick();

        foreach ($frames as $frame) {

            if ($is_dest_animate_format) {
                $delay = $frame->getImageDelay();
            }

            $frame = $frame->getImage();

            $dest = $this->renderFrame($frame, $background, $geometry, $options);

            $frame->clear();

            if ($is_dest_animate_format) {

                $dest->setImageDelay($delay);

                $dest->setImageBackgroundColor(new \ImagickPixel('transparent'));
                $dest->setImageDispose(\Imagick::DISPOSE_BACKGROUND);
                $dest->setImagePage($geometry['dest_w'], $geometry['dest_h'], 0, 0);
            }

            $dest_image->addImage($dest);

            $dest->clear();
        }

        $frames->clear();

        $dest_image->setFirstIterator();
        $dest_image->setImageIterations($iterations);

        // Водяной знак
        if (!empty($options['watermark']['file'])) {
            $this->addWatermark(
                $dest_image,
                $options['watermark']['file'],
                $options['watermark']['x'],
                $options['watermark']['y']
            );
        }

        $dest_image->setFirstIterator();

        // Формат и качество
        foreach ($dest_image as $frame) {

            $frame->setImageFormat($format);
            $frame->setImageCompressionQuality($quality);

            $frame->setInterlaceScheme(
                !empty($options['interlace']) ?
                    \Imagick::INTERLACE_PLANE :
                    \Imagick::INTERLACE_NO
            );
        }
        $dest_image->setFirstIterator();
        $dest_image->setFormat($format);
        $dest_image->setCompressionQuality($quality);

        switch ($image_type) {
            case IMAGETYPE_JPEG:
                if ($dest_image->getImageAlphaChannel()) {
                    $dest_image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                }
                $dest_image->setOption('jpeg:sampling-factor', '4:2:0');
                break;
            case IMAGETYPE_WEBP:
                $dest_image->setOption('webp:lossless', 'false');
                $dest_image->setOption('webp:quality', (string) $quality);
                $dest_image->setOption('webp:method', '6');
                $dest_image->setOption('webp:auto-filter', 'true');
                if ($dest_image->getImageAlphaChannel()) {
                    $dest_image->setOption('webp:alpha-quality', '85');
                }
                break;
        }

        // Сохранение
        if ($is_dest_animate_format) {

            if ($image_type === IMAGETYPE_GIF) {
                $dest_image->optimizeImageLayers();
            }

            $success = $dest_image->writeImages($filename, true);

        } else {
            $success = $dest_image->writeImage($filename);
        }

        $dest_image->clear();

        return $success;
    }

    private function addWatermark($dest_image, $filename, $x, $y) {

        $watermark = new \Imagick();

        try {

            if (!$watermark->readImage($filename)) {
                return;
            }

            $watermark->setFirstIterator();

            $wm = $watermark->getImage();

            $wm->setImagePage(0, 0, 0, 0);

            $dest_image->setFirstIterator();

            do {
                $dest_image->compositeImage($wm, \Imagick::COMPOSITE_OVER, $x, $y);

            } while ($dest_image->nextImage());

            $wm->clear();

        } catch (\ImagickException $e) {
        } finally {
            $watermark->clear();
        }
    }

    public function destroy() {

        if ($this->image) {
            $this->image->clear();
            $this->image = null;
        }

        $this->width = 0;
        $this->height = 0;
        $this->is_source_animate_format = false;
    }

}
