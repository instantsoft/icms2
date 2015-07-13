<?php

class fieldCheckbox extends cmsFormField {

    public $title       = LANG_PARSER_CHECKBOX;
    public $sql         = 'tinyint(1) NULL DEFAULT NULL';
    public $filter_type = 'int';

    public function parse($value){
        $value = $value ? LANG_YES : LANG_NO;
        return htmlspecialchars($value);
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, 1);
    }    

}

