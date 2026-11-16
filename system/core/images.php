<?php

class cmsImages {

    const CROPTOP       = 1;
    const CROPCENTRE    = 2;
    const CROPCENTER    = 2;
    const CROPBOTTOM    = 3;
    const CROPLEFT      = 4;
    const CROPRIGHT     = 5;
    const CROPTOPCENTER = 6;

    public $gamma_correct = false;
    public $interlace     = true;

    public $source_type = IMAGETYPE_UNKNOWN;

    public $max_source_pixels = 28000000;

    /**
     * Конвертер
     * auto = сначала Imagick, затем GD в качестве резервного варианта
     * Можно явно указать gd или imagick.
     *
     * @var string
     */
    protected $engine = 'auto';

    /**
     * Драйвер обработки изображений
     *
     * @var imagesDriverInterface
     */
    protected $driver;

    /**
     * Массив данных для ватермарка
     *
     * @var ?array
     */
    protected $watermark = null;

    protected $source_file_path;
    protected $dest_dir = '';

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

    protected $last_error;

    /**
     * Устанавливает директорию назначения
     *
     * @param string $dest_dir
     * @return \cmsImages
     */
    public function setDestinationDir($dest_dir) {

        $this->dest_dir = $dest_dir;

        return $this;
    }

    /**
     * Подготавливает данные для конвертации
     * И возвращает [$image_type, $dest_file]
     * Где $image_type это IMAGETYPE_GIF || IMAGETYPE_JPEG || IMAGETYPE_PNG || IMAGETYPE_WEBP
     *     $dest_file путь к файлу результата
     *
     * @param array $preset
     * @param ?string $file_name
     * @param ?string $user_id
     * @return array
     */
    protected function prepareResizeByPreset(array $preset, $file_name, $user_id) {

        $image_type = $this->source_type;
        $dest_ext   = $this->getSourceExt();

        if($this->dest_dir) {
            $dest_dir = $this->dest_dir;
        } else {
            $dest_dir = files_get_upload_dir($user_id ?? cmsUser::get('id'));
            if (!$dest_dir) {
                return [$image_type, null];
            }
        }

        if(!empty($preset['convert_format'])) {

            $image_type = $this->getImageType($preset['convert_format']);
            $dest_ext = $preset['convert_format'];

            if(!empty($preset['gif_to_gif']) && $this->driver->isSourceAnimate()) {
                $image_type = $this->source_type;
                $dest_ext   = $this->getSourceExt();
            }
        }

        if($file_name) {
            // Если только ASCII-символы, не транслитерировать
            $convert_slug = !preg_match('/^[\x00-\x7F]+$/', $file_name);

            $dest_name = files_sanitize_name($file_name.($this->dest_dir ? '' : ' '.$preset['name']), $convert_slug);
        } else {
            $dest_name = string_random(8);
        }

        $dest_file = $dest_dir . $dest_name . '.' .$dest_ext;

        while (file_exists($dest_file)) {
            $dest_file = $dest_dir . $dest_name . '_' . substr(md5(microtime(true)), 0, 2) . '.' .$dest_ext;
        }

        return [$image_type, $dest_file];
    }

    /**
     * Выполняет ресайз изображения согласно параметров пресета
     *
     * @param array $preset
     * @param ?string $file_name
     * @param ?string|int $user_id
     * @return ?string Полный путь к полученному изображению
     */
    public function resizeByPreset(array $preset, $file_name = null, $user_id = null) {

        try {
            $this->setEngine($preset['engine']);
        } catch (Exception $exc) {
            $this->last_error = $exc->getMessage();
            return null;
        }

        [$image_type, $dest_file] = $this->prepareResizeByPreset($preset, $file_name, $user_id);

        if (!$dest_file || $image_type === IMAGETYPE_UNKNOWN) {
            return null;
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

        $this->preparePresetWatermark($preset);

        $this->gamma(!empty($preset['gamma_correct']));

        $success = $this->save($dest_file, $image_type, $preset['quality']);

        if (!$success) {
            return null;
        }

        return str_replace(cmsConfig::get('upload_path'), '', $dest_file);
    }

    /**
     * Подготавливает данные для нанесения ватермарка, если требуется
     *
     * @param array $preset
     * @return void
     */
    protected function preparePresetWatermark(array $preset) {

        if (empty($preset['is_watermark']) || empty($preset['wm_image']['original'])) {
            return;
        }

        $wm_file = cmsConfig::get('upload_path') . $preset['wm_image']['original'];

        $wm_size = getimagesize($wm_file);
        if ($wm_size === false) {
            return;
        }

        $wmW = (int) $wm_size[0];
        $wmH = (int) $wm_size[1];

        $imgW   = $this->getDestWidth();
        $imgH   = $this->getDestHeight();
        $margin = (int) ($preset['wm_margin'] ?? 0);

        switch($preset['wm_origin'] ?? 'bottom-right') {
            case 'top-left':    $x = $margin;
                                $y = $margin;
                break;
            case 'top-center':  $x = ($imgW/2) - ($wmW/2);
                                $y = $margin;
                break;
            case 'top-right':   $x = ($imgW - $wmW - $margin);
                                $y = $margin;
                break;
            case 'left':        $x = $margin;
                                $y = ($imgH/2) - ($wmH/2);
                break;
            case 'center':      $x = ($imgW/2) - ($wmW/2);
                                $y = ($imgH/2) - ($wmH/2);
                break;
            case 'right':       $x = ($imgW - $wmW - $margin);
                                $y = ($imgH/2) - ($wmH/2);
                break;
            case 'bottom-left': $x = $margin;
                                $y = ($imgH - $wmH - $margin);
                break;
            case 'bottom':      $x = ($imgW/2) - ($wmW/2);
                                $y = ($imgH - $wmH - $margin);
                break;
            default: // bottom-right
                                $x = ($imgW - $wmW - $margin);
                                $y = ($imgH - $wmH - $margin);
                break;
        };

        $this->watermark = ['file' => $wm_file, 'x' => (int) $x, 'y' => (int) $y];
    }

    /**
     * Возвращает расширение исходного файла
     *
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

    /**
     * Возвращает тип файла по расширению
     *
     * @param string $ext Расширение файла
     * @return int
     */
    protected function getImageType(string $ext) {
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
                return IMAGETYPE_UNKNOWN;
        }
    }

    /**
     * Возвращает объект класса драйвера
     *
     * @return icms\core\imageDrivers\imagesDriverInterface
     * @throws Exception
     */
    protected function loadDriver() {

        if ($this->engine === 'auto') {
            $this->engine = extension_loaded('imagick') ? 'imagick' : 'gd';
        }

        $driver_class = 'icms\core\imageDrivers\\' . $this->engine;

        if (!class_exists($driver_class)) {
            throw new Exception($this->engine . ' extension is not available');
        }

        return new $driver_class();
    }

    /**
     * Возвращает информацию об изображении через getimagesize
     * Проверяя перед этим через finfo
     *
     * @param string $filename Полный путь к файлу
     * @return bool|array
     */
    protected function getImageInfo(string $filename) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $filename) : false;
        if ($finfo) { finfo_close($finfo); }

        if (!$mime || strpos($mime, 'image/') !== 0) {
            return false;
        }

        return getimagesize($filename);
    }

    /**
     * Загружает драйвер обработки
     * И устанавливает параметры текущего исходного изображения
     *
     * @param string $engine Имя драйвера
     * @return \cmsImages
     * @throws Exception
     */
    protected function setEngine(string $engine) {

        if ($this->driver) {
            $this->driver->destroy();
        }

        $this->engine = strtolower($engine);

        $this->driver = $this->loadDriver();

        $this->driver->load($this->source_file_path, $this->source_type);

        $this->original_w = $this->driver->getWidth();
        $this->original_h = $this->driver->getHeight();

        return $this->resize($this->getSourceWidth(), $this->getSourceHeight());
    }

    /**
     * Загружает источник изображения и его свойства в экземпляр объекта
     *
     * @param string $filename
     * @param string $engine
     * @throws Exception
     */
    public function __construct(string $filename, ?string $engine = null) {

        if (empty($filename) || !is_file($filename)) {
            throw new Exception(ERR_FILE_NOT_FOUND);
        }

        $image_info = $this->getImageInfo($filename);
        if (!$image_info) {
            throw new Exception(LANG_UPLOAD_ERR_MIME);
        }

        // Защита от атак типа image-bomb
        if ($this->max_source_pixels > 0 && ($image_info[0] * $image_info[1]) > $this->max_source_pixels) {
            throw new Exception(
                sprintf(
                    LANG_ERROR_IMAGE_PIXELS,
                    $image_info[0],
                    $image_info[1],
                    $image_info[0] * $image_info[1],
                    $this->max_source_pixels
                )
            );
        }

        $this->source_file_path = $filename;

        $this->source_type = (int) $image_info[2];

        if (!in_array($this->source_type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            throw new Exception(LANG_UPLOAD_ERR_MIME);
        }

        if ($engine !== null) {
            $this->setEngine($engine);
        }
    }

    public function reset() {

        $this->gamma_correct = false;
        $this->watermark     = null;

        return $this;
    }

    /**
     * Сохраняет новое изображение
     *
     * @param string $filename  Имя файла для сохранения
     * @param ?int $image_type  Тип изображения
     * @param ?int $quality     Качество
     * @param ?int $permissions Права доступа
     * @return bool
     */
    public function save(string $filename, $image_type = null, $quality = null, $permissions = null) {

        if (!$image_type) {
            $image_type = $this->source_type;
        }

        $quality = is_numeric($quality) ? abs((int)$quality) : null;

        $this->dest_x = 0;
        $this->dest_y = 0;

        $geometry = [
            'source_x' => $this->source_x,
            'source_y' => $this->source_y,
            'source_w' => $this->source_w,
            'source_h' => $this->source_h,
            'dest_x'   => $this->dest_x,
            'dest_y'   => $this->dest_y,
            'dest_w'   => $this->getDestWidth(),
            'dest_h'   => $this->getDestHeight()
        ];

        $options = [
            'gamma_correct' => $this->gamma_correct,
            'interlace'     => $this->interlace && $image_type === IMAGETYPE_JPEG,
            'watermark'     => $this->watermark
        ];

        $success = $this->driver->save($filename, $image_type, $quality, $geometry, $options);

        $this->reset();

        if ($permissions) {
            chmod($filename, $permissions);
        }

        return $success;
    }

    /**
     * Изменяет размер изображения в соответствии с заданной высотой (ширина пропорциональна)
     *
     * @param int $height
     * @param bool $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToHeight(int $height, $allow_enlarge = false) {

        $ratio = $height / $this->getSourceHeight();
        $width = (int) ($this->getSourceWidth() * $ratio);

        return $this->resize($width, $height, $allow_enlarge);
    }

    /**
     * Изменяет размер изображения в соответствии с заданной шириной (высота пропорциональна)
     *
     * @param int $width
     * @param bool $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToWidth(int $width, $allow_enlarge = false) {

        $ratio  = $width / $this->getSourceWidth();
        $height = (int) ($this->getSourceHeight() * $ratio);

        return $this->resize($width, $height, $allow_enlarge);
    }

    /**
     * Изменяет размер изображения, чтобы оно лучше подходило к заданным размерам
     *
     * @param int $max_width
     * @param int $max_height
     * @param bool $allow_enlarge
     * @return \cmsImages
     */
    public function resizeToBestFit(int $max_width, int $max_height, $allow_enlarge = false) {

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
     * Изменяет размер изображения в соответствии с заданной шириной и высотой
     *
     * @param int $width
     * @param int $height
     * @param bool $allow_enlarge
     * @return \cmsImages
     */
    public function resize(int $width, int $height, $allow_enlarge = false) {

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
        $this->dest_w   = $width;
        $this->dest_h   = $height;
        $this->source_w = $this->getSourceWidth();
        $this->source_h = $this->getSourceHeight();

        return $this;
    }

    /**
     * Обрезает изображение в соответствии с заданной шириной, высотой и положением обрезки
     *
     * @param int $width
     * @param int $height
     * @param bool $allow_enlarge
     * @param int $position
     * @return \cmsImages
     */
    public function crop(int $width, int $height, bool $allow_enlarge = false, int $position = self::CROPCENTER) {

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
     * Возвращает ширину источника
     *
     * @return int
     */
    public function getSourceWidth() {
        return $this->original_w;
    }

    /**
     * Возвращает высоту источника
     *
     * @return integer
     */
    public function getSourceHeight() {
        return $this->original_h;
    }

    /**
     * Возвращает ширину целевого изображения
     *
     * @return int
     */
    public function getDestWidth() {
        return $this->dest_w;
    }

    /**
     * Возвращает высоту целевого изображения
     *
     * @return int
     */
    public function getDestHeight() {
        return $this->dest_h;
    }

    /**
     * Возвращает позицию обрезки (X или Y) в соответствии с заданной позицией
     *
     * @param int $expected_size
     * @param int $position
     * @return int
     */
    protected function getCropPosition(int $expected_size, int $position = self::CROPCENTER) {
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
        return (int) $size;
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

    /**
     * Возвращает последнюю ошибку
     *
     * @return string
     */
    public function getLastError() {
        return $this->last_error;
    }

    public function __destruct() {
        if ($this->driver) {
            $this->driver->destroy();
        }
    }

}
