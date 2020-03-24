<?php
/**
 * Базовый класс для работы всех полей форм CMS
 */
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
     * @var string | boolean
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
     * Контекст запроса
     * Устанавливается в методе parse объекта класса cmsForm
     * @var object
     */
    public $request = null;
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
     * Значение поля из массива конвертировать в json
     * @var boolean
     */
    public $store_array_as_json = false;
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

    public $visible_depend = array();

    /**
     * Исключение поля в контроллерах
     * @var array
     */
    public $excluded_controllers = array();

    public $context = null;

    /**
     * Метод для вывода поля в форме
     * @var string
     */
    public $display_input = 'getInput';

    /**
     * Показывать заголовок поля
     * при выводе поля для фильтра
     *
     * @var boolean
     */
    public $show_filter_input_title = false;

    /**
     * Формировать поле формы на нескольких языках
     * @var boolean
     */
    public $multilanguage = false;

    /**
     * Тип поля
     * по факту имя файла
     *
     * @var string
     */
    public $field_type;

    /**
     * @param string $name Имя поля
     * @param array $options Массив опций
     */
	public function __construct($name, $options=false){

        $this->setName($name);

        $this->field_type = substr(mb_strtolower(get_called_class()), 5);
        $this->class = $this->field_type;

        if ($options){
            $this->setOptions($options);
        }

    }

    /**
     * Магия для var_export
     * @param array $data
     * @return \field_class
     */
    public static function __set_state($data) {

        $field_class  = 'field'.string_to_camel('_', $data['class']);

        return new $field_class($data['name'], $data);

    }

    /**
     * Возвращает свойство поля по названию
     * @param string $key Имя свойства
     * @return mixed
     */
    public function getProperty($key){
        return isset($this->{$key}) ? $this->{$key} : false;
    }

    /**
     * Устанавливает свойство поля
     * @param string $key Имя свойства
     * @param mixed $value Присваиваемое значение
     * @return $this
     */
    public function setProperty($key, $value) { $this->{$key} = $value; return $this; }

    /**
     * Возвращает опции поля
     * Опции - это объекты полей опций
     *
     * @return array
     */
    public function getOptions() { return array(); }

    /**
     * Возвращает значение опции поля
     * @param string $key Имя опции
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    public function getOption($key, $default = null) {

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

        return $default;

    }

    /**
     * Устанавливает все параметры для поля
     * опции и свойства
     *
     * @param array $options
     */
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

    /**
     * Устанавливает значение опции
     * @param string $key Название опции
     * @param mixed $value Значение опции
     * @return $this
     */
    public function setOption($key, $value) { $this->options[$key] = $value; return $this; }

    public function setContext($value){$this->context = $value; return $this;}
    /**
     * Возвращает название поля
     * @return string
     */
    public function getTitle(){ return $this->title; }

    /**
     * Возвращает имя поля
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * Возвращает имя поля в случае денормализации при сохранении значения от поля
     * @return string
     */
    public function getDenormalName() { return $this->name.self::FIELD_CACHE_POSTFIX; }

    /**
     * Устанавливает имя поля и имя HTML элемента поля
     * @param string $name Имя поля
     * @return $this
     */
    public function setName($name) {

        $this->name = $name;

        $keys = explode(':', $name);
        $this->element_name = count($keys) > 1 ? array_shift($keys) . '[' . implode('][', $keys) . ']' : $name;

        $this->id = str_replace(array(':', '|'), '_', $name);

        return $this;

    }

    /**
     * Возвращает имя HTML элемента поля
     * @return string
     */
    public function getElementName() { return $this->element_name; }

    /**
     * Устанавливает данные текущей записи поля
     * @param array $item
     * @return $this
     */
    public function setItem($item) { $this->item = $item; return $this; }

    /**
     * Возвращает последнюю часть строки SQL запроса
     * для создания поля в базе данных,
     * в котором будет храниться кэшированное значение
     *
     * @return string
     */
    public function getCacheSQL() { return $this->cache_sql; }

    /**
     * Возвращает последнюю часть строки SQL запроса
     * для создания поля в базе данных
     *
     * @return string
     */
    public function getSQL() {

        $max_length = $this->getOption('max_length');

        if($max_length){
            return str_replace('{max_length}', $max_length, $this->sql);
        }
        return $this->sql;

    }

    /**
     * Возвращает массив правил валидации поля
     * @return array
     */
    public function getRules(){ return $this->rules; }

    /**
     * Возвращает булево значение наличия умолчания у поля
     * @return boolean
     */
    public function hasDefaultValue() { return isset($this->default); }

    /**
     * Возвращает значение по умолчанию у поля
     * @return mixed
     */
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

    /**
     * Возвращает HTML код поля в форме
     * @param mixed $value Текущее значение поля
     * @return string
     */
    public function getInput($value) {
        $this->title = $this->element_title;
        return cmsTemplate::getInstance()->renderFormField($this->class, array(
            'field' => $this,
            'value' => $value
        ));
    }

    /**
     * Возвращает HTML код поля в форме фильтра
     * @param mixed $value Текущее значение поля
     * @return string
     */
    public function getFilterInput($value){

        if(!$this->show_filter_input_title){
            $this->element_title = false;
        }

        // при фильтрации все поля необязательны
        $required_key = array_search(array('required'), $this->getRules());
        if($required_key !== false){
            unset($this->rules[$required_key]);
        }
        return $this->getInput($value);
    }

    /**
     * Возвращает отформатированное значение поля для показа в записи
     * @param mixed $value Значение поля
     * @return string
     */
    public function parse($value){ return ''; }

    /**
     * Возвращает отформатированное значение поля для показа в списке записей
     * @param mixed $value Значение поля
     * @return string
     */
    public function parseTeaser($value){ return $this->parse($value); }

    /**
     * Если значение вашего поля предполагает тип, отличный от типа «string» или же
     * значение поля формируется как некий HTML код, то этот метод должен вернуть
     * как минимум строковое представление значения.
     * Метод используется при автоматическом формировании
     * SEO параметров по шаблонам: slug (URL страницы), title, keywords, description.
     *
     * @param mixed $value Значение поля
     * @return string
     */
    public function getStringValue($value){ return $this->parse($value); }

    /**
     * Метод, который вызывается при применении фильтра списка записей,
     * например, при работе фильтра списка записей типов контента,
     * списка пользователей, групп.
     * В методе необходимо реализовать нужную фильтрацию для этого поля,
     * пользуясь объектом модели и значением из формы.
     * Вернуть метод должен объект $model.
     *
     * @param object $model Объект модели из контекста вызова
     * @param mixed $value Значение поля
     * @return boolean | $model
     */
    public function applyFilter($model, $value) { return false; }

    /**
     * Метод, который подготавливает входную переменную
     * из поля для записи в базу данных
     *
     * @param mixed $value Значение поля из формы
     * @param boolean $is_submitted Форма отправлена?
     * @param mixed $old_value Предыдущее значение поля
     * @return string | array
     */
    public function store($value, $is_submitted, $old_value=null){
        if($this->store_array_as_json && is_array($value)){
            return cmsModel::arrayToString($value);
        }
        return $value;
    }

    /**
     * Подготавливает входную переменную
     * из поля фильтра
     *
     * @param mixed $value Значение поля из формы фильтра
     * @return mixed
     */
    public function storeFilter($value){
        return $value;
    }

    /**
     * Метод, аналогичный store, но должен вернуть строку,
     * которая будет использоваться для денормализации значения
     *
     * @param mixed $value
     * @return string
     */
    public function storeCachedValue($value){
        return null;
    }

    /**
     * Метод вызывается после сохранения записей типов контента, профилей и групп
     *
     * @param array $item Полный массив записи, в которой есть текущее поле
     * @param object $model Объект модели из контекста вызова
     * @param string $action Действие - add или edit
     * @return void
     */
    public function afterStore($item, $model, $action){
        return;
    }

    /**
     * Метод, который вызывается при удалении записи
     *
     * @param mixed $value Значение поля
     * @return boolean
     */
    public function delete($value){
        return true;
    }

    /**
     * Метод вызывается после создания поля в админке,
     * например в типах контента, профилях или группах
     *
     * @param string $content_table_name Название таблицы, для которой поле создаётся
     * @param array $field Массив данных поля
     * @param object $model Объект модели контроллера контекста работы
     * @return $this
     */
    public function hookAfterAdd($content_table_name, $field, $model){
        return $this;
    }

    /**
     * Метод вызывается после редактирования поля в админке,
     * например в типах контента, профилях или группах
     *
     * @param string $content_table_name Название таблицы, для которой поле редактируется
     * @param array $field Новый массив данных поля
     * @param array $field_old Старый (до редактирования) массив данных поля
     * @param object $model объект модели контроллера контекста работы
     * @return $this
     */
    public function hookAfterUpdate($content_table_name, $field, $field_old, $model){
        return $this;
    }

    /**
     * Метод вызывается после удаления поля в админке,
     * например в типах контента, профилях или группах
     *
     * @param string $content_table_name Название таблицы, для которой поле было создано
     * @param array $field Массив данных поля
     * @param object $model Объект модели контроллера контекста работы
     * @return $this
     */
    public function hookAfterRemove($content_table_name, $field, $model){
        return $this;
    }

}
