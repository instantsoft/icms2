<?php
/**
 * Базовый класс для работы всех полей форм CMS
 */
#[\AllowDynamicProperties]
class cmsFormField {

    /**
     * Префикс названия ячейки БД поля для кэширования
     */
    const FIELD_CACHE_POSTFIX = '_cache';

    /**
     * Имя поля, как было задано в форме
     * @var string
     */
    public $name = '';
    /**
     * Имя поля, как должно быть в HTML теге
     * @var string
     */
    public $element_name = '';
    /**
     * Класс CSS для родительского элемента поля
     * @var string
     */
    public $css_class = '';
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
    public $title = '';
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
    public $sql = '';
    /**
     * Последняя часть строки SQL запроса для создания поля в базе данных, в котором будет храниться кэшированное значение
     * @var string
     */
    public $cache_sql = '';
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
    public $rules = [];
    /**
     * Значение поля из массива конвертировать в json
     * @var boolean
     */
    public $store_array_as_json = false;
    /**
     * Флаг, что выводится тизер поля
     * @var boolean
     */
    public $is_parse_teaser = false;
    /**
     * Массив опций поля
     * @var array
     */
    public $options = [];
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
    public $data = [];

    /**
     * Зависимости показа (видимости) поля
     * @var arra
     */
    public $visible_depend = [];

    /**
     * Исключение поля в контроллерах
     * @var array
     */
    public $excluded_controllers = [];

    public $context = null;
    public $context_params = [];

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
     *
     * @var boolean
     */
    public $multilanguage = false;

    /**
     * Поле может быть доступно на нескольких языках
     * как минимум контроллер languages ориентируется на
     * этот флаг
     *
     * @var boolean
     */
    public $can_multilanguage = false;

    /**
     * Язык поля (для мультиязычности)
     *
     * @var string
     */
    public $lang = '';

    /**
     * Параметры мультиязычности поля
     *
     * @var array
     */
    public $multilanguage_params = [
        // Флаг, что поле в таблице
        // При этом автоматически создадутся языковые колонки
        'is_table_field' => null,
        // Если флаг выше включен, в этой таблице создадутся языковые колонки
        'table' => null,
        // Убирать правило required с поля
        'unset_required' => null
    ];

    /**
     * Тип поля
     * по факту имя файла
     *
     * @var string
     */
    public $field_type = '';

    /**
     * Название субъекта использования поля
     * Задаётся полю при рендере опций поля в админке
     *
     * @var string
     */
    public $subject_name = '';

    /**
     * Подключать языковой файл для поля?
     *
     * @var boolean
     */
    protected $use_language = false;

    /**
     * Последняя ошибка поля
     * @var mixed
     */
    protected $last_error;

    /**
     * @param string $name Имя поля
     * @param array $options Массив опций
     */
	public function __construct($name, $options = false) {

        $this->lang = cmsConfig::get('language');

        if($name){
            $this->setName($name);
        }

        $this->field_type = substr(mb_strtolower(get_called_class()), 5);
        $this->class = $this->field_type;

        if($this->use_language){
            cmsCore::loadFieldLanguage($this->field_type);
        }

        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Возвращает последнюю ошибку поля
     *
     * @return mixed
     */
    public function getError() {
        return $this->last_error;
    }

    /**
     * Устанавливает ошибку поля
     *
     * @param mixed $last_error
     * @return $last_error
     */
    public function setError($last_error) {
        $this->last_error = $last_error;
        return $last_error;
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
    public function getOptions() { return []; }

    public function getOptionsExtended() {

        $ext_options = [];

        $options = $this->getOptions();

        if($options){
            foreach ($options as $option_field) {
                if(!empty($option_field->extended_option)){
                    $ext_options[] = $option_field;
                }
            }
        }

        return $ext_options;
    }

    /**
     * Возвращает значение опции поля
     * @param string $key Имя опции
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    public function getOption($key, $default = null) {

        if (array_key_exists($key, $this->options)) {

            if(!$this->options[$key] && $default !== null){
                return $default;
            }

            return $this->options[$key];
        }

        if ($this->default_options_loaded !== true) {

            $options = $this->getOptions();

            $field_options = [];

            foreach ($options as $field) {
                $field_options[$field->getName()] = $field->getDefaultValue();
            }

            $this->options = array_merge($field_options, $this->options);

            $this->default_options_loaded = true;

            return $this->getOption($key, $default);

        } else {
            return $default;
        }
    }

    /**
     * Устанавливает все параметры для поля
     * опции и свойства
     *
     * @param array $options
     */
    public function setOptions($options) {
        if (is_array($options)) {
            foreach ($options as $option => $value) {
                if ($option === 'id') {
                    $this->field_id = $value;
                    continue;
                }
                $this->{$option} = $value;
            }
            $show_title = true;
            if (array_key_exists('show_title', ($options['options'] ?? []))) {
                $show_title = $options['options']['show_title'];
            }
            if (isset($options['title']) && $show_title) {
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

        $this->id = str_replace([':', '|'], '_', $name);

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

        if ($max_length) {
            return str_replace('{max_length}', (string)$max_length, $this->sql);
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
    public function getDefaultVarType($is_filter = false) {

        if (is_string($this->var_type)) {
            return $this->var_type;
        }

        $default_value = $this->getDefaultValue();

        if ($default_value === null) {
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
        return cmsTemplate::getInstance()->renderFormField($this->class, [
            'field' => $this,
            'value' => $value
        ]);
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
        $required_key = array_search(['required'], $this->getRules());
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
    public function parseTeaser($value) {

        $this->is_parse_teaser = true;

        return $this->parse($value);
    }

    /**
     * Выполняет некие действия после отработки метода parse
     * для всех полей одной записи
     * @param mixed $value Значение уже отформатированного поля
     * @param array $item Массив полей записи, с уже обработанными данными
     * @return mixed
     */
    public function afterParse($value, $item){ return $value; }

    /**
     * Выполняет некие действия над массивом записи
     * после всех обработок
     *
     * @param array $item
     * @param array $fields
     * @return array
     */
    public function hookItem($item, $fields){ return $item; }

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
     * Если поле работает с файлами
     * Возвращает массив путей файлов от корня CMS
     *
     * @param mixed $value
     * @return boolean | array
     */
    public function getFiles($value){
        return false;
    }

    /**
     * Метод, который подготавливает входную переменную
     * из поля для записи в базу данных
     *
     * @param mixed $value Значение поля из формы
     * @param boolean $is_submitted Форма отправлена?
     * @param mixed $old_value Предыдущее значение поля
     * @return string | array
     */
    public function store($value, $is_submitted, $old_value = null) {
        if ($this->store_array_as_json && is_array($value)) {
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

    /*
     * Метод для получения списка в полях
     */
    public function getListItems(){

        $items = [];

        if (isset($this->items)){

            $items = $this->items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $this->items = $generator($this->item, $this->request, $this);

        } else if ($this->hasDefaultValue()) {

            $this->items = (!empty($this->show_empty_value) ? ['' => ''] : [])  + string_explode_list($this->getDefaultValue());

            $list_sorting = $this->getOption('list_sorting', 'keys');

            if($list_sorting === 'keys'){
                ksort($this->items, SORT_NATURAL);
            } elseif($list_sorting === 'values') {
                asort($this->items, SORT_NATURAL);
            }

            $items = $this->items;

        } else if($this->getOption('list_where') === 'table'){

            $list_table = $this->getOption('list_table');

            if($list_table){

                $model = new cmsModel();

                $list_order = $this->getOption('list_order');
                if($list_order){

                    $ordering = explode(':', $list_order);

                    $model->orderBy($ordering[0], (!empty($ordering[1]) ? $ordering[1] : 'asc'));
                }

                $list_where_cond = $this->getOption('list_where_cond');
                if($list_where_cond){
                    $list_where_cond = json_decode($list_where_cond, true);
                    if(is_array($list_where_cond)){
                        $model->applyDatasetFilters(['filters' => $list_where_cond]);
                    }
                }

                $model->limit(false);

                $this->items = $model->selectTranslatedField($this->getOption('list_where_title'), $list_table)->
                        get($list_table, function ($item, $model){
                    return $item[$this->getOption('list_where_title')];
                }, $this->getOption('list_where_id')) ?: [];

                if(!$list_order){
                    ksort($this->items, SORT_NATURAL);
                }

                $items = $this->items;
            }
        }

        return $items;
    }

}
