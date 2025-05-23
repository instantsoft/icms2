<?php
/**
 * Базовый класс экшенов
 *
 * @property \cmsConfig $cms_config
 * @property \cmsCore $cms_core
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 * @property \cmsRequest $request
 */
class cmsAction {

    /**
     * Объект контроллера
     * @var cmsController
     */
    protected $controller;

    /**
     * Параметры экшена
     * @var array
     */
    protected $params;

    /**
     * Массив языков для подключения
     * @var array
     */
    protected $extended_langs = [];

    public function __construct(cmsController $controller, $params = []) {

        $this->controller = $controller;
        $this->params     = $params;

        if ($this->extended_langs) {
            foreach ($this->extended_langs as $controller_name) {
                cmsCore::loadControllerLanguage($controller_name);
            }
        }
    }

    /**
     * Вызывается до начала работы экшена
     */
    public function before() {
        return true;
    }

    /**
     * Вызывается после работы экшена
     */
    public function after() {
        return true;
    }

    public function &__get($name) {
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
        return call_user_func_array([$this->controller, $name], $arguments);
    }

}
