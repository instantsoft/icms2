<?php

class fieldString extends cmsFormField {

    public $title   = LANG_PARSER_STRING;
    public $sql     = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = 'str';

    public function getOptions(){
        return array(
            new fieldNumber('min_length', array(
                'title' => LANG_PARSER_TEXT_MIN_LEN,
                'default' => 0
            )),
            new fieldNumber('max_length', array(
                'title' => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 255
            ))
        );
    }

    public function getRules() {

        if ($this->getOption('min_length')){
            $this->rules[] = array('min_length', $this->getOption('min_length'));
        }

        if ($this->getOption('max_length')){
            $this->rules[] = array('max_length', $this->getOption('max_length'));
        }

        return $this->rules;

    }

    public function parse($value){
        return htmlspecialchars($value);
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function store($value, $is_submitted, $old_value=null){
        if($this->getProperty('is_clean_disable') === true){
            return trim($value);
        }
        return strip_tags($value);
    }

    public function getInput($value){

        $this->data['type']         = $this->getProperty('is_password') ? 'password' : 'text';
        $this->data['autocomplete'] = $this->getProperty('autocomplete');

        return parent::getInput($value);

    }

}
