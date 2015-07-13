<?php

class cmsUploader {

    public function __construct(){ }

//============================================================================//
//============================================================================//

    /**
     * Возвращает строку с максимальный размером загружаемых файлов,
     * установленным в php.ini
     * @return string
     */
    public function getMaxUploadSize(){
        $max_size = ini_get('upload_max_filesize');
        $max_size = str_replace('M', 'Мb', $max_size);
        $max_size = str_replace('K', 'Kb', $max_size);
        return $max_size;
    }

    public function isUploaded($name){
        if (!isset($_FILES[$name])) { return false; }
        if (!$_FILES[$name]['size']) { return false; }
        return true;
    }

    public function isUploadedXHR($name){
        return isset($_GET['qqfile']);
    }

//============================================================================//
//============================================================================//

    public function resizeImage($source_file, $size){

        $cfg = cmsConfig::getInstance();
        $user = cmsUser::getInstance();

        $dest_dir   = $this->getUploadDestinationDirectory();

        $dest_info  = pathinfo($source_file);
        $dest_ext   = $dest_info['extension'];
        $dest_file  = substr(md5( $user->id . $user->files_count . microtime(true) . $size['width'] ), 0, 8) . '.' . $dest_ext;
        $dest_file  = $dest_dir . '/' . $dest_file;

        $user->increaseFilesCount();

        if (!isset($size['height'])) { $size['height'] = $size['width']; }

        if ($this->imageCopyResized($source_file, $dest_file, $size['width'], $size['height'], $size['square'])) {

            $url = str_replace($cfg->upload_path, '', $dest_file);

            return $url;

        }

        return false;

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает файл на сервер
     * @param string $post_filename Название поля с файлом в массиве $_FILES
     * @param string $allowed_ext Список допустимых расширений (через запятую)
     * @param string $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Папка назначения (внутри пути upload)
     * @return array
     */
    public function upload($post_filename, $allowed_ext = false, $allowed_size = 0, $destination = false){

        if ($this->isUploadedXHR($post_filename)){
            return $this->uploadXHR($post_filename, $allowed_ext, $allowed_size, $destination);
        }

        if ($this->isUploaded($post_filename)){
            return $this->uploadForm($post_filename, $allowed_ext, $allowed_size, $destination);
        }

        return array(
            'success' => false,
            'error' => LANG_UPLOAD_ERR_NO_FILE
        );

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает файл на сервер переданный через input типа file
     * @param string $post_filename Название поля с файлом в массиве $_FILES
     * @param string $allowed_ext Список допустимых расширений (через запятую)
     * @param string $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Папка назначения (внутри пути upload)
     * @return array
     */
    public function uploadForm($post_filename, $allowed_ext = false, $allowed_size = 0, $destination = false){

        $config = cmsConfig::getInstance();
        $user = cmsUser::getInstance();

        $source     = $_FILES[$post_filename]['tmp_name'];
        $error_code = $_FILES[$post_filename]['error'];

        $dest_size  = $_FILES[$post_filename]['size'];
        $dest_name  = basename(files_sanitize_name($_FILES[$post_filename]['name']));
        $dest_ext   = mb_strtolower(pathinfo($dest_name, PATHINFO_EXTENSION));

        if ($allowed_ext !== false){
            $allowed_ext = explode(",", $allowed_ext);
            foreach($allowed_ext as $idx=>$ext){ $allowed_ext[$idx] = mb_strtolower(trim(trim($ext, '., '))); }
            if (!in_array($dest_ext, $allowed_ext)){
                return array(
                    'error' => LANG_UPLOAD_ERR_MIME,
                    'success' => false,
                    'name' => $dest_name
                );
            }
        }

        if ($allowed_size){
            if ($dest_size > $allowed_size){
                return array(
                    'error' => sprintf(LANG_UPLOAD_ERR_INI_SIZE, files_format_bytes($allowed_size)),
                    'success' => false,
                    'name' => $dest_name
                );
            }
        }

        if (!$destination){

            $user->increaseFilesCount();
            $dest_dir = $this->getUploadDestinationDirectory();
            $dest_file = substr(md5( $user->id . $user->files_count . microtime(true) ), 0, 8) . '.' . $dest_ext;
            $destination = $dest_dir . '/' . $dest_file;

        } else {

            $destination = $config->upload_path . $destination . '/' . $dest_name;

        }

        return $this->moveUploadedFile($source, $destination, $error_code, $dest_name, $dest_size);

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает файл на сервер переданный через XHR
     * @param string $post_filename Название поля с файлом в массиве $_GET
     * @param string $allowed_ext Список допустимых расширений (через запятую)
     * @param string $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Папка назначения (внутри пути upload)
     * @return array
     */
    public function uploadXHR($post_filename, $allowed_ext = false, $allowed_size = 0, $destination = false){

        $cfg = cmsConfig::getInstance();
        $user = cmsUser::getInstance();

        $dest_size  = 10; //$this->getXHRFileSize();

        if (!$dest_size){
            return array(
                'success' => false,
                'error' => LANG_UPLOAD_ERR_NO_FILE
            );
        }

        $dest_name  = files_sanitize_name($_GET['qqfile']);
        $dest_info  = pathinfo($dest_name);
        $dest_ext   = $dest_info['extension'];

        if ($allowed_ext !== false){
            $allowed_ext = explode(",", $allowed_ext);
            foreach($allowed_ext as $idx=>$ext){ $allowed_ext[$idx] = trim($ext); }
            if (!in_array($dest_ext, $allowed_ext)){
                return array(
                    'error' => LANG_UPLOAD_ERR_MIME,
                    'success' => false,
                    'name' => $dest_name
                );
            }
        }

        if (!$destination){

            $user->increaseFilesCount();
            $dest_dir = $this->getUploadDestinationDirectory();
            $dest_file = substr(md5( $user->id . $user->files_count . microtime(true) ), 0, 8) . '.' . $dest_ext;
            $destination = $dest_dir . '/' . $dest_file;

        } else {

            $destination = $cfg->upload_path . $destination . '/' . $dest_file;

        }

        return $this->saveXHRFile($destination, $dest_name, $dest_size);

    }

    public function getXHRFileSize(){
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            return false;
        }
    }

//============================================================================//
//============================================================================//

    public function saveXHRFile($destination, $orig_name='', $orig_size=0){

        $cfg = cmsConfig::getInstance();

        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getXHRFileSize()){
            return array(
                'success' => false,
                'error' => LANG_UPLOAD_ERR_PARTIAL,
                'name' => $orig_name,
                'path' => ''
            );
        }

        $target = fopen($destination, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return array(
            'success' => true,
            'path'  => $destination,
            'url' => str_replace($cfg->upload_path, '', $destination),
            'name' => $orig_name,
            'size' => $orig_size
        );

    }

//============================================================================//
//============================================================================//

    /**
     * Копирует файл из временной папки в целевую и отслеживает ошибки
     * @param string $source
     * @param string $destination
     * @param int $errorCode
     * @return bool
     */
    private function moveUploadedFile($source, $destination, $errorCode, $orig_name='', $orig_size=0){

        $cfg = cmsConfig::getInstance();

        $max_size = $this->getMaxUploadSize();

        // Возможные ошибки
        $uploadErrors = array(
            UPLOAD_ERR_OK => LANG_UPLOAD_ERR_OK,
            UPLOAD_ERR_INI_SIZE => sprintf(LANG_UPLOAD_ERR_INI_SIZE, $max_size),
            UPLOAD_ERR_FORM_SIZE => LANG_UPLOAD_ERR_FORM_SIZE,
            UPLOAD_ERR_PARTIAL => LANG_UPLOAD_ERR_PARTIAL,
            UPLOAD_ERR_NO_FILE => LANG_UPLOAD_ERR_NO_FILE,
            UPLOAD_ERR_NO_TMP_DIR => LANG_UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE => LANG_UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION => LANG_UPLOAD_ERR_EXTENSION
        );

        if($errorCode !== UPLOAD_ERR_OK && isset($uploadErrors[$errorCode])){

            return array(
                'success' => false,
                'error' => $uploadErrors[$errorCode],
                'name' => $orig_name,
                'path' => ''
            );

        }

        $upload_dir = dirname($destination);
        if (!is_writable($upload_dir)){	@chmod($upload_dir, 0755); }

        return array(
            'success' => @move_uploaded_file($source, $destination),
            'path'  => $destination,
            'url' => str_replace($cfg->upload_path, '', $destination),
            'name' => $orig_name,
            'size' => $orig_size,
            'error' => $uploadErrors[$errorCode]
        );

    }

//============================================================================//
//============================================================================//

    public function remove($file_path){

        return @unlink($file_path);

    }

//============================================================================//
//============================================================================//

    public function getUploadDestinationDirectory(){

        $cfg = cmsConfig::getInstance();
        $user = cmsUser::getInstance();

        $dir_num_user   = sprintf('%03d', intval($user->id/100));
        $dir_num_file   = sprintf('%03d', intval($user->files_count/100));
        $dest_dir       = $cfg->upload_path . "{$dir_num_user}/u{$user->id}/{$dir_num_file}";

        @mkdir($dest_dir, 0777, true);

        return $dest_dir;

    }

//============================================================================//
//============================================================================//

    public function isImage($src){

        $size = getimagesize($src);

        if ($size === false) return false;

        return true;

    }

//============================================================================//
//============================================================================//

    public function imageCopyResized($src, $dest, $maxwidth, $maxheight, $is_square=false, $quality=100){

        if (!file_exists($src)) return false;

        $upload_dir = dirname($dest);

        if (!is_writable($upload_dir)) { @chmod($dest, 0755); }

        $size = getimagesize($src);

        if ($size === false) return false;

        $new_width = $size[0];
        $new_height = $size[1];

        if (($new_height <= $maxheight) && ($new_width <= $maxwidth)) {
            @copy($src, $dest);
            return true;
        }

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $icfunc = "imagecreatefrom" . $format;
        if (!function_exists($icfunc)) return false;

        $isrc = $icfunc($src);

        if ($is_square) {
            $idest = imagecreatetruecolor($maxwidth, $maxwidth);
            imagefill($idest, 0, 0, 0xFFFFFF);
            if ($new_width > $new_height)
                imagecopyresampled($idest, $isrc, 0, 0, round((max($new_width, $new_height) - min($new_width, $new_height)) / 2), 0, $maxwidth, $maxwidth, min($new_width, $new_height), min($new_width, $new_height));
            if ($new_width < $new_height)
                imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $maxwidth, $maxwidth, min($new_width, $new_height), min($new_width, $new_height));
            if ($new_width == $new_height)
                imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $maxwidth, $maxwidth, $new_width, $new_width);
        } else {
            while ($new_width > $maxwidth) {
                $new_width *= 0.99;
                $new_height *= 0.99;
            }
            while ($new_height > $maxheight) {
                $new_width *= 0.99;
                $new_height *= 0.99;
            }
            $idest = imagecreatetruecolor($new_width, $new_height);
            imagefill($idest, 0, 0, 0xFFFFFF);
            imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
        }

        imageinterlace($idest, 1);

        imagejpeg($idest, $dest, $quality);

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;

    }

//============================================================================//
//============================================================================//

}
