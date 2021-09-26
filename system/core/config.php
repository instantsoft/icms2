<?php
/**
 * Класс для работы с конфигурациями
 */
class cmsConfig {

    /**
     * Путь директории с конфигурациями
     * можно изменить на хранение вне корня сайта,
     * изменив путь, используя две точки (..) для
     * указания на родительские каталоги
     */
    const CONFIG_DIR = '/system/config/';

    /**
     * Синглтон
     * @var object
     */
    private static $instance = null;
    /**
     * Сформированный массив ремапа контроллеров
     * @var array
     */
    private static $mapping  = null;

    /**
     * Флаг, что конфиг сайта есть и загружен
     * @var boolean
     */
    private $ready = false;
    /**
     * Массив конфигурации сайта
     * @var array
     */
    private $data = [];
    /**
     * Динамические значения конфигурации,
     * которые не указаны в файле
     * @var array
     */
    private $dynamic = [];
    /**
     * Значения конфигурации, как они есть в файле
     * @var array
     */
    private $config = [];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get($key) {
        return self::getInstance()->$key;
    }

    public static function getControllersMapping() {

        if (self::$mapping !== null) {
            return self::$mapping;
        }

        self::$mapping = [];

        $map_file     = 'system/config/remap.php';
        $map_function = 'remap_controllers';

        if (!cmsCore::includeFile($map_file)) {
            return self::$mapping;
        }

        if (!function_exists($map_function)) {
            return self::$mapping;
        }

        self::$mapping = call_user_func($map_function);

        if (!is_array(self::$mapping)) {
            return [];
        }

        return self::$mapping;
    }

    public function __construct($cfg_file = 'config.php') {
        if ($this->setData($cfg_file)) {
            $this->ready = true;
        }
    }

    /**
     * Конфигурация есть и загружена
     * @return boolean
     */
    public function isReady() {
        return $this->ready;
    }

    /**
     * Устанавливает/изменяет значение опции конфигурации
     *
     * @param string $key Ключ
     * @param mixed $value Значение
     * @return $this
     */
    public function set($key, $value) {

        // Нет такой опции в файле конфигурации
        if(!array_key_exists($key, $this->data)){
            $this->dynamic[] = $key;
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Возвращает весь актуальный конфиг сайта
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * Возвращает весь конфиг сайта, как он задан в файле
     * Если передан ключ, возвращает его значение
     *
     * @param string $key
     * @return mixed
     */
    public function getConfig($key = null) {
        if($key === null){
            return $this->config;
        }
        return array_key_exists($key, $this->config) ? $this->config[$key] : false;
    }

    public function __get($name) {
        if(!array_key_exists($name, $this->data)){
            return false;
        }
        return $this->data[$name];
    }

    public function __isset($name) {
        return array_key_exists($name, $this->data);
    }

    public function setData($cfg_file = 'config.php') {

        $this->data = $this->load($cfg_file);
        if (!$this->data) { return false; }

        // Запоминаем оригинальный конфиг
        $this->config = $this->data;

        if (empty($this->data['detect_ip_key']) || !isset($_SERVER[$this->data['detect_ip_key']])) {
            $this->data['detect_ip_key'] = 'REMOTE_ADDR';
        }

        if (empty($this->data['session_save_path'])) {

            $this->data['session_save_path'] = session_save_path();

            if (empty($this->data['session_save_path'])) {
                $this->data['session_save_path'] = rtrim(sys_get_temp_dir(), '/');
            }

            if (!is_writable($this->data['session_save_path'])) {
                $this->data['session_save_path'] = '';
            }
        }

        // Переходное для 2.14.0
        if (!array_key_exists('allow_users_time_zone', $this->data)) {
            $this->data['allow_users_time_zone'] = 1;
        }
        if (!array_key_exists('bcmathscale', $this->data)) {
            $this->data['bcmathscale'] = 8;
        }
        // Ставим константу, для вспомогательных функций
        define('BCMATHSCALE', $this->data['bcmathscale']);
        // разрядность математической библиотеки
        if(function_exists('bcscale')){
            bcscale($this->data['bcmathscale']);
        }

        if (empty($this->data['native_yaml']) || !function_exists('yaml_emit')) {
            $this->data['native_yaml'] = 0;
        }

        $this->upload_host_abs = $this->upload_host;

        if (mb_strpos($this->upload_host, $this->host) === 0) {
            $url_parts = parse_url(trim($this->host, '/'));
            $host = empty($url_parts['path']) ? $this->host : $url_parts['scheme'] . '://' . $url_parts['host'];
            $this->upload_host = str_replace($host, '', $this->upload_host);
            $replace_upload_host_protocol = true;
        }

        $this->set('document_root', rtrim(PATH, $this->root));
        $this->set('root_path', PATH . DIRECTORY_SEPARATOR);
        $this->set('system_path', $this->root_path . 'system/');
        $this->set('upload_path', $this->document_root . $this->upload_root);
        $this->set('cache_path', $this->document_root . $this->cache_root);

        $protocol = 'http://';
        if (
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) {
            $protocol = 'https://';
            $this->host = str_replace('http://', $protocol, $this->host);
            if (!empty($replace_upload_host_protocol)) {
                $this->upload_host_abs = str_replace('http://', $protocol, $this->upload_host_abs);
            }
        }

        $this->set('protocol', $protocol);

        if (!empty($_SERVER['HTTP_HOST'])) {
            $this->set('current_domain', $_SERVER['HTTP_HOST']);
        }

        return true;
    }

    public static function isSecureProtocol() {
        return self::get('protocol') === 'https://';
    }

    public function load($cfg_file = 'config.php') {

        $cfg_file = PATH . self::CONFIG_DIR . $cfg_file;

        if (!is_readable($cfg_file)) {
            return [];
        }

        return include $cfg_file;
    }

    public function save($values, $cfg_file = 'config.php') {

        $dump = "<?php\n" .
                "return array(\n\n";

        foreach ($values as $key => $value) {

            if (in_array($key, $this->dynamic)) {
                continue;
            }

            $value = var_export($value, true);

            $tabs = 10 - ceil((mb_strlen($key) + 3) / 4);

            $dump .= "\t'{$key}'";
            $dump .= str_repeat("\t", $tabs > 0 ? $tabs : 0);
            $dump .= "=> $value,\n";
        }

        $dump .= "\n);\n";

        $file = PATH . self::CONFIG_DIR . $cfg_file;

        $success = false;

        if (is_writable($file)) {

            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            $success = file_put_contents($file, $dump);
        }

        return $success;
    }

    public function update($key, $value, $cfg_file = 'config.php') {

        $data = $this->load($cfg_file);
        $data[$key] = $value;

        return $this->save($data, $cfg_file);
    }

}
