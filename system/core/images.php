<?php
/**
 * This library is maintained by Gumlet
 * https://github.com/gumlet/php-image-resize
 * modifed for InstantCMS
 */
class cmsImages {

    const CROPTOP             = 1;
    const CROPCENTRE          = 2;
    const CROPCENTER          = 2;
    const CROPBOTTOM          = 3;
    const CROPLEFT            = 4;
    const CROPRIGHT           = 5;
    const CROPTOPCENTER       = 6;

    public $quality_jpg       = 90;
    public $quality_webp      = 90;
    public $quality_png       = 6;
    public $quality_truecolor = true;
    public $gamma_correct     = false;
    public $interlace         = 1;
    public $gif_to_gif        = false;
    public $source_type;

    protected $source_file_path;
    protected $source_image;
    protected $original_w = 0;
    protected $original_h = 0;
    protected $dest_x     = 0;
    protected $dest_y     = 0;
    protected $source_x   = 0;
    protected $source_y   = 0;
    protected $dest_w     = 0;
    protected $dest_h     = 0;
    protected $source_w   = 0;
    protected $source_h   = 0;
    protected $source_info;
    protected $filters    = [];
    protected $dest_dir = '';

    /**
     * Устанавливает директорию назначения
     * @param string $dest_dir
     * @return $this
     */
    public function setDestinationDir($dest_dir) {

        $this->dest_dir = $dest_dir;

        return $this;
    }

    /**
     * Экземпляр класса из base64 строки изображения
     *
     * @param string $image_data
     * @return \cmsImages
     */
    public static function createFromString($image_data) {
        return new self('data://application/octet-stream;base64,' . base64_encode($image_data));
    }

    /**
     * Выполняет ресайз изображения согласно параметров пресета
     *
     * @param array $preset
     * @param string $file_name
     * @param integer $user_id
     * @return string Полный путь к полученному изображению
     */
    public function resizeByPreset($preset, $file_name = null, $user_id = null) {

        $image_type = $this->source_type;
        $dest_ext   = $this->getSourceExt();

        if($this->dest_dir){
            $dest_dir = $this->dest_dir;
        } else {
            $dest_dir = files_get_upload_dir($user_id === null ? cmsUser::get('id') : $user_id);
        }

        if(!empty($preset['convert_format'])){

            $image_type = $this->getImageType($preset['convert_format']);
            $dest_ext = $preset['convert_format'];

            if(!empty($preset['gif_to_gif']) && $this->source_type == IMAGETYPE_GIF){
                $this->gif_to_gif = true;
                $image_type = $this->source_type;
                $dest_ext   = $this->getSourceExt();
            }
        } else {
            $this->gif_to_gif = true;
        }

        if($file_name){
            $dest_name = files_sanitize_name($file_name.($this->dest_dir ? '' : ' '.$preset['name']));
        } else {
            $dest_name = substr(md5(microtime(true)), 0, 8);
        }

        $dest_file = $dest_dir . $dest_name . '.' .$dest_ext;

        while (file_exists($dest_file)) {
            $dest_file = $dest_dir . $dest_name . '_' . substr(md5(microtime(true)), 0, 2) . '.' .$dest_ext;
        }

        if(!empty($preset['is_square'])){

            $this->crop($preset['width'], $preset['height'], !empty($preset['allow_enlarge']), $preset['crop_position']);

        } else {

            if(!$preset['width'] || !$preset['height']){

                if(!$preset['width']){
                    $this->resizeToHeight($preset['height'], !empty($preset['allow_enlarge']));
                } else {
                    $this->resizeToWidth($preset['width'], !empty($preset['allow_enlarge']));
                }

            } else {
                $this->resizeToBestFit($preset['width'], $preset['height'], !empty($preset['allow_enlarge']));
            }

        }

        if (!empty($preset['is_watermark']) && $preset['wm_image']){

            $this->addFilter(function ($dest_image, $cmsImages) use ($preset) {

                $wm_file = cmsConfig::get('upload_path').$preset['wm_image']['original'];

                $wm_size = getimagesize($wm_file);
                if ($wm_size === false) { return false; }

                $wm_width  = $wm_size[0];
                $wm_height = $wm_size[1];

                $wm_format = strtolower(substr($wm_size['mime'], strpos($wm_size['mime'], '/' ) + 1));
                $wm_func   = 'imagecreatefrom'.$wm_format;
                if (!function_exists($wm_func)) { return false; }

                $wm = $wm_func($wm_file);

                $img_width  = imagesx($dest_image);
                $img_height = imagesy($dest_image);

                $wm_margin = intval($preset['wm_margin']);

                switch($preset['wm_origin']){
                    case 'top-left':
                        $x = $wm_margin;
                        $y = $wm_margin;
                        break;
                    case 'top-center':
                        $x = ($img_width/2) - ($wm_width/2);
                        $y = $wm_margin;
                        break;
                    case 'top-right':
                        $x = ($img_width - $wm_width - $wm_margin);
                        $y = $wm_margin;
                        break;
                    case 'left':
                        $x = $wm_margin;
                        $y = ($img_height/2) - ($wm_height/2);
                        break;
                    case 'center':
                        $x = ($img_width/2) - ($wm_width/2);
                        $y = ($img_height/2) - ($wm_height/2);
                        break;
                    case 'right':
                        $x = ($img_width - $wm_width - $wm_margin);
                        $y = ($img_height/2) - ($wm_height/2);
                        break;
                    case 'bottom-left':
                        $x = $wm_margin;
                        $y = ($img_height - $wm_height - $wm_margin);
                        break;
                    case 'bottom':
                        $x = ($img_width/2) - ($wm_width/2);
                        $y = ($img_height - $wm_height - $wm_margin);
                        break;
                    case 'bottom-right':
                        $x = ($img_width - $wm_width - $wm_margin);
                        $y = ($img_height - $wm_height - $wm_margin);
                        break;
                }

                imagealphablending($dest_image, true);

                imagecopy($dest_image, $wm, (int)$x, (int)$y, 0, 0, $wm_width, $wm_height);

                imagedestroy($wm);
            });

        }

        $this->gamma(!empty($preset['gamma_correct']));

        $this->save($dest_file, $image_type, $preset['quality']);

        $this->filters = [];

        return str_replace(cmsConfig::get('upload_path'), '', $dest_file);
    }

    /**
     * Возвращает расширение исходного файла
     * @return string
     */
    public function getSourceExt() {
        switch ($this->source_type) {
            case IMAGETYPE_GIF:
                return 'gif';
            case IMAGETYPE_JPEG:
                return 'jpg';
            case IMAGETYPE_PNG:
                return 'png';
            case IMAGETYPE_WEBP:
                return 'webp';
            default:
                return '';
        }
    }

    public function getImageType($ext) {
        switch ($ext) {
            case 'gif':
                return IMAGETYPE_GIF;
            case 'jpg':
                return IMAGETYPE_JPEG;
            case 'png':
                return IMAGETYPE_PNG;
            case 'webp':
                return IMAGETYPE_WEBP;
            default:
                return 'jpg';
        }
    }

    /**
     * Добавляет функцию фильтра для использования перед сохранением в файл
     *
     * @param callable $filter
     * @return $this
     */
    public function addFilter(callable $filter) {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Применяет фильтры
     *
     * @param resource $image Идентификатор ресурса изображения
     */
    protected function applyFilter($image) {
        foreach ($this->filters as $function) {
            if(is_callable($function)){
                call_user_func_array($function, [$image, $this]);
            }
        }
    }

    /**
     * Загружает источник изображения и его свойства в экземпляр объекта
     *
     * @param string $filename
     */
    public function __construct($filename) {

        if (!defined('IMAGETYPE_WEBP')) {
            define('IMAGETYPE_WEBP', 18);
        }

        if ($filename === null || empty($filename) || (substr($filename, 0, 5) !== 'data:' && !is_file($filename))) {
            throw new Exception('File does not exist');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (strstr(finfo_file($finfo, $filename), 'image') === false) {
            throw new Exception('Unsupported file type');
        }
        if (!$image_info = getimagesize($filename, $this->source_info)) {
            $image_info = getimagesize($filename);
        }
        if (!$image_info) {
            throw new Exception('Could not read file');
        }

        $this->original_w  = $image_info[0];
        $this->original_h  = $image_info[1];
        $this->source_type = $image_info[2];

        switch ($this->source_type) {
            case IMAGETYPE_GIF:
                $this->source_image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_JPEG:
                $this->source_image = $this->imageCreateJpegfromExif($filename);
                // возможно изменили размеры, переустанавливаем
                $this->original_w = imagesx($this->source_image);
                $this->original_h = imagesy($this->source_image);
                break;
            case IMAGETYPE_PNG:
                $this->source_image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_WEBP:
                $this->source_image = imagecreatefromwebp($filename);
                $this->original_w = imagesx($this->source_image);
                $this->original_h = imagesy($this->source_image);
                break;
            default:
                throw new Exception('Unsupported image type');
        }

        if (!$this->source_image) {
            throw new Exception('Could not load image');
        }

        $this->source_file_path = $filename;

        finfo_close($finfo);

        return $this->resize($this->getSourceWidth(), $this->getSourceHeight());
    }

    public function imageCreateJpegfromExif($filename) {

        $img = imagecreatefromjpeg($filename);

        if (!function_exists('exif_read_data') || !isset($this->source_info['APP1']) || strpos($this->source_info['APP1'], 'Exif') !== 0) {
            return $img;
        }

        try {
            $exif = @exif_read_data($filename);
        } catch (Exception $e) {
            $exif = null;
        }

        if (!$exif || empty($exif['Orientation'])) {
            return $img;
        }

        $orientation = $exif['Orientation'];

        if ($orientation === 6 || $orientation === 5) {
            $img = imagerotate($img, 270, null);
        } elseif ($orientation === 3 || $orientation === 4) {
            $img = imagerotate($img, 180, null);
        } elseif ($orientation === 8 || $orientation === 7) {
            $img = imagerotate($img, 90, null);
        }
        if ($orientation === 5 || $orientation === 4 || $orientation === 7) {
            imageflip($img, IMG_FLIP_HORIZONTAL);
        }

        return $img;
    }

    public function saveGif($filename, $quality = null, $permissions = null, $exact_size = false) {

        $im = new Imagick($this->source_file_path);

        if (!empty($exact_size) && is_array($exact_size)) {

            $width = $exact_size[0];
            $height = $exact_size[1];

        } else {

            $width = $this->getDestWidth();
            $height = $this->getDestHeight();
        }

        $im = $im->coalesceImages();

        foreach ($im as $frame) {

            $frame->cropImage($width, $height, $this->source_x, $this->source_y);

            $frame->thumbnailImage($width, $height);

            $frame->setImagePage($width, $height, 0, 0);
        }

        $im = $im->deconstructImages();

        if ($im->writeImages($filename, true) && $permissions) {
            chmod($filename, $permissions);
        }

        $im->clear();
        $im->destroy();

        return $this;
    }

    /**
     * Сохраняет новое изображение
     *
     * @param string $filename     Имя файла для сохранения
     * @param integer $image_type  Тип изображения
     * @param integer $quality     Качество
     * @param integer $permissions Права доступа
     * @param array $exact_size    Массив размеров
     * @return \cmsImages
     */
    public function save($filename, $image_type = null, $quality = null, $permissions = null, $exact_size = false) {

        if(!$image_type){
            $image_type = $this->source_type;
        }

        $quality = is_numeric($quality) ? abs((int)$quality) : null;

        switch ($image_type) {
            case IMAGETYPE_GIF:

                if ($this->gif_to_gif && extension_loaded('imagick')) {
                    return $this->saveGif($filename, $quality, $permissions, $exact_size);
                }

                if (!empty($exact_size) && is_array($exact_size)) {
                    $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                } else {
                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                }

                $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 1);

                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);
                imagesavealpha($dest_image, true);

                break;

            case IMAGETYPE_JPEG:

                if (!empty($exact_size) && is_array($exact_size)) {

                    $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                    $background = imagecolorallocate($dest_image, 255, 255, 255);

                    imagefilledrectangle($dest_image, 0, 0, $exact_size[0], $exact_size[1], $background);

                } else {

                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                    $background = imagecolorallocate($dest_image, 255, 255, 255);

                    imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);

                }

                break;

            case IMAGETYPE_WEBP:

                if (!empty($exact_size) && is_array($exact_size)) {

                    $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                    $background = imagecolorallocate($dest_image, 255, 255, 255);

                    imagefilledrectangle($dest_image, 0, 0, $exact_size[0], $exact_size[1], $background);

                } else {

                    $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                    $background = imagecolorallocate($dest_image, 255, 255, 255);

                    imagefilledrectangle($dest_image, 0, 0, $this->getDestWidth(), $this->getDestHeight(), $background);

                }

                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);

                break;

            case IMAGETYPE_PNG:

                if (!$this->quality_truecolor || !imageistruecolor($this->source_image)) {

                    if (!empty($exact_size) && is_array($exact_size)) {
                        $dest_image = imagecreate($exact_size[0], $exact_size[1]);
                    } else {
                        $dest_image = imagecreate($this->getDestWidth(), $this->getDestHeight());
                    }

                } else {

                    if (!empty($exact_size) && is_array($exact_size)) {
                        $dest_image = imagecreatetruecolor($exact_size[0], $exact_size[1]);
                    } else {
                        $dest_image = imagecreatetruecolor($this->getDestWidth(), $this->getDestHeight());
                    }

                }

                imagealphablending($dest_image, false);
                imagesavealpha($dest_image, true);

                $background = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);

                imagecolortransparent($dest_image, $background);
                imagefill($dest_image, 0, 0, $background);

                break;
        }

        imageinterlace($dest_image, $this->interlace);

        if ($this->gamma_correct) {
            imagegammacorrect($this->source_image, 2.2, 1.0);
        }

        if (!empty($exact_size) && is_array($exact_size)) {

            if ($this->getSourceHeight() < $this->getSourceWidth()) {

                $this->dest_x = 0;
                $this->dest_y = ($exact_size[1] - $this->getDestHeight()) / 2;
            }
            if ($this->getSourceHeight() > $this->getSourceWidth()) {

                $this->dest_x = ($exact_size[0] - $this->getDestWidth()) / 2;
                $this->dest_y = 0;
            }
        }

        imagecopyresampled(
            $dest_image,
            $this->source_image,
            (int)$this->dest_x,
            (int)$this->dest_y,
            (int)$this->source_x,
            (int)$this->source_y,
            $this->getDestWidth(),
            $this->getDestHeight(),
            (int)$this->source_w,
            (int)$this->source_h
        );

        if ($this->gamma_correct) {
            imagegammacorrect($dest_image, 1.0, 2.2);
        }

        $this->applyFilter($dest_image);

        switch ($image_type) {
            case IMAGETYPE_GIF:

                imagegif($dest_image, $filename);

                break;
            case IMAGETYPE_JPEG:

                if ($quality === null || $quality > 100) {
                    $quality = $this->quality_jpg;
                }

                imagejpeg($dest_image, $filename, $quality);

                break;
            case IMAGETYPE_WEBP:

                if ($quality === null) {
                    $quality = $this->quality_webp;
                }

                imagewebp($dest_image, $filename, $quality);

                break;
            case IMAGETYPE_PNG:

                $quality = (9 - ceil((9*$quality)/100));

                if ($quality === null || $quality > 9) {
                    $quality = $this->quality_png;
                }

                imagepng($dest_image, $filename, $quality);

                break;
        }

        if ($permissions) {
            chmod($filename, $permissions);
        }

        imagedestroy($dest_image);

        return $this;
    }

    /**
     * Преобразовывает изображение в строку
     *
     * @param integer $image_type
     * @param integer $quality
     * @return string
     */
    public function getImageAsString($image_type = null, $quality = null) {

        $string_temp = tempnam(sys_get_temp_dir(), '');

        $this->save($string_temp, $image_type, $quality);

        $string = file_get_contents($string_temp);

        unlink($string_temp);

        return $string;
    }

    /**
     * Преобразовывает изображение в строку с текущими настройками
     *
     * @return string
     */
    public function __toString() {
        return $this->getImageAsString();
    }

    /**
     * Выводит изображение в браузер
     *
     * @param string $image_type
     * @param integer $quality
     */
    public function output($image_type = null, $quality = null) {

        if(!$image_type){
            $image_type = $this->source_type;
        }

        header('Content-Type: ' . image_type_to_mime_type($image_type));

        $this->save(null, $image_type, $quality);
    }

    /**
     * Изменяет размер изображения в соответствии с заданной короткой стороной (пропорциональная длинная сторона)
     *
     * @param integer $max_short
     * @param boolean $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToShortSide($max_short, $allow_enlarge = false) {

        if ($this->getSourceHeight() < $this->getSourceWidth()) {

            $ratio = $max_short / $this->getSourceHeight();
            $long = (int) ($this->getSourceWidth() * $ratio);

            return $this->resize($long, $max_short, $allow_enlarge);

        } else {

            $ratio = $max_short / $this->getSourceWidth();
            $long = (int) ($this->getSourceHeight() * $ratio);

            return $this->resize($max_short, $long, $allow_enlarge);
        }

    }

    /**
     * Изменяет размер изображения в соответствии
     * с заданной длинной стороной (пропорциональная короткая сторона)
     *
     * @param integer $max_long
     * @param boolean $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToLongSide($max_long, $allow_enlarge = false) {

        if ($this->getSourceHeight() > $this->getSourceWidth()) {

            $ratio = $max_long / $this->getSourceHeight();
            $short = (int) ($this->getSourceWidth() * $ratio);

            return $this->resize($short, $max_long, $allow_enlarge);

        } else {

            $ratio = $max_long / $this->getSourceWidth();
            $short = (int) ($this->getSourceHeight() * $ratio);

            return $this->resize($max_long, $short, $allow_enlarge);
        }

    }

    /**
     * Изменяет размер изображения в соответствии с заданной высотой (ширина пропорциональна)
     *
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToHeight($height, $allow_enlarge = false) {

        $ratio = $height / $this->getSourceHeight();
        $width = (int) ($this->getSourceWidth() * $ratio);

        return $this->resize($width, $height, $allow_enlarge);
    }

    /**
     * Изменяет размер изображения в соответствии с заданной шириной (высота пропорциональна)
     *
     * @param integer $width
     * @param boolean $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToWidth($width, $allow_enlarge = false) {

        $ratio  = $width / $this->getSourceWidth();
        $height = (int) ($this->getSourceHeight() * $ratio);

        return $this->resize($width, $height, $allow_enlarge);
    }

    /**
     * Изменяет размер изображения, чтобы оно лучше подходило к заданным размерам
     *
     * @param integer $max_width
     * @param integer $max_height
     * @param boolean $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToBestFit($max_width, $max_height, $allow_enlarge = false) {

        if ($this->getSourceWidth() <= $max_width && $this->getSourceHeight() <= $max_height && !$allow_enlarge) {
            return $this;
        }

        $ratio  = $this->getSourceHeight() / $this->getSourceWidth();
        $width = $max_width;
        $height = (int) ($width * $ratio);

        if ($height > $max_height) {
            $height = $max_height;
            $width = (int) ($height / $ratio);
        }

        return $this->resize($width, $height, $allow_enlarge);
    }

    /**
     * Изменяет размер изображения в соответствии с заданным масштабом (пропорционально)
     *
     * @param integer|float $scale
     * @return \cmsImages
     */
    public function scale($scale) {

        $width  = (int) ($this->getSourceWidth() * $scale / 100);
        $height = (int) ($this->getSourceHeight() * $scale / 100);

        return $this->resize($width, $height, true);
    }

    /**
     * Изменяет размер изображения в соответствии с заданной шириной и высотой
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return \cmsImages
     */
    public function resize($width, $height, $allow_enlarge = false) {

        if (!$allow_enlarge) {
            // сброс размеров к оригиналу
            // если требуемые значения больше оригинала
            if ($width > $this->getSourceWidth() || $height > $this->getSourceHeight()) {
                $width  = $this->getSourceWidth();
                $height = $this->getSourceHeight();
            }
        }

        $this->source_x = 0;
        $this->source_y = 0;
        $this->dest_w   = round($width);
        $this->dest_h   = round($height);
        $this->source_w = $this->getSourceWidth();
        $this->source_h = $this->getSourceHeight();

        return $this;
    }

    /**
     * Обрезает изображение в соответствии с заданной шириной, высотой и положением обрезки
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @param integer $position
     * @return \cmsImages
     */
    public function crop($width, $height, $allow_enlarge = false, $position = self::CROPCENTER) {

        if (!$allow_enlarge) {
            // эта логика немного отличается от resize()
            // сброс размеров к оригиналу
            // если требуемые значения больше оригинала
            if ($width > $this->getSourceWidth()) {
                $width = $this->getSourceWidth();
            }
            if ($height > $this->getSourceHeight()) {
                $height = $this->getSourceHeight();
            }
        }

        $ratio_source = $this->getSourceWidth() / $this->getSourceHeight();
        $ratio_dest   = $width / $height;

        if ($ratio_dest < $ratio_source) {

            $this->resizeToHeight($height, $allow_enlarge);

            $excess_width = (int) (($this->getDestWidth() - $width) * $this->getSourceWidth() / $this->getDestWidth());

            $this->source_w = $this->getSourceWidth() - $excess_width;
            $this->source_x = $this->getCropPosition($excess_width, $position);
            $this->dest_w   = $width;

        } else {

            $this->resizeToWidth($width, $allow_enlarge);

            $excess_height = (int) (($this->getDestHeight() - $height) * $this->getSourceHeight() / $this->getDestHeight());

            $this->source_h = $this->getSourceHeight() - $excess_height;
            $this->source_y = $this->getCropPosition($excess_height, $position);
            $this->dest_h   = $height;
        }

        return $this;
    }

    /**
     * Обрезать изображение в соответствии с заданной шириной, высотой, координатами х и у
     *
     * @param integer $width
     * @param integer $height
     * @param integer $x
     * @param integer $y
     * @return \cmsImages
     */
    public function freecrop($width, $height, $x = false, $y = false) {

        if ($x === false || $y === false) {
            return $this->crop($width, $height);
        }

        $this->source_x = $x;
        $this->source_y = $y;

        if ($width > $this->getSourceWidth() - $x) {
            $this->source_w = $this->getSourceWidth() - $x;
        } else {
            $this->source_w = $width;
        }

        if ($height > $this->getSourceHeight() - $y) {
            $this->source_h = $this->getSourceHeight() - $y;
        } else {
            $this->source_h = $height;
        }

        $this->dest_w = $width;
        $this->dest_h = $height;

        return $this;
    }

    /**
     * Получает ширину источника
     *
     * @return integer
     */
    public function getSourceWidth() {
        return $this->original_w;
    }

    /**
     * Получает высоту источника
     *
     * @return integer
     */
    public function getSourceHeight() {
        return $this->original_h;
    }

    /**
     * Получает ширину целевого изображения
     *
     * @return integer
     */
    public function getDestWidth() {
        return $this->dest_w;
    }

    /**
     * Получает высоту целевого изображения
     * @return integer
     */
    public function getDestHeight() {
        return $this->dest_h;
    }

    /**
     * Получает позицию обрезки (X или Y) в соответствии с заданной позицией
     *
     * @param integer $expected_size
     * @param integer $position
     * @return float|integer
     */
    protected function getCropPosition($expected_size, $position = self::CROPCENTER) {
        $size = 0;
        switch ($position) {
            case self::CROPBOTTOM:
            case self::CROPRIGHT:
                $size = $expected_size;
                break;
            case self::CROPCENTER:
                $size = $expected_size / 2;
                break;
            case self::CROPTOPCENTER:
                $size = $expected_size / 4;
                break;
        }
        return $size;
    }

    /**
     * Включить/выключить коррекцию гамма-цвета на изображении, выключено по умолчанию
     *
     * @param bool $enable
     * @return \cmsImages
     */
    public function gamma($enable = false) {
        $this->gamma_correct = $enable;
        return $this;
    }

}
