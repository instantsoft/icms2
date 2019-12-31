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

    private static $instance = null;
    private static $mapping  = null;

    private $ready   = false;
    private $data    = array();
    private $dynamic = array();

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get($key){
        return self::getInstance()->$key;
    }

    public static function getControllersMapping(){

        if (self::$mapping !== null) { return self::$mapping; }

        self::$mapping = array();

        $map_file = 'system/config/remap.php';
        $map_function = 'remap_controllers';

        if (!cmsCore::includeFile($map_file)) { return self::$mapping; }

        if (!function_exists($map_function)){ return self::$mapping; }

        self::$mapping = call_user_func($map_function);

        if (!is_array(self::$mapping)){ return array(); }

        return self::$mapping;

    }

//============================================================================//
//============================================================================//

	public function __construct($cfg_file = 'config.php'){

        if($this->setData($cfg_file)){
            $this->ready = true;
        }

	}

//============================================================================//
//============================================================================//

    public function isReady(){
        return $this->ready;
    }

    public function set($key, $value){

        if(!isset($this->data[$key])){
            $this->dynamic[] = $key;
        }

        $this->data[$key] = $value;

        return $this;

    }

    public function getAll(){
        return $this->data;
    }

    public function __get($name) {
		if (!isset($this->data[$name])){ return false; }
        return $this->data[$name];
    }

	public function __isset($name) {
		return isset($this->data[$name]);
	}

//============================================================================//
//============================================================================//

    public function setData($cfg_file = 'config.php') {

        $this->data = $this->load($cfg_file);
        if(!$this->data){ return false; }

        // таймзона может быть изменена в процессе работы
        $this->set('cfg_time_zone', $this->data['time_zone']);

        // переходная проверка на версии 2.11.1
        if(!empty($this->data['ctype_default']) && !is_array($this->data['ctype_default'])){
            $this->data['ctype_default'] = [$this->data['ctype_default']];
        }
        if(empty($this->data['ctype_default'])){
            $this->data['ctype_default'] = [];
        }

        if(empty($this->data['detect_ip_key']) || !isset($_SERVER[$this->data['detect_ip_key']])){
            $this->data['detect_ip_key'] = 'REMOTE_ADDR';
        }

        if(empty($this->data['session_save_path'])){

            $this->data['session_save_path'] = session_save_path();

            if(empty($this->data['session_save_path'])){
                $this->data['session_save_path'] = rtrim(sys_get_temp_dir(), '/');
            }

            if(!is_writable($this->data['session_save_path'])){
                $this->data['session_save_path'] = '';
            }

        }

        if(empty($this->data['db_charset'])){
            $this->data['db_charset'] = 'utf8';
        }

        if(empty($this->data['session_save_handler'])){
            $this->data['session_save_handler'] = 'files';
        }

        if(!isset($this->data['controllers_without_widgets'])){
            $this->data['controllers_without_widgets'] = array('admin');
        }

        if(!isset($this->data['session_name'])){
            $this->data['session_name'] = 'ICMSSID';
        }

        if(empty($this->data['native_yaml']) || !function_exists('yaml_emit')){
            $this->data['native_yaml'] = 0;
        }

		$this->upload_host_abs = $this->upload_host;

		if (mb_strpos($this->upload_host, $this->host) === 0){
			$url_parts = parse_url(trim($this->host, '/'));
			$host = empty($url_parts['path']) ? $this->host : $url_parts['scheme'] . '://' . $url_parts['host'];
			$this->upload_host = str_replace($host, '', $this->upload_host); $replace_upload_host_protocol = true;
		}

        $this->set('document_root', rtrim(PATH, $this->root));
        $this->set('root_path', PATH . DIRECTORY_SEPARATOR);
        $this->set('system_path', $this->root_path . 'system/');
        $this->set('upload_path', $this->document_root . $this->upload_root);
        $this->set('cache_path', $this->document_root . $this->cache_root);

        $protocol = 'http://';
        if(
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            ){
            $protocol = 'https://';
            $this->host = str_replace('http://', $protocol, $this->host);
            if(!empty($replace_upload_host_protocol)){
                $this->upload_host_abs = str_replace('http://', $protocol, $this->upload_host_abs);
            }
        }

        $this->set('protocol', $protocol);

        if(!empty($_SERVER['HTTP_HOST'])){
            $this->set('current_domain', $_SERVER['HTTP_HOST']);
        }

        return true;

    }

    public static function isSecureProtocol() {
        return self::get('protocol') === 'https://';
    }

//============================================================================//
//============================================================================//

    public function load($cfg_file = 'config.php'){

        $cfg_file = PATH . self::CONFIG_DIR . $cfg_file;

        if(!is_readable($cfg_file)){
            return false;
        }

        return include $cfg_file;

    }

    public function save($values, $cfg_file = 'config.php'){

        $dump = "<?php\n" .
                "return array(\n\n";

        foreach($values as $key=>$value){

            if (in_array($key, $this->dynamic)){ continue; }

            $value = var_export($value, true);

            $tabs = 10 - ceil((mb_strlen($key)+3)/4);

            $dump .= "\t'{$key}'";
            $dump .= str_repeat("\t", $tabs > 0 ? $tabs : 0);
            $dump .= "=> $value,\n";

        }

        $dump .= "\n);\n";

        $file = PATH . self::CONFIG_DIR . $cfg_file;

        $success = false;

        if(is_writable($file)){
            $success = file_put_contents($file, $dump);
            if (function_exists('opcache_invalidate')) { @opcache_invalidate($file, true); }
        }

        return $success;

    }

    public function update($key, $value, $cfg_file = 'config.php'){

        $data = $this->load($cfg_file);
        $data[$key] = $value;

        return $this->save($data, $cfg_file);

    }

}
