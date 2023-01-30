<?php

class fieldCheckbox extends cmsFormField {

    public $title       = LANG_PARSER_CHECKBOX;
    public $sql         = 'TINYINT(1) UNSIGNED NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $var_type    = 'integer';

    public function parse($value) {
        return ($value ? LANG_YES : LANG_NO);
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, 1);
    }

    public function getInput($value) {

        $this->data['attributes']             = $this->getProperty('attributes') ?: [];
        $this->data['attributes']['id']       = $this->id;
        $this->data['attributes']['required'] = (array_search(['required'], $this->getRules()) !== false);

        if (empty($this->data['attributes']['class'])) {
            $this->data['attributes']['class'] = 'custom-control-input';
        } else {
            $this->data['attributes']['class'] .= ' custom-control-input';
        }

        return parent::getInput($value);
    }

}
