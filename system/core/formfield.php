<?php

class cmsFormField {

    const FIELD_CACHE_POSTFIX = '_cache';

    public $name;
    public $element_name = '';
    public $filter_type = false;
    public $filter_hint  = false;

    public $title;
    public $element_title = '';

    public $is_public = true;

    public $sql;
    public $cache_sql;
    public $allow_index = true;
    public $is_denormalization = false;

    public $item = null;

    public $is_virtual = false;
    public $is_hidden = false;

    public $rules = array();
    public $options = array();

    /**
     * Тип переменной поля
     * boolean | integer | double | string | array | object | resource
     * если получаемые значения от поля типизированы (всегда одного типа)
     * указывайте это свойство в своем классе поля
     * @var string
     */
    public $var_type = null;

    public $data = array(); // массив для данных в шаблоне

	function __construct($name, $options=false){

        $this->setName($name);

        $this->class = substr(mb_strtolower(get_called_class()), 5);

        if ($options){
            $this->setOptions($options);
        }

        $this->id = str_replace(':', '_', $name);

    }

    /**
     * Для var_export
     * @param array $data
     * @return \field_class
     */
    public static function __set_state($data) {

        $field_class  = 'field'.string_to_camel('_', $data['class']);

        return new $field_class($data['name'], $data);

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

    public function getDenormalName() { return $this->name.self::FIELD_CACHE_POSTFIX; }

    public function setName($name) {
        $this->name = $name;
        if (strpos($name, ':') !== false){
            list($key, $subkey) = explode(':', $name);
            $this->element_name = "{$key}[{$subkey}]";
        } else {
            $this->element_name = $name;
        }
        return $this;
    }

    public function getElementName() { return $this->element_name; }

    public function setItem($item) { $this->item = $item; return $this; }

    public function getCacheSQL() { return $this->cache_sql; }

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

    /**
     * Возвращает тип переменной для поля
     * @param bool $is_filter Указывает, что нам нужен тип при использовании в фильтре
     * @return string|null
     */
    public function getDefaultVarType($is_filter=false) {

        if(is_string($this->var_type)){
            return $this->var_type;
        }

        $default_value = $this->getDefaultValue();

        if($default_value === null){
            return null;
        }

        return gettype($default_value);

    }

    public function getInput($value) {
        $this->title = $this->element_title;
        return cmsTemplate::getInstance()->renderFormField($this->class, array(
            'field' => $this,
            'value' => $value
        ));
    }

    public function getFilterInput($value){
        $this->element_title = false;
        // при фильтрации все поля необязательны
        $required_key = array_search(array('required'), $this->getRules());
        if($required_key !== false){
            unset($this->rules[$required_key]);
        }
        return $this->getInput($value);
    }

    public function parse($value){ return false; }

    public function parseTeaser($value){ return $this->parse($value); }

    public function getStringValue($value){ return $this->parse($value); }

    public function applyFilter($model, $value) { return false; }

    public function store($value, $is_submitted, $old_value=null){
       return $value;
    }

    public function storeCachedValue($value){
        return null;
    }

    public function delete($value){
        return true;
    }

}