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

    public function hasInArray($array_name, $var){
        return isset($this->data[$array_name][$var]);
    }

    public function hasInQuery($var){
        $query = $this->getQuery();
        if (!$query){ return false; }
        return isset($query[$var]);
    }

    /**
     * Возвращает параметр из запроса
     * @param str $var
     * @return mixed
     */
    public function get($var, $default=false){

        $is_array = strstr($var, ':');

        //если значение не определено, возвращаем умолчание

        if (!$is_array){
            if (!$this->has($var)) { return $default; }
            $value = $this->data[$var];
        } else {
            $name_parts = explode(':', $var);
            if (!$this->hasInArray($name_parts[0], $name_parts[1])) { return $default; }
            $value = $this->data[$name_parts[0]][$name_parts[1]];
        }

        //если дошли сюда, то возвращаем значение как есть
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
        if (!$_FILES[$name]['size']) { return false; }
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

}
