<?php

class fieldColor extends cmsFormField {

    public $title   = LANG_PARSER_COLOR;
    public $sql     = 'varchar(7) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $filter_hint = '#RRGGBB';

    public function getRules() {

        $this->rules[] = array('color');

        return $this->rules;

    }

    public function parse($value){
        return '<div class="color-block" style="background-color:'.$value.'" title="'.$value.'"></div>';
    }

    public function getStringValue($value){
        return $value;
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, "{$value}");
    }

}
