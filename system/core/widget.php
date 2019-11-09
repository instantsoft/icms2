<?php
class cmsWidget {

    public $name;
    public $controller;
    public $title;
    public $is_title;
    public $position;
    public $groups_view;
    public $groups_hide;
    public $options;
    public $css_class;

    public $is_cacheable = null;

    private $allow_cacheable_option = true;

    private $template;
    private $wrapper = 'wrapper';

    public function __construct($widget){

        foreach($widget as $field => $value){
            // кэшированием можно управлять из класса виджета
            // свойство там - приоритетное
            if($field === 'is_cacheable'){
                if($this->is_cacheable === null){
                    $this->is_cacheable = boolval($value);
                } else {
                    $this->allow_cacheable_option = false;
                }
                continue;
            }
            $this->{$field} = $value;
        }

        $this->css_class       = $widget['class'];
        $this->css_class_title = empty($widget['class_title']) ? '' : $widget['class_title'];
        $this->css_class_wrap  = empty($widget['class_wrap']) ? '' : $widget['class_wrap'];
        $this->template        = $this->name;

        if (!empty($widget['tpl_wrap'])){
			$this->setWrapper($widget['tpl_wrap']);
		}

		if (!empty($widget['tpl_body'])){
			$this->setTemplate($widget['tpl_body']);
		}

    }

    public function getOption($key, $default = false){
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    public function getOptions(){
        return $this->options;
    }

    public function setTemplate($template){
        $this->template = $template;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function setWrapper($template){
        $this->wrapper = $template;
    }

    public function getWrapper(){
        return $this->wrapper;
    }

    public function disableCache(){
        $this->is_cacheable = false;
    }

    public function enableCache(){
        $this->is_cacheable = true;
    }

    public function isCacheable(){
        return $this->is_cacheable;
    }

    public function isAllowCacheableOption(){
        return $this->allow_cacheable_option;
    }

}
