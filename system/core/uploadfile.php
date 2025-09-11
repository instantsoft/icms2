<?php

class cmsUploadfile {

    /**
     * Mime Type файла
     *
     * @var string|false
     */
    private $file_mime = false;

    /**
     * Расширение файла
     *
     * @var string
     */
    private $file_ext = '???'; // FILEINFO_EXTENSION

    /**
     * Объект Mime Types
     *
     * @var cmsMimetypes
     */
    private $mime;

    /**
     * Разрешённые Mime Types
     *
     * @var ?array
     */
    private $allowed_mime = null;

    /**
     * Разрешённые расширения файлов согласно Mime Types
     *
     * @var array
     */
    private $allowed_exts = [];

    /**
     * Возвращает текущий объект по пути к файлу
     *
     * @param string $file_path Полный путь к файлу
     * @param ?array $allowed_mime Разрешённые типы
     * @param ?array $allowed_exts Разрешённые расширения
     * @return \self
     */
    public static function fromPath($file_path, $allowed_mime = null, $allowed_exts = null) {

        $self = new self($allowed_mime, $allowed_exts);

        $self->loadMimeFromPath($file_path);

        return $self;
    }

    /**
     * Возвращает текущий объект по переданным бинарным данным
     *
     * @param string $file_str Бинарные данные файла
     * @param ?array $allowed_mime Разрешённые типы
     * @param ?array $allowed_exts Разрешённые расширения
     * @return \self
     */
    public static function fromString($file_str, $allowed_mime = null, $allowed_exts = null) {

        $self = new self($allowed_mime, $allowed_exts);

        $self->loadMimeFromString($file_str);

        return $self;
    }

    public function __construct($allowed_mime = null, $allowed_exts = null) {

        $this->mime = new cmsMimetypes();

        if ($allowed_mime) {
            $this->setAllowedMime($allowed_mime);
        }

        if ($allowed_exts) {
            $this->setAllowedExtensions($allowed_exts);
        }
    }

    /**
     * Устанавливает разрешённые типы файлов
     *
     * @param array $allowed_mime
     * @return $this
     */
    public function setAllowedMime(array $allowed_mime) {

        $this->allowed_mime = $allowed_mime;

        foreach ($this->allowed_mime as $mime_type) {

            $exts = $this->mime->getExtensions($mime_type);

            if ($exts) {
                $this->allowed_exts = array_merge($this->allowed_exts, $exts);
            }
        }

        $this->allowed_exts = array_unique($this->allowed_exts);

        return $this;
    }

    /**
     * Устанавливает разрешённые типы файлов по их расширению
     * Перезаписывает установленное setAllowedMime
     *
     * @param array|string $allowed_ext
     * @return $this
     */
    public function setAllowedExtensions($allowed_ext) {

        $this->allowed_mime = [];

        if (!is_array($allowed_ext)) {
            $allowed_ext = explode(',', (string) $allowed_ext);
        }

        foreach ($allowed_ext as $aext) {

            $aext = strtolower(trim(trim((string) $aext, '., ')));

            if (!$aext) {
                continue;
            }

            $this->allowed_exts[] = $aext;

            $mime_types = $this->mime->getMimeTypes($aext);

            if ($mime_types) {
                $this->allowed_mime = array_merge($this->allowed_mime, $mime_types);
            }
        }

        $this->allowed_exts = array_unique($this->allowed_exts);
        $this->allowed_mime = array_unique($this->allowed_mime);

        // Если не нашли соответствий
        if (!$this->allowed_mime) {
            $this->allowed_exts = [];
        }

        return $this;
    }

    /**
     * Устанавливает Mime Type текущего файла
     *
     * @param string $file_path Полный путь к файлу
     */
    public function loadMimeFromPath($file_path) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $this->file_mime = finfo_file($finfo, $file_path);

        finfo_close($finfo);

        $this->loadExtFromMime();
    }

    /**
     * Устанавливает Mime Type текущего файла
     *
     * @param string $file_str Бинарные данные файла
     */
    public function loadMimeFromString($file_str) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $this->file_mime = finfo_buffer($finfo, $file_str);

        finfo_close($finfo);

        $this->loadExtFromMime();
    }

    /**
     * Устанавливает расширение текущего файла по его Mime Type
     */
    private function loadExtFromMime() {

        $ext = $this->mime->getExtensions($this->file_mime);

        if ($ext) {
            $this->file_ext = $ext[0]; // Берём первый из списка
        }
    }

    /**
     * Возвращает Mime Type текущего файла
     *
     * @return string
     */
    public function getMime() {
        return $this->file_mime;
    }

    /**
     * Возвращает расширение текущего файла
     *
     * @return string
     */
    public function getExt() {
        return $this->file_ext;
    }

    /**
     * Возвращает разрешённые Mime Type
     *
     * @return array
     */
    public function getAllowedMime() {
        return $this->allowed_mime;
    }

    /**
     * Возвращает разрешённые расширения файлов
     *
     * @return array
     */
    public function getAllowedExtensions() {
        return $this->allowed_exts;
    }

    /**
     * Проверяет что файл разрешён
     *
     * @return bool
     */
    public function isAllowed() {

        // Пускаем любые, если не указано
        if ($this->allowed_mime === null) {
            return true;
        }

        if ($this->file_mime === false) {
            return false;
        }

        return in_array($this->file_mime, $this->allowed_mime, true);
    }

}
