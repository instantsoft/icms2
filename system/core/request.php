<?php

class cmsRequest {

    const CTX_AUTO_DETECT = 0;
    const CTX_STANDARD = 1;
    const CTX_INTERNAL = 2;
    const CTX_AJAX = 3;

    private $data = array();
    private $context;
    private $permission;

    public $uri = '';
    public $query_string = '';

    private static $device_type = null;
    public static $device_types = array('desktop', 'mobile', 'tablet');

    /**
     * Создает объект запроса
     * @param array $data Параметры для контроллера
     * @param int $context Контекст (если не указан, определяется автоматически)
     */
    public function __construct($data, $context=cmsRequest::CTX_AUTO_DETECT){

        $this->data = $data;

        if ($context == cmsRequest::CTX_AUTO_DETECT){
            $this->context = $this->detectContext();
        } else {
            $this->context = $context;
        }

    }

//============================================================================//
//============================================================================//

    /**
     * Определяет контекст текущего запроса (стандартный или ajax)
     * @return int
     */
    private function detectContext(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return cmsRequest::CTX_AJAX;
        } else {
            return cmsRequest::CTX_STANDARD;
        }
    }

    /**
     * Возвращает текущий контекст
     * @return int
     */
    public function getContext(){
        return $this->context;
    }

    /**
     * Возвращает true, если запрос вызван через URL
     * @return bool
     */
    public function isStandard(){
        return ($this->context == cmsRequest::CTX_STANDARD);
    }

    /**
     * Возвращает true, если запрос вызван другим контроллером
     * @return bool
     */
    public function isInternal(){
        return ($this->context == cmsRequest::CTX_INTERNAL);
    }

    /**
     * Возвращает true, если запрос вызван через AJAX
     * @return bool
     */
    public function isAjax(){
        return ($this->context == cmsRequest::CTX_AJAX);
    }

//============================================================================//
//============================================================================//

    public function has($var){
        return isset($this->data[$var]);
    }

    public function hasInArray(){
        $keys = func_get_args(); if(count($keys) === 1){ $keys = $keys[0]; }
        return (bool)array_value_recursive($keys, $this->data);
    }

    public function hasInQuery($var){
        $query = $this->getQuery();
        if (!$query){ return false; }
        return isset($query[$var]);
    }

    /**
     * Возвращает параметр из запроса
     * @param string $var Название переменной
     * @param mixed $default Значение по умолчанию, если в запросе переменной нет
     * @param string $var_type Тип переменной
     * @return mixed
     */
    public function get($var, $default=false, $var_type=null){

        //если значение не определено, возвращаем умолчание

        if (strpos($var, ':') === false){
            if (!$this->has($var)) { return $default; }
            $value = $this->data[$var];
        } else {
            $value = array_value_recursive($var, $this->data);
            if ($value === null) { return $default; }
        }

        if($var_type === null){

            // типизируем, основываясь на значении по умолчанию
            // пока что берем во внимание не все типы
            $default_type = gettype($default);
            if(in_array($default_type, array('integer','string','double','array'))){
                @settype($value, $default_type); // подавляем, чтобы не видеть нотис, если массив в строку
            }

        } else {
            @settype($value, $var_type);
        }

        return $value;

    }

    /**
     * Возвращает все имеющиеся параметры
     * @return array
     */
    public function getAll(){ return $this->data; }
    public function getData(){ return $this->getAll(); }

	public function setData($data){ $this->data = $data; }

    public function getQuery(){
        $core = cmsCore::getInstance();
        return $core->uri_query;
    }

//============================================================================//
//============================================================================//

    public function hasFile($name){
        if (!isset($_FILES[$name])) { return false; }
        if (empty($_FILES[$name]['size'])) { return false; }
        return true;
    }

//============================================================================//
//============================================================================//

    public function set($name, $value){
        $this->data[$name] = $value;
    }


//============================================================================//
//============================================================================//

    public function setPermission($perm){
        $this->permission = $perm;
    }

    public function getPermission(){
        return $this->permission;
    }

//============================================================================//
//============================================================================//

    private static function loadDeviceType() {

        $device_type  = (string)cmsUser::getCookie('device_type');

        if(!$device_type || !in_array($device_type, self::$device_types, true)){

            cmsCore::loadLib('mobile_detect.class');

            $detect = new Mobile_Detect();

            $device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');

            cmsUser::setCookie('device_type', $device_type, 31536000); // на 1 год

        }

        self::$device_type = $device_type;

    }

    public static function getDeviceType() {

        if(self::$device_type === null){
            self::loadDeviceType();
        }

        return self::$device_type;

    }

}
