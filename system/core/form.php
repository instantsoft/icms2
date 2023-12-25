<?php
/**
 * Базовый класс для работы с формами в CMS
 */
class cmsForm {

    /**
     * Форма с табами?
     *
     * @var boolean
     */
    public $is_tabbed = false;

    /**
     * Показывать предупреждение о несохранённых данных формы
     * при уходе со страницы формы
     *
     * @var boolean
     */
    public $show_unsave_notice = true;

    /**
     * Параметры формы, передающиеся в метод init
     *
     * @var array
     */
    private $params = [];

    /**
     * Структура формы
     *
     * @var array
     */
    private $structure = [];

    /**
     * Массив имён отключенных полей формы
     * Они не участвуют в валидации,
     * не участвуют в парсинге формы
     *
     * @var array
     */
    protected $disabled_fields = [];

    /**
     * Объект контроллера контекста вызова формы
     *
     * @var object
     */
    protected $controller;

    /**
     * Массив любых данных, необходимых для формы
     *
     * @var array
     */
    protected $data = [];

    /**
     * Имя формы
     * @var string
     */
    protected $name = '';

    public function __construct() {
        $this->name = strtolower(get_called_class());
    }

    /**
     * Возвращает имя формы
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Устанавливает массив параметров формы, которые передются
     * в метод init формы аргументами
     *
     * @param array $params
     */
    public function setParams($params = []) {
        $this->params = $params;
    }

    /**
     * Возвращает массив параметров формы
     *
     * @return array
     */
    public function getParams(){
        return $this->params;
    }

    /**
     * Устанавливает вспомогательные данные
     *
     * @param string $key Ключ
     * @param mixed $value Значение
     * @return \cmsForm
     */
    public function setData($key, $value) {
        $this->data[$key] = $value; return $this;
    }

    /**
     * Возвращает данные по ключу
     *
     * @param string $key Ключ
     * @return mixed
     */
    public function getData($key) {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }

    /**
     * Сохраняет ссылку на контроллер контекста
     *
     * @param object $controller_obj
     * @return \cmsForm
     */
    public function setContext($controller_obj){

        $this->controller = $controller_obj;

        $this->name .= '_'.$this->controller->name;

        return $this;
    }

    /**
     * Возвращает объект контроллера контекста
     *
     * @return object
     */
    public function getContext(){
        return $this->controller;
    }

//============================================================================//
//========================= Работа со структурой =============================//

    /**
     * Объединяет форму с переданной
     * заменяя наборы полей при совпадении ключей
     *
     * @param cmsForm $form
     * @return void
     */
    public function mergeForm(cmsForm $form) {

        $structure = $form->getStructure();

        foreach($structure as $fid => $fieldset){
            $this->addStructure($fid, $fieldset, true);
        }
    }

    /**
     * Устанавливает массив полей формы
     *
     * @param array $structure
     * @return \cmsForm
     */
    public function setStructure($structure = []) {
        $this->structure = $structure; return $this;
    }

    /**
     * Возвращает массив полей формы "как есть"
     *
     * @return array
     */
    public function getStructure(){
        return $this->structure;
    }

    /**
     * Возвращает подготовленный массив полей формы
     *
     * @param array $item Массив данных формы
     * @return array
     */
    public function getFormStructure($item = null){

        $default_lang = cmsConfig::get('language');
        $is_change_lang = cmsConfig::get('is_user_change_lang');

        $langs = cmsCore::getLanguages();

        $structure = $this->structure;

        foreach($structure as $fid => $fieldset){

            if (!isset($fieldset['childs'])) { continue; }

            $childs = [];

            foreach($fieldset['childs'] as $id => $field){

                if($item){
                    $field->setItem($item);
                }

                $name = $field->getName();

                if(!empty($field->patterns_hint['patterns'])) {

                    $field->patterns_hint['pattern_fields'] = [];

                    $wrap_symbols = empty($field->patterns_hint['wrap_symbols']) ? ['{','}'] : $field->patterns_hint['wrap_symbols'];

                    foreach($field->patterns_hint['patterns'] as $pattern => $p_title){
                        if(is_numeric($pattern)){
                            $field->patterns_hint['pattern_fields'][] = '<a href="#">'.$wrap_symbols[0].$p_title.$wrap_symbols[1].'</a>';
                        } else {
                            $field->patterns_hint['pattern_fields'][] = '<a title="'.html($p_title, false).'" href="#">'.$wrap_symbols[0].$pattern.$wrap_symbols[1].'</a>';
                        }
                    }

                }

                // проверяем является ли поле элементом массива
                $is_array = strpos($name, ':');

                $field->classes = [
                    'field' . ($field->css_class ? ' ' . $field->css_class : ''),
                    'ft_' . strtolower(substr(get_class($field), 5))
                ];

                if(array_search(['required'], $field->rules) !== false){ $field->classes[] = 'reguired_field'; }

                if (!empty($field->groups_edit)){
                    if (!in_array(0, $field->groups_edit)){
                        $field->classes[] = 'groups-limit';
                        foreach($field->groups_edit as $group_id){
                            $field->classes[] = 'group-' . $group_id;
                        }
                    }
                }

                $field->styles = [];

                if (isset($field->is_visible)){
                    if (!$field->is_visible){
                        $field->styles[] = 'display:none';
                    }
                }

                if($field->visible_depend){
                     $field->classes[] = 'child_field';
                }

                $is_multilanguage = $field->multilanguage && $is_change_lang;

                if($is_multilanguage){
                     $field->classes[] = 'multilanguage';
                }

                if(!$is_multilanguage || $field->is_hidden || $field->getOption('is_hidden')){

                    $childs[] = $field;

                    continue;
                }

                $_childs = []; $first = true;

                foreach ($langs as $lang) {

                    $_field = clone $field;

                    $_field->lang = $lang;

                    if($default_lang !== $lang) {

                        if(!empty($_field->multilanguage_params['unset_required'])){

                            $required_key = array_search(['required'], $_field->getRules());
                            if($required_key !== false){
                                unset($_field->rules[$required_key]);
                            }
                            $class_key = array_search('reguired_field', $_field->classes);
                            if($class_key !== false){
                                unset($_field->classes[$class_key]);
                            }
                        }

                        if($is_array){

                            $name_parts = explode(':', $name);

                            $count = count($name_parts);

                            $name_parts[$count-($count > 2 ? 2 : 1)] .= '_'.$lang;

                            $_field->setName(implode(':', $name_parts));

                        } else {
                            $_field->setName($name.'_'.$lang);
                        }

                    } else {

                        $_field->classes[] = 'multilanguage-base';
                    }

                    if(!$first){
                        $_field->styles[] = 'display:none';
                    }

                    $_field->element_title = $_field->title.' ['.strtoupper($lang).']';

                    $_field->rel = $lang;

                    $_field->field_tab_title = strtoupper($lang);

                    $_childs[$lang] = $_field;

                    $first = false;
                }

                $childs[] = $_childs;
            }

            $structure[$fid]['childs'] = $childs;
        }

        return $structure;
    }

    /**
     * Возвращает поле с указанным именем, или null если такого поля нет в форме
     *
     * @param string $name Имя поля
     * @param string $fieldset_id ID набора полей (не указано - поиск по всем наборам формы)
     * @return cmsFormField|null
     */
    public function getField($name, $fieldset_id = null) {
        if ($fieldset_id !== null) {
            if ($this->isFieldsetExists($fieldset_id)) {
                foreach ($this->structure[$fieldset_id]['childs'] as $field) {
                    if ($field->getName() == $name) { return $field; }
                }
            }
        } else {
            foreach ($this->structure as $fieldset_id => $fieldset) {
                foreach ($fieldset['childs'] as $field) {
                    if ($field->getName() == $name) { return $field; }
                }
            }
        }
        return null;
    }

    /**
     * Присутствует ли поле с указанным именем в форме
     *
     * @param string $name Имя поля
     * @param string $fieldset_id ID набора полей
     * @return boolean
     */
    public function hasField($name, $fieldset_id = null) {
        return $this->getField($name, $fieldset_id) !== null;
    }

    /**
     * Проверяет, существует ли набор
     *
     * @param string $id ID набора
     * @return boolean
     */
    public function isFieldsetExists($id) {
        return isset($this->structure[$id]);
    }

    /**
     * Возвращает ID последнего набора
     *
     * @return string
     */
    public function getLastFieldsetId(){

		$ids = array_keys($this->structure);

		return $ids[count($ids)-1];
	}

    /**
     * Добавляет набор в конец формы
     *
     * @param string $id ID добавляемого набора
     * @param array $structure
     * @param boolean $is_overwrite
     * @return string $id
     */
    private function addStructure($id, $structure, $is_overwrite = false) {

        if ($id === null) {
            $id = count($this->structure);
        }

        if (!$is_overwrite && $this->isFieldsetExists($id)) {
            return $id;
        }

        $this->structure[$id] = $structure;

        return $id;
    }

    /**
     * Добавляет набор в начало формы
     *
     * @param string $id ID добавляемого набора
     * @param array $structure
     * @return string $id
     */
    private function addStructureToBeginning($id, $structure) {

        if ($id === null) {
            $id = count($this->structure);
        }

        if ($this->isFieldsetExists($id)) {
            return $id;
        }

        $this->structure = [$id => $structure] + $this->structure;

        return $id;
    }

    /**
     * Добавляет набор после заданного
     *
     * @param string $after_id ID набора, после которого вставить
     * @param string $id ID добавляемого набора
     * @param array $structure
     * @return string|null $id
     */
    private function addStructureAfter($after_id, $id, $structure) {

        if ($id === null) {
            $id = count($this->structure);
        }

        if ($this->isFieldsetExists($id)) {
            return $id;
        }

        $pos = array_search($after_id, array_keys($this->structure), true);

        if($pos !== false){

            $before = array_slice($this->structure, 0, $pos + 1, true);
            $after  = array_slice($this->structure, $pos + 1, null, true);

            $this->structure = $before + array($id => $structure) + $after;


            return $id;
        }

        return null;
    }

    /**
     * Убирает из набора все поля
     *
     * @param string $fieldset_id ID набора полей
     * @return cmsForm
     */
    public function clearFieldset($fieldset_id) {
        $this->structure[$fieldset_id]['childs'] = [];
        return $this;
    }

    /**
     * Удаляет набор полей из формы
     *
     * @param string $fieldset_id ID набора полей
     * @return cmsForm
     */
    public function removeFieldset($fieldset_id) {
        unset($this->structure[$fieldset_id]);
        return $this;
    }

    /**
     * Удаляет поле из формы
     *
     * @param string $fieldset_id ID набора полей, null - поиск по всем
     * @param string $field_name Название поля
     * @return cmsForm
     */
    public function removeField($fieldset_id, $field_name) {
        if ($fieldset_id !== null) {
            if ($this->isFieldsetExists($fieldset_id)) {
                foreach ($this->structure[$fieldset_id]['childs'] as $field_id => $field) {
                    if ($field->getName() == $field_name) {
                        unset($this->structure[$fieldset_id]['childs'][$field_id]);
                        break;
                    }
                }
            }
        } else {
            foreach ($this->structure as $fieldset_id => $fieldset) {
                foreach ($fieldset['childs'] as $field_id => $field) {
                    if ($field->getName() == $field_name) {
                        unset($this->structure[$fieldset_id]['childs'][$field_id]);
                        break;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Отключает поле в форме
     * Поле не удаляется, но перестает участвовать в парсинге и валидации
     *
     * @param string $field_name Название поля
     * @return cmsForm
     */
    public function disableField($field_name) {
        $this->disabled_fields[] = $field_name;
        return $this;
    }

//============================================================================//
//========================= HTML наборы ======================================//

    /**
     * Добавляет набор в виде сформированного HTML
     *
     * @param string $id ID набора
     * @param string $content HTML код
     * @return string ID набора
     */
    public function addHtmlBlock($id, $content = null) {

        return $this->addStructure($id, [
            'type'    => 'html',
            'content' => $content
        ]);
    }

    /**
     * Добавляет набор в виде сформированного HTML в начало формы
     *
     * @param string $id ID набора
     * @param string $content HTML код
     * @return string ID набора
     */
    public function addHtmlBlockToBeginning($id, $content = null) {

        return $this->addStructureToBeginning($id, [
            'type'    => 'html',
            'content' => $content
        ]);
    }

    /**
     * Добавляет набор в виде сформированного HTML после заданного
     *
     * @param string $after_id ID набора, после которого вставить
     * @param string $id ID набора
     * @param string $content HTML код
     * @return string ID набора
     */
    public function addHtmlBlockAfter($after_id, $id, $content = null) {

        return $this->addStructureAfter($after_id, $id, [
            'type'    => 'html',
            'content' => $content
        ]);
    }

//============================================================================//
//========================= Наборы полей =====================================//

    /**
     * Добавляет набор полей в форму
     *
     * @param string $title Заголовок набора полей
     * @param string $id ID набора полей
     * @param array $options Массив данных и опций набора
     * @return string id набора
     */
    public function addFieldset($title = '', $id = null, $options = []) {

        return $this->addStructure($id, array_merge([
            'type'   => 'fieldset',
            'title'  => $title,
            'childs' => []
        ], $options));
    }

    /**
     * Добавляет набор полей в начало формы
     *
     * @param string $title Заголовок набора полей
     * @param string $id ID набора полей
     * @param array $options Массив данных и опций набора
     * @return string id набора
     */
    public function addFieldsetToBeginning($title = '', $id = null, $options = []) {

        return $this->addStructureToBeginning($id, array_merge([
            'type'   => 'fieldset',
            'title'  => $title,
            'childs' => []
        ], $options));
    }

    /**
     *
     * Добавляет набор полей после заданного
     *
     * @param string $after_id ID набора, после которого вставить
     * @param string $title Заголовок набора полей
     * @param string $id ID набора
     * @param array $options Массив данных и опций набора
     * @return string ID набора
     */
    public function addFieldsetAfter($after_id, $title = '', $id = null, $options = []) {

        return $this->addStructureAfter($after_id, $id, array_merge([
            'type'   => 'fieldset',
            'title'  => $title,
            'childs' => []
        ], $options));
    }

//============================================================================//
//========================= Поля наборов =====================================//

    /**
     * Добавляет поле в конец набора полей
     *
     * @param string $fieldset_id ID набора полей
     * @param object $field Объект поля
     * @return cmsForm
     */
    public function addField($fieldset_id, $field) {

        $this->structure[$fieldset_id]['childs'][$field->name] = $field;

        return $this;
    }

    /**
     * Добавляет поле в начало набора полей
     *
     * @param string $fieldset_id ID набора полей
     * @param object $field Объект поля
     * @return cmsForm
     */
    public function addFieldToBeginning($fieldset_id, $field) {

        $this->structure[$fieldset_id]['childs'] = [$field->name => $field] + $this->structure[$fieldset_id]['childs'];

        return $this;
    }

    /**
     * Добавляет поле после заданного в $after_id
     *
     * @param string $after_id ID поля, после которого нужно добавить
     * @param string $fieldset_id ID набора полей
     * @param object $field Объект поля
     * @return cmsForm
     */
    public function addFieldAfter($after_id, $fieldset_id, $field) {

        $pos = array_search($after_id, array_keys($this->structure[$fieldset_id]['childs']), true);

        if ($pos === false) {
            return $this;
        }

        $before = array_slice($this->structure[$fieldset_id]['childs'], 0, $pos + 1, true);
        $after  = array_slice($this->structure[$fieldset_id]['childs'], $pos + 1, null, true);

        $this->structure[$fieldset_id]['childs'] = $before + [$field->name => $field] + $after;

        return $this;
    }

//============================================================================//
//===================== Изменение опций и свойств ============================//

    /**
     * Изменяет атрибут набора полей в форме
     *
     * @param string $fieldset_id ID набора полей
     * @param string $attr_name Название атрибута
     * @param mixed $value Новое значение
     * @return cmsForm
     */
    public function setFieldsetAttribute($fieldset_id, $attr_name, $value) {

        if(isset($this->structure[$fieldset_id][$attr_name])){
            $this->structure[$fieldset_id][$attr_name] = $value;
        }

        return $this;
    }

    /**
     * Изменяет по ID набора и по имени опцию поля в форме
     *
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     * @param string $attr_name Имя опции
     * @param mixed $value Новое значение
     * @return cmsForm
     */
    public function setFieldAttribute($fieldset_id, $field_name, $attr_name, $value){
        $field = $this->getField($field_name, $fieldset_id);
        if ($field) { $field->setOption($attr_name, $value); }
        return $this;
    }

    /**
     * Изменяет по имени опцию поля в форме
     *
     * @param string $field_name Название поля
     * @param string $attr_name Имя опции
     * @param mixed $value Новое значение
     * @return cmsForm
     */
    public function setFieldAttributeByName($field_name, $attr_name, $value){
        return $this->setFieldAttribute(null, $field_name, $attr_name, $value);
    }

    /**
     * Изменяет по ID набора и по имени свойство поля в форме
     *
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     * @param string $attr_name Имя опции
     * @param mixed $value Новое значение
     * @return cmsForm
     */
    public function setFieldProperty($fieldset_id, $field_name, $attr_name, $value){
        $field = $this->getField($field_name, $fieldset_id);
        if ($field) { $field->{$attr_name} = $value; }
        return $this;
    }

    /**
     * Скрывает набор полей в форме
     *
     * @param string $fieldset_id ID набора полей
     * @return cmsForm
     */
    public function hideFieldset($fieldset_id){
        return $this->setFieldsetAttribute($fieldset_id, 'is_hidden', true);
    }

    /**
     * Скрывает поле в форме
     *
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     * @return cmsForm
     */
    public function hideField($fieldset_id, $field_name = '') {

        // Если передан только первый параметр, считаем что это $field_name
        if ($fieldset_id && empty($field_name)) {
            return $this->setFieldAttributeByName($fieldset_id, 'is_hidden', true);
        }

        return $this->setFieldAttribute($fieldset_id, $field_name, 'is_hidden', true);
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает массив полей формы, заполнив их значениями переданными в запросе $request
     *
     * @param object $request Объект cmsRequest
     * @param boolean $is_submitted Форма отправлена?
     * @param array $item Массив предыдущих значений формы
     * @return array
     */
    public function parse($request, $is_submitted = false, $item = null) {

        $result = [];

        foreach($this->getFormStructure($item) as $fieldset){

            if (!isset($fieldset['childs'])) { continue; }

            foreach($fieldset['childs'] as $field){

                if(!is_array($field)){ $_field = [$field]; } else { $_field = $field; }

                foreach ($_field as $field) {

                    $name = $field->getName();

                    // если поле отключено, пропускаем поле
                    if (in_array($name, $this->disabled_fields)){ continue; }

                    $is_array = strpos($name, ':');

                    $value = $request->get($name, null, $field->getDefaultVarType());

                    if (is_null($value) && $field->hasDefaultValue() && !$is_submitted) { $value = $field->getDefaultValue(); }

                    $old_value = null;

                    if($item){
                        if ($is_array === false){
                            $old_value = array_key_exists($name, $item) ? $item[$name] : null;
                        }
                        if ($is_array !== false){
                            $old_value = array_value_recursive($name, $item);
                        }
                    }

                    $field->request = $request;

                    $value = $field->store($value, $is_submitted, $old_value);
                    if ($value === false) { continue; }

                    if ($is_array === false){
                        $result[$name] = $value;
                    }

                    if ($is_array !== false){
                        $result = set_array_value_recursive($name, $result, $value);
                    }

                    // если нужна денормализация
                    if($is_submitted && $field->is_denormalization){

                        $d_name = $field->getDenormalName();

                        if ($is_array === false){
                            $result[$d_name] = $field->storeCachedValue($value);
                        } else {
                            $result = set_array_value_recursive($d_name, $result, $field->storeCachedValue($value));
                        }

                    }

                }

            }

        }

        return $result;
    }

    /**
     * Проверяет соответствие массива $data правилам
     * валидации указанным для полей формы
     *
     * @param object $controller Объект cmsController
     * @param array $data Данные, полученные из формы
     * @param boolean $is_check_csrf Проверять валидность csrf токена
     * @return boolean|array Если ошибки не найдены, возвращает false
     *                       Если ошибки найдены, возвращает их массив
     *                       в формате [имя_поля => текст_ошибки]
     */
    public function validate($controller, $data, $is_check_csrf = true) {

        $errors = [];

        //
        // Проверяем CSRF-token
        //
        if ($is_check_csrf) {
            $csrf_token = $controller->request->get('csrf_token', '');
            if (!self::validateCSRFToken($csrf_token)) {
                return ['csrf_token' => ERR_VALIDATE_INVALID];
            }
        }

        //
        // Перебираем поля формы
        //
        foreach ($this->getFormStructure($data) as $fieldset) {

            if (!isset($fieldset['childs'])) {
                continue;
            }

            foreach ($fieldset['childs'] as $field) {

                if (!is_array($field)) {
                    $_field = [$field];
                } else {
                    $_field = $field;
                }

                foreach ($_field as $field) {

                    $name = $field->getName();

                    // если поле отключено, пропускаем поле
                    if (in_array($name, $this->disabled_fields)) {
                        continue;
                    }

                    // правила
                    $rules = $field->getRules();

                    // если нет правил, пропускаем поле
                    if (!$rules) {
                        continue;
                    }

                    // проверяем является ли поле элементом массива
                    $is_array = strpos($name, ':');

                    //
                    // получаем значение поля из массива данных
                    //
                    if ($is_array === false) {
                        $value = array_key_exists($name, $data) ? $data[$name] : '';
                    }

                    if ($is_array !== false) {
                        $value = array_value_recursive($name, $data);
                    }

                    //
                    // перебираем правила для поля
                    // и проверяем каждое из них
                    //
                    foreach ($rules as $rule) {

                        if (!$rule) {
                            continue;
                        }

                        // если правило - это колбэк
                        if (is_callable($rule[0]) && ($rule[0] instanceof Closure)) {

                            $result = $rule[0]($controller, $data, $value);

                            if ($result !== true) {
                                $errors[$name] = $field->setError($result);
                                break;
                            }

                            continue;
                        }

                        // каждое правило это массив
                        // первый элемент - название функции-валидатора
                        $validate_function = "validate_{$rule[0]}";

                        // к остальным элементам добавляем $value, т.к.
                        // в валидаторах $value всегда последний аргумент
                        $rule[] = $value;

                        // убираем название валидатора из массива,
                        // оставляем только параметры (аргументы)
                        unset($rule[0]);

                        // вызываем валидатор и объединяем результат с предыдущими
                        // методы валидации могут быть определены (в порядке приоритета):
                        // в классе формы, в классе поля, в контроллерах
                        if (method_exists($this, $validate_function)) {
                            $result = call_user_func_array([$this, $validate_function], $rule);
                        } elseif (method_exists($field, $validate_function)) {
                            $result = call_user_func_array([$field, $validate_function], $rule);
                        } else {
                            $result = call_user_func_array([$controller, $validate_function], $rule);
                        }

                        // если получилось false, то дальше не проверяем, т.к.
                        // ошибка уже найдена
                        if ($result !== true) {
                            $errors[$name] = $field->setError($result);
                            break;
                        }
                    }
                }
            }
        }

        return $errors;
    }

//============================================================================//
//============================= CSRF =========================================//

    /**
     * Возвращает CSRF токен
     * Это основной метод для его получения
     *
     * @return string
     */
    public static function getCSRFToken(){

        if (cmsUser::isSessionSet('csrf_token')){
            return cmsUser::sessionGet('csrf_token');
        }

        return self::generateCSRFToken();
    }

    /**
     * Создает, сохраняет в сессии и возвращает CSRF токен
     *
     * @return string
     */
    public static function generateCSRFToken(){

        $hash = implode('::', array(session_id(), microtime(true)));

        if(function_exists('hash') && in_array('sha256', hash_algos())){
            $token = hash('sha256', $hash);
        } else {
            $token = md5($hash);
        }

        cmsUser::sessionSet('csrf_token', $token);

        return $token;
    }

    /**
     * Проверяет валидность CSRF токена
     *
     * @param string $csrf_token
     * @return boolean
     */
    public static function validateCSRFToken($csrf_token){
        return (cmsUser::sessionGet('csrf_token') === $csrf_token);
    }

//============================================================================//
//============================================================================//

    /**
     * Разбивает массив полей по группам
     *
     * @param array $fields Массив полей из БД
     * @param callable $callback
     * @param array $values Массив значений полей
     * @return array
     */
    public static function mapFieldsToFieldsets($fields, $callback = null, $values = null) {

        $fieldsets = [];

        if (!$fields) { return $fieldsets; }

        $current = false;
        $index   = -1;

        $user = cmsUser::getInstance();

        foreach ($fields as $field) {

            if (is_callable($callback)) {
                if (!$callback($field, $user)) {
                    continue;
                }
            }

            if (is_array($values)) {
                if (empty($values[$field['name']])) {
                    continue;
                }
            }

            if ($current !== $field['fieldset']) {

                $current = $field['fieldset'];
                $index   += 1;

                $fieldsets[$index] = [
                    'title'  => $current,
                    'fields' => []
                ];
            }

            $fieldsets[$index]['fields'][] = $field;
        }

        return $fieldsets;
    }

    /**
     * Возвращает список всех имеющихся типов полей
     *
     * @param boolean $only_public Возвращать только публичные поля
     * @param string $controller Название контроллера для контекста вызова
     * @return array
     */
    public static function getAvailableFormFields($only_public = true, $controller = false) {

        $fields_types = [];
        $fields_files = cmsCore::getFilesList('system/fields', '*.php', true, true);

        foreach ($fields_files as $name) {

            $class = 'field' . string_to_camel('_', $name);

            $field = new $class(null, null);

            if ($only_public && !$field->is_public) {
                continue;
            }
            if ($controller && in_array($controller, $field->excluded_controllers)) {
                continue;
            }

            $fields_types[$name] = $field->getTitle();
        }

        asort($fields_types, SORT_STRING);

        return $fields_types;
    }

    /**
     * Инициализирует объект формы
     *
     * @param string $form_file Полный путь к файлу формы
     * @param string $form_name Название формы
     * @param array $params Параметры, передаваемые в метод init формы
     * @param object $controller Объект контроллера контекста
     * @return boolean|string|form_class instanceof cmsForm
     */
    public static function getForm($form_file, $form_name, $params = false, $controller = null) {

        if (!file_exists($form_file)) { return false; }

        include_once $form_file;

        $form_class = 'form' . string_to_camel('_', $form_name);

        if (!class_exists($form_class, false)) {
            return sprintf(ERR_CLASS_NOT_DEFINED, str_replace(PATH, '', $form_file), $form_class);
        }

        $form = new $form_class();

        if ($controller instanceof cmsController) {
            $form->setContext($controller);
        }

        if ($params) {
            $form->setParams($params);
            $form->setStructure(call_user_func_array([$form, 'init'], $params));
        } else {
            $form->setStructure($form->init());
        }

        return $form;
    }

}
