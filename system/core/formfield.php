<?php

class cmsFormField {

    /**
     * Префикс названия ячейки БД поля для кэширования
     */
    const FIELD_CACHE_POSTFIX = '_cache';

    /**
     * Имя поля, как было задано в форме
     * @var string
     */
    public $name;
    /**
     * Имя поля, как должно быть в HTML теге
     * @var string
     */
    public $element_name = '';
    /**
     * Тип фильтрации для поля
     * true, false, int, str или date
     * @var string || boolean
     */
    public $filter_type = false;
    /**
     * Краткое описания поля фильтрации для простых фильтров в админке
     * @var string
     */
    public $filter_hint  = false;
    /**
     * Название поля
     * @var string
     */
    public $title;
    public $element_title = '';
    /**
     * Флаг, указывающий, что поле может быть использовано для создания в полях типов контента, конструкторе форм и т.п.
     * @var boolean
     */
    public $is_public = true;
    /**
     * Последняя часть строки SQL запроса для создания поля в базе данных
     * @var string
     */
    public $sql;
    /**
     * Последняя часть строки SQL запроса для создания поля в базе данных, в котором будет храниться кэшированное значение
     * @var string
     */
    public $cache_sql;
    /**
     * Флаг, указывающий, что при создании поля в базе данных (например, при добавлении поля в типах контента)
     * необходимо также добавить SQL индекс к этому полю
     * @var boolean
     */
    public $allow_index = true;
    /**
     * Флаг, указывающий, что нам нужна денормализация данных, полученных из поля формы
     * @var boolean
     */
    public $is_denormalization = false;
    /**
     * Массив записи, в которой это поле используется
     * @var array
     */
    public $item = null;
    /**
     * ID поля, если запись о нём есть в таблице
     * @var integer
     */
    public $field_id = 0;
    /**
     * Флаг, что поле виртуальное
     * @var boolean
     */
    public $is_virtual = false;
    /**
     * Флаг скрытого поля
     * @var boolean
     */
    public $is_hidden = false;
    /**
     * Массив правил валидации
     * @var array
     */
    public $rules = array();
    /**
     * Массив опций поля
     * @var array
     */
    public $options = array();
    protected $default_options_loaded = false;

    /**
     * Тип переменной поля
     * boolean | integer | double | string | array | object | resource
     * если получаемые значения от поля типизированы (всегда одного типа)
     * указывайте это свойство в своем классе поля
     * @var string
     */
    public $var_type = null;
    /**
     * Массив для данных в шаблоне
     * @var array
     */
    public $data = array();

	public function __construct($name, $options=false){

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

        if($this->default_options_loaded !== true){

            $options = $this->getOptions();

            $field_options = array();

            foreach($options as $field){
                $field_options[$field->getName()] = $field->getDefaultValue();
            }

            $this->options = array_merge($field_options, $this->options);

            $this->default_options_loaded = true;

        }

        if(array_key_exists($key, $this->options)){
            return $this->options[$key];
        }

        return null;

    }

    public function setOptions($options){
        if (is_array($options)){
            foreach($options as $option=>$value){
				if ($option == 'id') {
                    $this->field_id = $value;
                    continue;
                }
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

        $keys = explode(':', $name);
        $this->element_name = count($keys) > 1 ? array_shift($keys) . '[' . implode('][', $keys) . ']' : $name;

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

    public function afterStore($item, $model, $action){
        return;
    }

    public function delete($value){
        return true;
    }

    public function hookAfterAdd($content_table_name, $field, $model){
        return $this;
    }

    public function hookAfterUpdate($content_table_name, $field, $field_old, $model){
        return $this;
    }

    public function hookAfterRemove($content_table_name, $field, $model){
        return $this;
    }

}
