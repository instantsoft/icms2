<?php
class cmsAction {

    protected $controller;
    protected $params;

    public function __construct($controller, $params=array()){
        $this->controller = $controller;
        $this->params = $params;
    }

    public function __get($name) {
        return $this->controller->$name;
    }

    public function __set($name, $value) {
        $this->controller->$name = $value;
    }

    public function __isset($name) {
        return isset($this->controller->$name);
    }

    public function __unset($name) {
        unset($this->controller->$name);
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->controller, $name), $arguments);
    }

}
