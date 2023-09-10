<?php

class cmsUploader {

    private $allow_remote = false;
    private $file_name = '';
    private $user_id = 0;
    private $site_cfg = null;

    private $last_error = null;
    private $upload_errors = [];

    private $allowed_mime = false;
    private $allowed_mime_ext = [];
    private $mime_types = [];

    public function __construct($user_id = null) {

        $this->mime_types = (new cmsConfigs('mimetypes.php'))->getAll();

        $this->upload_errors = [
            UPLOAD_ERR_OK         => LANG_UPLOAD_ERR_OK,
            UPLOAD_ERR_INI_SIZE   => sprintf(LANG_UPLOAD_ERR_INI_SIZE, $this->getMaxUploadSize()),
            UPLOAD_ERR_FORM_SIZE  => LANG_UPLOAD_ERR_FORM_SIZE,
            UPLOAD_ERR_PARTIAL    => LANG_UPLOAD_ERR_PARTIAL,
            UPLOAD_ERR_NO_FILE    => LANG_UPLOAD_ERR_NO_FILE,
            UPLOAD_ERR_NO_TMP_DIR => LANG_UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE => LANG_UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION  => LANG_UPLOAD_ERR_EXTENSION
        ];

        $this->user_id  = $user_id ?? cmsUser::getInstance()->id;
        $this->site_cfg = cmsConfig::getInstance();
    }

    /**
     * Устанавливает разрешённые типы файлов
     *
     * @param array $types
     * @return $this
     */
    public function setAllowedMime($types) {

        $this->allowed_mime = $types;

        foreach ($this->allowed_mime as $mime) {
            if (isset($this->mime_types[$mime])) {
                $this->allowed_mime_ext[] = $this->mime_types[$mime];
            }
        }

        return $this;
    }

    /**
     * Устанавливает разрешённые типы файлов по их расширению
     *
     * @param array|string $allowed_ext
     * @return $this
     */
    private function setAllowedMimeByExt($allowed_ext) {

        // Если установлено ранее, то ничего не делаем
        if ($this->allowed_mime) {
            return $this;
        }

        $this->allowed_mime = [];

        if (!is_array($allowed_ext)) {
            $allowed_ext = explode(',', (string) $allowed_ext);
        }

        foreach ($allowed_ext as $aext) {

            $aext = mb_strtolower(trim(trim((string) $aext, '., ')));

            if (empty($aext)) {
                continue;
            }

            $mime_key = array_search($aext, $this->mime_types, true);

            if(!$mime_key){
                continue;
            }

            $this->allowed_mime[] = $mime_key;

            $this->allowed_mime_ext[] = $aext;
        }

    }

    /**
     * Устанавливает имя файла
     *
     * @param string $name
     * @return $this
     */
    public function setFileName($name) {

        $this->file_name = mb_substr(trim($name), 0, 64);

        return $this;
    }

    /**
     * Устанавливает id пользователя
     *
     * @param int $id
     * @return $this
     */
    public function setUserId($id) {

        $this->user_id = $id;

        return $this;
    }

    /**
     * Возвращает последнюю ошибку
     *
     * @return ?string
     */
    public function getLastError() {
        return $this->last_error;
    }

    /**
     * Возвращает строку с максимальным размером загружаемых файлов,
     * установленным в php.ini
     *
     * @return string
     */
    public function getMaxUploadSize() {

        // вычисляем по тому, что меньше, т.к. если post_max_size меньше upload_max_filesize,
        // то максимум можно будет загрузить post_max_size
        $max_size = min(files_convert_bytes(@ini_get('upload_max_filesize')), files_convert_bytes(@ini_get('post_max_size')));

        return files_format_bytes($max_size);
    }

    /**
     * Проверяет, загружен ли файл наличием его в $_FILES
     *
     * @param string $name Имя в массиве $_FILES
     * @return boolean
     */
    public function isUploaded($name) {

        if (!isset($_FILES[$name])) {
            return false;
        }

        if (empty($_FILES[$name]['size'])) {

            if (isset($_FILES[$name]['error'])) {
                if (isset($this->upload_errors[$_FILES[$name]['error']]) && $this->upload_errors[$_FILES[$name]['error']] !== UPLOAD_ERR_OK) {
                    $this->last_error = $this->upload_errors[$_FILES[$name]['error']];
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Проверяет, загружен ли файл через XHR
     *
     * @param string $name Имя в массиве $_GET
     * @return boolean
     */
    public function isUploadedXHR($name) {
        return !empty($_GET['qqfile']);
    }

    /**
     * Проверяет, надо ли загрузить файл по ссылке
     *
     * @param string $name Имя в массиве $_POST
     * @return boolean
     */
    public function isUploadedFromLink($name) {
        return $this->allow_remote && !empty($_POST[$name]);
    }

    /**
     * Разрешает загрузку по ссылке
     *
     * @return $this
     */
    public function enableRemoteUpload() {

        $this->allow_remote = true;

        return $this;
    }

    /**
     * Запрещает загрузку по ссылке
     *
     * @return $this
     */
    public function disableRemoteUpload() {

        $this->allow_remote = false;

        return $this;
    }

    /**
     * Возвращает имя файла с расширением
     * проверяя наличии одноимённого
     *
     * @param string $path Путь к директории хранения файла
     * @param string $file_ext Расширение файла
     * @param ?string $file_name Имя файла
     * @return string
     */
    private function getFileName($path, $file_ext, $file_name = null) {

        if (!$file_name) {
            if ($this->file_name) {
                $file_name = str_replace('.' . $file_ext, '', files_sanitize_name($this->file_name . '.' . $file_ext));
            } else {
                $file_name = substr(md5(microtime(true)), 0, 8);
            }
        }

        if (file_exists($path . $file_name . '.' . $file_ext)) {
            return $this->getFileName($path, $file_ext, $file_name . '_' . md5(microtime(true)));
        }

        return $file_name . '.' . $file_ext;
    }

    /**
     * Загружает файл на сервер
     *
     * @param string $filename Название поля с файлом
     * @param string $allowed_ext Список допустимых расширений (через запятую)
     * @param string $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Директория назначения (внутри пути upload)
     * @return array
     */
    public function upload($filename, $allowed_ext = false, $allowed_size = 0, $destination = false) {

        // Если переданы расширения
        if ($allowed_ext) {
            $this->setAllowedMimeByExt($allowed_ext);
        }

        if ($this->isUploadedFromLink($filename)) {
            return $this->uploadFromLink($filename, $allowed_size, $destination);
        }

        if ($this->isUploadedXHR($filename)) {
            return $this->uploadXHR($filename, $allowed_size, $destination);
        }

        if ($this->isUploaded($filename)) {
            return $this->uploadForm($filename, $allowed_size, $destination);
        }

        $last_error = $this->getLastError();

        return [
            'success' => false,
            'error'   => ($last_error ? $last_error : LANG_UPLOAD_ERR_NO_FILE)
        ];
    }

//============================================================================//
//============================================================================//

    /**
     * Загружает файл на сервер переданный через input типа file
     *
     * @param string $filename Название поля с файлом в массиве $_FILES
     * @param int $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Директория назначения (внутри пути upload)
     * @return array
     */
    public function uploadForm($filename, $allowed_size = 0, $destination = false) {

        $source     = $_FILES[$filename]['tmp_name'];
        $error_code = $_FILES[$filename]['error'];
        $dest_size  = (int) $_FILES[$filename]['size'];
        $dest_name  = files_sanitize_name($_FILES[$filename]['name']);

        $file = cmsUploadfile::fromPath($source, $this->allowed_mime);

        if (!$file->isAllowed()) {
            return [
                'error'   => LANG_UPLOAD_ERR_MIME . '. ' . sprintf(LANG_PARSER_FILE_EXTS_FIELD_HINT, implode(', ', $this->allowed_mime_ext)),
                'success' => false,
                'name'    => $dest_name
            ];
        }

        if ($allowed_size) {
            if ($dest_size > $allowed_size) {
                return [
                    'error'   => sprintf(LANG_UPLOAD_ERR_INI_SIZE, files_format_bytes($allowed_size)),
                    'success' => false,
                    'name'    => $dest_name
                ];
            }
        }

        $dest_ext = $file->getExt();

        if (!$destination) {
            $destination = $this->getUploadDestinationDirectory();
        } else {
            $destination = $this->site_cfg->upload_path . $destination . '/';
        }

        if (!$this->file_name) {
            $this->file_name = pathinfo($dest_name, PATHINFO_FILENAME);
        }

        $destination .= $this->getFileName($destination, $dest_ext);

        return $this->moveUploadedFile($source, $destination, $error_code, $dest_name, $dest_size);
    }

    /**
     * Загружает файл по ссылке
     *
     * @param string $post_filename Название поля с файлом в массиве $_POST
     * @param int $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Директория назначения (внутри пути upload)
     * @return array
     */
    public function uploadFromLink($post_filename, $allowed_size = 0, $destination = false) {

        $link = $file_name = trim($_POST[$post_filename]);

        $url_data = parse_url($link);

        // Валидный URL с PATH
        if (
            filter_var($link, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) !== $link ||
            empty($url_data['host'])
            ) {

            return [
                'success' => false,
                'error'   => 'Not allowed',
                'name'    => '',
                'path'    => ''
            ];
        }

        // Узнаём ipv4 адрес хоста, gethostbyname умеет только ipv4
        $host_ip = gethostbyname($url_data['host']);
        // Не зарезольвили
        if ($host_ip === $url_data['host']) {
            return [
                'success' => false,
                'error'   => 'Not allowed',
                'name'    => '',
                'path'    => ''
            ];
        }

        // Проверяем вхождение в зарезервированные сети
        if(filter_var(
            $host_ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
        ) !== $host_ip){
            return [
                'success' => false,
                'error'   => 'Not allowed',
                'name'    => '',
                'path'    => ''
            ];
        }

        // проверяем редирект и имя файла
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $headers = curl_exec($curl);
        curl_close($curl);

        $matches = [];
        if (preg_match("/(?:Location:|URI:)([^\n]+)*/is", $headers, $matches)) {

            $url = trim($matches[1]);

            if (strpos($url, 'http') !== 0) {
                $link = $url_data['scheme'] . '://' . $url_data['host'] . $url;
            } else {
                $link = $url;
            }

            $_POST[$post_filename] = $link;

            return $this->uploadFromLink($post_filename, $allowed_size, $destination);
        }

        if (preg_match('#filename="([^"]+)#uis', $headers, $matches)) {
            $file_name = trim($matches[1]);
        }

        $dest_name = files_sanitize_name($file_name);

        $file_bin = file_get_contents_from_url($link);

        if (!$file_bin) {
            return [
                'success' => false,
                'error'   => LANG_UPLOAD_ERR_NO_FILE,
                'name'    => $dest_name,
                'path'    => ''
            ];
        }

        return $this->saveFileFromString($file_bin, $allowed_size, $destination, $dest_name);
    }

    /**
     * Сохраняет файл из php://input в целевую директорию  и отслеживает ошибки
     *
     * @param string $filename Название поля с файлом в массиве $_GET
     * @param int $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Директория назначения (внутри пути upload)
     * @return array
     */
    public function uploadXHR($filename, $allowed_size = 0, $destination = false) {

        $dest_name = files_sanitize_name($_GET['qqfile']);

        $file_bin = file_get_contents('php://input');

        if (!$file_bin) {
            return [
                'success' => false,
                'error'   => LANG_UPLOAD_ERR_NO_FILE,
                'name'    => $dest_name,
                'path'    => ''
            ];
        }

        return $this->saveFileFromString($file_bin, $allowed_size, $destination, $dest_name);
    }

    /**
     * Сохраняет файл из строки данных
     *
     * @param string $file_bin
     * @param int $allowed_size Максимальный размер файла (в байтах)
     * @param string $destination Директория назначения (внутри пути upload)
     * @param string $dest_name Имя файла
     * @return array
     */
    private function saveFileFromString($file_bin, $allowed_size, $destination, $dest_name) {

        $file = cmsUploadfile::fromString($file_bin, $this->allowed_mime);

        if (!$file->isAllowed()) {
            return [
                'error'   => LANG_UPLOAD_ERR_MIME . '. ' . sprintf(LANG_PARSER_FILE_EXTS_FIELD_HINT, implode(', ', $this->allowed_mime_ext)),
                'success' => false,
                'name'    => $dest_name
            ];
        }

        $dest_ext = $file->getExt();

        $file_size = strlen($file_bin);

        if ($allowed_size) {
            if ($file_size > $allowed_size) {
                return [
                    'error'   => sprintf(LANG_UPLOAD_ERR_INI_SIZE, files_format_bytes($allowed_size)),
                    'success' => false,
                    'name'    => $dest_name
                ];
            }
        }

        if (!$destination) {
            $destination = $this->getUploadDestinationDirectory();
        } else {
            $destination = $this->site_cfg->upload_path . $destination . '/';
        }

        $destination .= $this->getFileName($destination, $dest_ext);

        if (!is_writable(dirname($destination))) {
            return [
                'success' => false,
                'error'   => LANG_UPLOAD_ERR_CANT_WRITE,
                'name'    => $dest_name,
                'path'    => ''
            ];
        }

        if(file_put_contents($destination, $file_bin) === false){
            return [
                'success' => false,
                'error'   => LANG_UPLOAD_ERR_CANT_WRITE,
                'name'    => $dest_name,
                'path'    => ''
            ];
        }

        return [
            'success' => true,
            'path'    => $destination,
            'url'     => str_replace($this->site_cfg->upload_path, '', $destination),
            'name'    => basename($destination),
            'size'    => $file_size
        ];
    }

    /**
     * Копирует файл из временной директории в целевую и отслеживает ошибки
     *
     * @param string $source
     * @param string $destination
     * @param int $errorCode
     * @return array
     */
    private function moveUploadedFile($source, $destination, $errorCode, $orig_name = '', $orig_size = 0) {

        if ($errorCode !== UPLOAD_ERR_OK && isset($this->upload_errors[$errorCode])) {

            return [
                'success' => false,
                'error'   => $this->upload_errors[$errorCode],
                'name'    => $orig_name,
                'path'    => ''
            ];
        }

        $upload_dir = dirname($destination);
        if (!is_writable($upload_dir)) {
            @chmod($upload_dir, 0777);
        }

        if (!is_writable($upload_dir)) {

            return [
                'success' => false,
                'error'   => LANG_UPLOAD_ERR_CANT_WRITE,
                'name'    => $orig_name,
                'path'    => ''
            ];
        }

        return [
            'success' => @move_uploaded_file($source, $destination),
            'path'    => $destination,
            'url'     => str_replace($this->site_cfg->upload_path, '', $destination),
            'name'    => basename($destination),
            'size'    => $orig_size,
            'error'   => $this->upload_errors[$errorCode]
        ];
    }

    /**
     * Удаляет файл
     * @param string $file_path
     * @return boolean
     */
    public function remove($file_path) {
        return files_delete_file($file_path, 2);
    }

    /**
     * Создаёт дерево директорий для загрузки файла
     * @return string
     */
    public function getUploadDestinationDirectory() {
        return files_get_upload_dir($this->user_id);
    }

}
