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
