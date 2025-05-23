<?php

class fieldFieldsgroup extends cmsFormField {

    public $title       = LANG_PARSER_FIELDSGROUP;
    public $is_public   = false;
    public $sql         = 'mediumtext';
    public $allow_index = false;
    public $var_type    = 'array';

    /**
     * Массив полей
     * только простой массив с последовательной нумерацией с ноля
     *
     * @var array
     */
    public $childs = [];
    /**
     * Может динамически добавлять
     * новую строку полей из $childs
     *
     * @var bool
     */
    public $is_dynamic = true;
    /**
     * Нумерует каждую строку полей
     *
     * @var bool
     */
    public $is_counter_list = false;

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

            $form_field = clone $field;

            if ($context_name) {

                $separator = $this->is_dynamic ? '::' : ':';

                $form_field->setName($this->name . $separator . $form_field->getName());
            }

            $form_field->setItem($this->item);

            $form->addField($fieldset_id, $form_field);
        }

        return $form;
    }

    public function getInput($value) {

        $this->data['form']            = $this->getChildForm(true);
        $this->data['is_counter_list'] = $this->is_counter_list;
        $this->data['is_dynamic']      = $this->is_dynamic;

        return parent::getInput(cmsModel::yamlToArray($value));
    }

    public function store($_value, $is_submitted, $old_value = null) {

        if (!$_value) {
            $_value = [];
        }

        $value = [];

        $total_fields = count($this->childs);
        $total_values = count($_value);

        // Валидация для динамического списка
        if ($this->is_dynamic) {
            if ($total_fields > $total_values || $total_values % $total_fields !== 0) {
                return $value;
            }
        }

        foreach ($this->childs as $key => $field) {

            $name = $field->getName();

            $field->setItem($this->item);

            if ($this->is_dynamic) {

                $value_key = 0;

                for ($i = $key; $i < $total_values; $i += $total_fields) {

                    // Валидация
                    if (!array_key_exists($i, $_value) || !is_array($_value[$i])) {
                        return [];
                    }

                    $value[$value_key][$name] = $this->getChildFieldValue($field, $_value[$i], $is_submitted);

                    $value_key++;
                }

            } else {
                $value[$name] = $this->getChildFieldValue($field, $_value, $is_submitted);
            }
        }

        return parent::store($value, $is_submitted, $old_value);
    }

    private function getChildFieldValue($field, $data, $is_submitted) {

        $request = new cmsRequest($data);

        $field_value = $request->get($field->getName(), null, $field->getDefaultVarType());

        if (is_null($field_value) && $field->hasDefaultValue() && !$is_submitted) {
            $field_value = $field->getDefaultValue();
        }

        return $field->store($field_value, $is_submitted);
    }

    /**
     * Сюда придёт значение после обработки в store
     *
     * @param array $value
     * @return mixed
     */
    public function validate_fieldsgroup($value) {

        if (!is_array($value)) {
            return ERR_VALIDATE_INVALID;
        }

        $form = $this->getChildForm();

        $errors = [];

        if ($this->is_dynamic) {

            if (empty($value)) {
                return true;
            }

            foreach ($value as $key => $data) {
                $errors[$key] = $form->validate(new cmsController($this->request), $data, false);
            }

            $errors = array_filter($errors);

        } else {
            $errors = $form->validate(new cmsController($this->request), $value, false);
        }

        return $errors ? $errors : true;
    }

}
