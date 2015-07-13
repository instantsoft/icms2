<?php

class fieldCity extends cmsFormField {

    public $title   = LANG_PARSER_CITY;
    public $is_public = false;
    public $sql     = 'int(11) NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_CITY_FILTER_HINT;

    public function getInput($value) {

        if (is_numeric($value)){
            $value = $this->getCity($value);
        }

        return parent::getInput($value);

    }

    public function getFilterInput($value) {

        if (is_int($value)){
            $value = $this->getCity($value);
        }

        return parent::getFilterInput($value);

    }

    private function getCity($id){

        $city = cmsCore::getModel('geo')->getCity($id);

        $value = $city ? array(
            'id' => $city['id'],
            'name' => $city['name']
        ) : false;

        return $value;

    }

    public function parse($value){
        return htmlspecialchars($value['name']);
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, "{$value}");
    }

}
