<?php

class cmsConfig {

    private static $instance;
    private static $mapping;

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
        $this->data[$key] = $value;
        $this->dynamic[] = $key;
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

        $this->set('cfg_time_zone', $this->data['time_zone']);

        if (isset($_SESSION['user']['time_zone'])){
            $this->data['time_zone'] = $_SESSION['user']['time_zone'];
        }

        if(empty($this->data['detect_ip_key']) || !isset($_SERVER[$this->data['detect_ip_key']])){
            $this->data['detect_ip_key'] = 'REMOTE_ADDR';
        }

        if(!isset($this->data['controllers_without_widgets'])){
            $this->data['controllers_without_widgets'] = array('admin');
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

    public function updateTimezone(){

        if (isset($_SESSION['user']['time_zone'])){
            $this->data['time_zone'] = $_SESSION['user']['time_zone'];
        }

        date_default_timezone_set( $this->data['time_zone'] );

        cmsDatabase::getInstance()->setTimezone();

    }

//============================================================================//
//============================================================================//

    public function load($cfg_file='config.php'){

        $cfg_file = PATH . '/system/config/' . $cfg_file;

        if(!is_readable($cfg_file)){
            return false;
        }

        return include $cfg_file;

    }

    public function save($values, $cfg_file='config.php'){

        $dump = "<?php\n" .
                "return array(\n\n";

        foreach($values as $key=>$value){

            if (in_array($key, $this->dynamic)){ continue; }

            $value = var_export($value, true);

            $tabs = 10 - ceil((mb_strlen($key)+3)/4);

            $dump .= "\t'{$key}'";
            $dump .= str_repeat("\t", $tabs);
            $dump .= "=> $value,\n";

        }

        $dump .= "\n);\n";

        $file = self::get('root_path').'system/config/' . $cfg_file;

        $success = false;

        if(is_writable($file)){
            $success = file_put_contents($file, $dump);
            if (function_exists('opcache_invalidate')) { @opcache_invalidate($file, true); }
        }

        return $success;

    }

    public function update($key, $value, $cfg_file='config.php'){

        $data = $this->load($cfg_file);
        $data[$key] = $value;

        return $this->save($data, $cfg_file);

    }

}
