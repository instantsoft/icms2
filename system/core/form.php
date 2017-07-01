<?php

class cmsForm {

    public $is_tabbed = false;

    private $params          = array();
    private $structure       = array();
    private $disabled_fields = array();

    public function setStructure($structure=array()){
        $this->structure = $structure;
    }

    /**
     * Возвращает массив полей формы
     * @return array
     */
    public function getStructure(){
        return $this->structure;
    }

//============================================================================//
//============================================================================//

    public function setParams($params=array()){
        $this->params = $params;
    }

    public function getParams(){
        return $this->params;
    }

//============================================================================//
//============================================================================//

    public function addHtmlBlock($id, $content=null){

        $this->structure[$id] = array(
            'type' => 'html',
            'content' => $content
        );

        return $id;

    }

    public function addHtmlBlockToBeginning($id, $content=null){

        $block = array(
            'type' => 'html',
            'content' => $content
        );

        $this->structure = array($id => $block) + $this->structure;

        return $id;

    }

    public function addHtmlBlockAfter($after_id, $id, $content=null){

        $block = array(
            'type' => 'html',
            'content' => $content
        );

        $pos = array_search($after_id, array_keys($this->structure));

        $before = array_slice($this->structure, 0, $pos+1);
        $after = array_slice($this->structure, $pos + 1);

        $this->structure = $before + array($id => $block) + $after;

        return $id;

    }

//============================================================================//
//============================================================================//

    public function isFieldsetExists($id) {
        return isset($this->structure[$id]);
    }

    /**
     * Добавляет набор полей в форму.
     * Возращает id набора полей
     * @param string $title Заголовок набора полей
     * @param string $id ID набора полей
     * @return mixed
     */
    public function addFieldset($title='', $id=null, $options=array()){

        if (is_null($id)){
            $id = sizeof($this->structure);
        }

        if($this->isFieldsetExists($id)){ return $id; }

        $fieldset = array(
            'type' => 'fieldset',
            'title' => $title,
            'childs' => array()
        );

        $fieldset = array_merge($fieldset, $options);

        $this->structure[$id] = $fieldset;

        return $id;

    }

    public function addFieldsetToBeginning($title='', $id=null, $options=array()){

        if (is_null($id)){
            $id = sizeof($this->structure);
        }

        $fieldset = array(
            'type' => 'fieldset',
            'title' => $title,
            'childs' => array()
        );

        $fieldset = array_merge($fieldset, $options);

        $this->structure = array($id => $fieldset) + $this->structure;

        return $id;

    }

    public function addFieldsetAfter($after_id, $title='', $id=null, $options=array()){

        if (is_null($id)){
            $id = sizeof($this->structure);
        }

        $fieldset = array(
            'type' => 'fieldset',
            'title' => $title,
            'childs' => array()
        );

        $fieldset = array_merge($fieldset, $options);

        $pos = array_search($after_id, array_keys($this->structure));

        $before = array_slice($this->structure, 0, $pos+1);
        $after = array_slice($this->structure, $pos + 1);

        $this->structure = $before + array($id => $fieldset) + $after;

        return $id;

    }

	public function getLastFieldsetId(){

		$ids = array_keys($this->structure);

		return $ids[count($ids)-1];

	}

    /**
     * Добавляет поле в указанный набор полей формы
     * @param string $fieldset_id ID набора полей
     * @param string $name Название поля
     * @param array $params Параметры поля
     */
    public function addField($fieldset_id, $field){

        $this->structure[ $fieldset_id ]['childs'][$field->name] = $field;

    }

    public function addFieldToBeginning($fieldset_id, $field){

        $this->structure[ $fieldset_id ]['childs'] = array($field->name => $field) + $this->structure[ $fieldset_id ]['childs'];

    }

//============================================================================//
//============================================================================//

    /**
     * Изменяет аттрибут набора полей в форме
     * @param string $fieldset_id ID набора полей
     * @param string $attr_name Название аттрибута
     * @param mixed $value Новое значение
     */
    public function setFieldsetAttribute($fieldset_id, $attr_name, $value){

        $this->structure[ $fieldset_id ][ $attr_name ] = $value;

    }

    /**
     * Изменяет аттрибут поля в форме
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     * @param string $attr_name Название аттрибута
     * @param mixed $value Новое значение
     */
    public function setFieldAttribute($fieldset_id, $field_name, $attr_name, $value){
        foreach( $this->structure[ $fieldset_id ]['childs'] as $field) {
            if ($field->getName() == $field_name){
                $field->setOption($attr_name, $value);
                break;
            }
        }
    }

    public function setFieldAttributeByName($field_name, $attr_name, $value){
        foreach($this->structure as $fieldset){
            foreach( $fieldset['childs'] as $field) {
                if ($field->getName() == $field_name){
                    $field->setOption($attr_name, $value);
                    break;
                }
            }
        }
    }

    public function setFieldProperty($fieldset_id, $field_name, $attr_name, $value){
        foreach( $this->structure[ $fieldset_id ]['childs'] as $field) {
            if ($field->getName() == $field_name){
                $field->{$attr_name} = $value;
                break;
            }
        }
    }
//============================================================================//
//============================================================================//

    /**
     * Скрывает набор полей в форме
     * @param string $fieldset_id ID набора полей
     */
    public function hideFieldset($fieldset_id){

        $this->setFieldsetAttribute($fieldset_id, 'is_hidden', true);

    }

    /**
     * Скрывает поле в форме
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     */
    public function hideField($fieldset_id, $field_name = ''){

        if ($fieldset_id && empty($field_name)){
            $this->setFieldAttributeByName($fieldset_id, 'is_hidden', true);
            return;
        }

        $this->setFieldAttribute($fieldset_id, $field_name, 'is_hidden', true);

    }

//============================================================================//
//============================================================================//

    /**
     * Убирает из набора все поля
     * @param string $fieldset_id ID набора полей
     */
    public function clearFieldset($fieldset_id){
        $this->structure[ $fieldset_id ]['childs'] = array();
    }

    /**
     * Удаляет набор полей из формы
     * @param string $fieldset_id ID набора полей
     */
    public function removeFieldset($fieldset_id){
        unset($this->structure[ $fieldset_id ]);
    }

    /**
     * Удаляет поле из формы
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     */
    public function removeField($fieldset_id, $field_name){
        foreach( $this->structure[ $fieldset_id ]['childs'] as $field_id => $field) {
            if ($field->getName() == $field_name){
                unset($this->structure[ $fieldset_id ]['childs'][ $field_id ]);
                break;
            }
        }
    }

    /**
     * Отключает поле в форме
     * Поле не удаляется, но перестает участвовать в парсинге и валидации
     *
     * @param string $field_name Название поля
     */
    public function disableField($field_name){
        $this->disabled_fields[] = $field_name;
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает массив полей формы, заполнив их значениями переданными в запросе $request
     * @param cmsRequest $request
     * @param bool $is_submitted
     * @param array $item
     * @return array
     */
    public function parse($request, $is_submitted=false, $item=false){

        $result = array();

        foreach($this->structure as $fieldset){

            if (!isset($fieldset['childs'])) { continue; }

            foreach($fieldset['childs'] as $field){

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

                $field->setItem($item);

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

        return $result;

    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет соответствие массива $data правилам
     * валидации указанным для полей формы
     * @param cmsController $controller
     * @param array $data
     * @param bool $is_check_csrf
     * @return bool Если ошибки не найдены, возвращает false
     */
    public function validate($controller, $data, $is_check_csrf = true){

        $errors = array();

        //
        // Проверяем CSRF-token
        //
        if ($is_check_csrf){
            $csrf_token = $controller->request->get('csrf_token', '');
            if ( !self::validateCSRFToken( $csrf_token ) ){
                return true;
            }
        }

        //
        // Перебираем поля формы
        //
        foreach($this->structure as $fieldset){

            if (!isset($fieldset['childs'])) { continue; }

            foreach($fieldset['childs'] as $field){

                $name = $field->getName();

                // если поле отключено, пропускаем поле
                if (in_array($name, $this->disabled_fields)){ continue; }

                // правила
                $rules = $field->getRules();

                // если нет правил, пропускаем поле
                if (!$rules){ continue; }

                // проверяем является ли поле элементом массива
                $is_array = strpos($name, ':');

                //
                // получаем значение поля из массива данных
                //
                if ($is_array === false){
                    $value = array_key_exists($name, $data) ? $data[$name] : '';
                }

                if ($is_array !== false){
                    $value = array_value_recursive($name, $data);
                }

                if ($data) { $field->setItem($data); }

                //
                // перебираем правила для поля
                // и проверяем каждое из них
                //
                foreach($rules as $rule){

                    if (!$rule) { continue; }

                    // каждое правило это массив
                    // первый элемент - название функции-валидатора
                    $validate_function = "validate_{$rule[0]}";

                    // к остальным элементам добавляем $value, т.к.
                    // в валидаторах $value всегда последний аргумент
                    $rule[] = $value;

                    // убираем название валидатора из массива,
                    // оставляем только параметры (аргументы)
                    unset($rule[0]);

                    // вызываем валидатор и объединяем результат
                    // с предыдущими
                    $result = call_user_func_array(array($controller, $validate_function), $rule);

                    // если получилось false, то дальше не проверяем, т.к.
                    // ошибка уже найдена
                    if ($result !== true) {
                        $errors[$name] = $result;
                        break;
                    }

                }

            }

        }

        if (!sizeof($errors)) { return false; }

        return $errors;

    }

//============================================================================//
//============================================================================//

    public static function getCSRFToken(){

        if (cmsUser::isSessionSet('csrf_token')){
            return cmsUser::sessionGet('csrf_token');
        }

        return self::generateCSRFToken();

    }

    /**
     * Создает, сохраняет в сессии и возвращает CSRF-token
     * @return string
     */
    public static function generateCSRFToken(){

        $hash = implode('::', array(session_id(), uniqid(), microtime(true)));
        $token = md5($hash);

        cmsUser::sessionSet('csrf_token', $token);

        return $token;

    }

    /**
     * Проверяет валидность CSRF-токена
     * @param string $csrf_token
     * @return bool
     */
    public static function validateCSRFToken($csrf_token){
        return (cmsUser::sessionGet('csrf_token') === $csrf_token);
    }

//============================================================================//
//============================================================================//

    public static function mapFieldsToFieldsets($fields, $callback=null, $values=null){

        $fieldsets = array();

        $current = null;

        $index = 0;

        $fieldsets[ $index ] = array(
            'title' => $current,
            'fields' => array()
        );

        $user = cmsUser::getInstance();

        foreach($fields as $field){

            if (is_callable($callback)){
                if (!$callback( $field, $user )) { continue; }
            }

            if (is_array($values)){
                if (empty($values[ $field['name'] ])){ continue; }
            }

            if ($current != $field['fieldset']){

                $current = $field['fieldset'];
                $index += 1;

                $fieldsets[ $index ] = array(
                    'title' => $current,
                    'fields' => array()
                );

            }

            $fieldsets[ $index ]['fields'][] = $field;

        }

        return $fieldsets;


    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает список всех имеющихся типов полей
     * @return array
     */
    public static function getAvailableFormFields($only_public = true){

        $fields_types   = array();
        $fields_files   = cmsCore::getFilesList('system/fields', '*.php', true, true);

        foreach ($fields_files as $name) {

            $class  = 'field' . string_to_camel('_', $name);

            $field = new $class(null, null);

            if ($only_public && !$field->is_public){ continue; }

            $fields_types[$name] = $field->getTitle();

        }

        return $fields_types;

    }

    public static function getForm($form_file, $form_name, $params=false){

        if (!file_exists($form_file)){ return false; }

        include_once $form_file;

        $form_class = 'form' . string_to_camel('_', $form_name);

        $form = new $form_class();

        if ($params){
            $form->setParams($params);
            $form->setStructure( call_user_func_array(array($form, 'init'), $params) );
        } else {
            $form->setStructure( $form->init() );
        }

        return $form;

    }

//============================================================================//
//============================================================================//

}