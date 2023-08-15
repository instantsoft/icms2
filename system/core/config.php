<?php
/**
 * Класс для работы с конфигурацией InstantCMS
 */
class cmsConfig extends cmsConfigs {

    /**
     * Синглтон
     * @var object
     */
    private static $instance = null;

    /**
     * Сформированный массив ремапа контроллеров
     *
     * @var array
     */
    private static $mapping  = null;

    /**
     * Флаг, что конфиг сайта есть и загружен
     * @var boolean
     */
    private $ready = false;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get($key) {
        return self::getInstance()->$key;
    }

    /**
     * Подключаем ремап контроллеров
     * http://docs.instantcms.ru/manual/settings/rewriting
     *
     * @return array
     */
    public static function getControllersMapping() {

        if (self::$mapping !== null) {
            return self::$mapping;
        }

        self::$mapping = [];

        $map_file     = PATH . parent::CONFIG_DIR . 'remap.php';
        $map_function = 'remap_controllers';

        if (!is_readable($map_file)) {
            return self::$mapping;
        }

        include_once $map_file;

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

        parent::__construct($cfg_file);

        if (!$this->data) {
            return;
        }

        $this->setData();

        $this->initErrorReporting();

        $this->ready = true;
    }

    /**
     * Конфигурация есть и загружена
     * @return boolean
     */
    public function isReady() {
        return $this->ready;
    }

    /**
     * Устанавливает дополнительные опции конфигурации
     *
     * @return bool
     */
    public function setData() {

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

        $this->set('document_root', preg_replace('#(.*)('.preg_quote($this->root).')$#u', '$1', PATH . DIRECTORY_SEPARATOR));
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

    /**
     * Устанавливает обработку ошибок PHP
     */
    private function initErrorReporting() {

        error_reporting(E_ALL);

        ini_set('log_errors', true);

        // Если данная опция установлена в .htaccess или в Apache
        // Её установка здесь ничего не изменит
        ini_set('display_errors', $this->data['debug']);
        ini_set('display_startup_errors', $this->data['debug']);

    }

    /**
     * Проверяет, что текущий протокол HTTPS
     *
     * @return boolean
     */
    public static function isSecureProtocol() {
        return self::get('protocol') === 'https://';
    }

}
