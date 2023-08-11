<?php

class cmsUploadfile {

    private $file_mime;
    private $mime_types;
    private $allowed_mime;

    public function __construct($file_path, $allowed_mime = null) {

        $this->allowed_mime = $allowed_mime;

        $this->mime_types = (new cmsConfigs('mimetypes.php'))->getAll();

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if(strpos($file_path, DIRECTORY_SEPARATOR) === 0){

            $this->file_mime = finfo_file($finfo, $file_path);

        } else {

            $this->file_mime = finfo_buffer($finfo, $file_path);
        }

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
