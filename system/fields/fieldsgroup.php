<?php

class fieldFieldsgroup extends cmsFormField {

    public $title       = LANG_PARSER_FIELDSGROUP;
    public $is_public   = false;
    public $sql         = 'mediumtext';
    public $allow_index = false;
    public $var_type    = 'array';

    public $childs = []; // только простой массив с последовательной нумерацией с ноля

    public function getRules() {

        $this->rules[] = ['fieldsgroup'];

        return $this->rules;
    }

    public function parse($value) {
        return '';
    }

    private function getChildForm($context_name = false) {

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        foreach ($this->childs as $field) {

            if($context_name){
                $field->setName($this->name.'::'.$field->getName());
            }

            $field->setItem($this->item);

            $form->addField($fieldset_id, $field);
        }

        return $form;
    }

    public function getInput($value) {

        $this->data['form'] = $this->getChildForm(true);

        if($value && !is_array($value)){
            $value = cmsModel::yamlToArray($value);
        }

        return parent::getInput($value);
    }

    public function store($_value, $is_submitted, $old_value = null){

        if(!$_value){
            $_value = [];
        }

        $value = [];

        $total_fields = count($this->childs);
        $total_values = count($_value);

        // Валидация
        if($total_fields > $total_values || $total_values % $total_fields !== 0){
            return $value;
        }

        foreach ($this->childs as $key => $field) {

            $name = $field->getName();

            $field->setItem($this->item);

            $value_key = 0;

            for ($i = $key; $i < $total_values; $i += $total_fields) {

                // Валидация
                if(!array_key_exists($i, $_value) || !is_array($_value[$i])){
                    return [];
                }

                // По сути упрощённый кусок из парсинга значений полей в классе форм
                $request = new cmsRequest($_value[$i]);

                $field_value = $request->get($name, null, $field->getDefaultVarType());

                if (is_null($field_value) && $field->hasDefaultValue() && !$is_submitted) { $field_value = $field->getDefaultValue(); }

                $value[$value_key][$name] = $field->store($field_value, $is_submitted, $old_value);

                $value_key++;
            }
        }

        return parent::store($value, $is_submitted, $old_value);
    }

    /**
     * Сюда придёт значение после обработки в store
     * @param string $value JSON
     * @return mixed
     */
    public function validate_fieldsgroup($value) {

        if (empty($value)) {
            return true;
        }

        $form = $this->getChildForm();

        $errors = [];

        foreach ($value as $key => $data) {

            $errors[$key] = $form->validate(new cmsController($this->request), $data);
        }

        $errors = array_filter($errors);

        return $errors ? $errors : true;
    }

}
