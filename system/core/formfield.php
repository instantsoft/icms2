<?php

class cmsFormField {

    public $name;
    public $element_name = '';
    public $filter_type = false;
    public $filter_hint  = false;

    public $title;
    public $element_title = '';

    public $is_public = true;

    public $sql;
    public $allow_index = true;

    public $item = null;

    public $is_virtual = false;
    public $is_hidden = false;

    public $rules = array();
    public $options = array();

    public $data = array(); // массив для данных в шаблоне

	function __construct($name, $options=false){

        $this->setName($name);

        $this->class = substr(mb_strtolower(get_called_class()), 5);

        if ($options){
            $this->setOptions($options);
        }

        $this->id = str_replace(':', '_', $name);

    }

    public function getProperty($key){
        return isset($this->{$key}) ? $this->{$key} : false;
    }

    public function setProperty($key, $value) { $this->{$key} = $value; }

    public function getOptions() { return array(); }

    public function getOption($key) {

        if(array_key_exists($key, $this->options)){
            return $this->options[$key];
        }

        $options = $this->getOptions();

        foreach($options as $field){
            if ($field->getName() == $key && $field->hasDefaultValue()){
                return $field->getDefaultValue();
            }
        }

    }

    public function setOptions($options){
        if (is_array($options)){
            foreach($options as $option=>$value){
				if ($option == 'id') { continue; }
                $this->{$option} = $value;
            }
            if (isset($options['title'])){
                $this->element_title = $options['title'];
            }
        }
    }

    public function setOption($key, $value) { $this->options[$key] = $value; }

    public function getTitle(){ return $this->title; }

    public function getName() { return $this->name; }

    public function setName($name) {
        $this->name = $name;

        if (strpos($name, ':') !== false){
            list($key, $subkey) = explode(':', $name);
            $this->element_name = "{$key}[{$subkey}]";
        } else {
            $this->element_name = $name;
        }
    }

    public function getElementName() { return $this->element_name; }

    public function setItem($item) { $this->item = $item; return $this; }

    public function getSQL() {

        $max_length = $this->getOption('max_length');

        if($max_length){
            return str_replace('{max_length}', $max_length, $this->sql);
        }
        return $this->sql;

    }

    public function getRules(){ return $this->rules; }

    public function hasDefaultValue() { return isset($this->default); }

    public function getDefaultValue() { return $this->hasDefaultValue() ? $this->default : null; }

    public function getInput($value) {
        $this->title = $this->element_title;
        return cmsTemplate::getInstance()->renderFormField($this->class, array(
            'field' => $this,
            'value' => $value
        ));
    }

    public function getFilterInput($value){
        $this->element_title = false;
        return $this->getInput($value);
    }

    public function parse($value){ return false; }

    public function parseTeaser($value){ return $this->parse($value); }

    public function getStringValue($value){ return $this->parse($value); }

    public function applyFilter($model, $value) { return true; }

    public function store($value, $is_submitted, $old_value=null){
       return $value;
    }

    public function delete($value){
        return true;
    }

}
