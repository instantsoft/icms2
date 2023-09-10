<?php

class cmsUploadfile {

    private $file_mime;
    private $mime_types;
    private $allowed_mime;

    public static function fromPath($file_path, $allowed_mime = null) {

        $self = new self($allowed_mime);

        $self->loadMimeFromPath($file_path);

        return $self;
    }

    public static function fromString($file_str, $allowed_mime = null) {

        $self = new self($allowed_mime);

        $self->loadMimeFromString($file_str);

        return $self;
    }

    public function __construct($allowed_mime = null) {

        $this->allowed_mime = $allowed_mime;

        $this->mime_types = (new cmsConfigs('mimetypes.php'))->getAll();
    }

    public function loadMimeFromPath($file_path) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $this->file_mime = finfo_file($finfo, $file_path);

        finfo_close($finfo);
    }

    public function loadMimeFromString($file_str) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $this->file_mime = finfo_buffer($finfo, $file_str);

        finfo_close($finfo);
    }

    public function getMime() {
        return $this->file_mime;
    }

    public function getExt() {

        if ($this->file_mime && isset($this->mime_types[$this->file_mime])) {

            return $this->mime_types[$this->file_mime];
        }

        return 'bin';
    }

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
