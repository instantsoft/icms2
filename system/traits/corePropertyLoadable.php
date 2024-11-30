<?php

namespace icms\traits;

/**
 * Трейт для магии классов core, контроллеров и моделей
 *
 * @property \cmsConfig $cms_config
 * @property \cmsCore $cms_core
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 */
trait corePropertyLoadable {

    /**
     * Возвращает объект модели или контроллера
     * А если не найдены, возвращает класс-заглушку
     *
     * @param string $name Тип и имя контроллера через двоеточие
     * @return anon|\cmsController|\cmsModel
     */
    public function callIfExists($name) {

        list($type, $controller) = explode(':', $name);

        $obj = null;

        if (!\cmsController::enabled($controller)) {
            $type = 'disabled';
        }

        switch ($type) {
            case 'model':

                $obj = \cmsCore::getModel($controller, '_', false);

                break;
            case 'controller':

                $obj = \cmsCore::getController($controller, ($this->request ?? null), false);

                break;
            default:
                break;
        }

        if ($obj !== null) {
            return $obj;
        }

        return new class() {
            public function __call($name, $arguments = []) { return null; }
            public function __get($name) { return null; }
            public function __set($name, $value) {}
            public function __isset($name) { return false; }
            public function __unset($name) {}
        };
    }

    public function __get($name) {

        $this->{$name} = null;

        if (strpos($name, 'cms_') === 0) {

            $class_name = lcfirst(\string_to_camel('_', $name));

            if (method_exists($class_name, 'getInstance')) {
                $this->{$name} = call_user_func([$class_name, 'getInstance']);
            } else {
                $this->{$name} = new $class_name();
            }

        } else if (strpos($name, 'controller_') === 0) {

            $this->{$name} = \cmsCore::getController(str_replace('controller_', '', $name), ($this->request ?? null));

        } else if (strpos($name, 'model_') === 0) {

            $this->{$name} = \cmsCore::getModel(str_replace('model_', '', $name));

        } else if ($name === 'model') {

            $this->{$name} = new \cmsModel();

        } else {
            trigger_error('Undefined property: '.$name, E_USER_WARNING);
        }

        return $this->{$name};
    }

}
