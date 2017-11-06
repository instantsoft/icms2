<?php

class fieldListMultiple extends cmsFormField {

    public $title       = LANG_PARSER_LIST_MULTIPLE;
    public $is_public   = false;
    public $sql         = 'text NULL DEFAULT NULL';
    public $allow_index = false;
    public $var_type    = 'array';
    public $is_vertical = false;

    public function getOptions(){
        return array(
            new fieldCheckbox('show_all', array(
                'title' => LANG_PARSER_LIST_MULTIPLE_SHOW_ALL,
                'default' => 1
            )),
        );
    }

    public function getListItems(){

        $items = array();

        if (isset($this->items)){

            $items = $this->items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $generator($this->item);

        } else if ($this->hasDefaultValue()) {

            $items = string_explode_list($this->getDefaultValue());

        }

        return $items;

    }

    public function getInput($value){

        $this->data['items'] = ( $this->getProperty('show_all') ? array(0 => LANG_ALL) : array() ) + $this->getListItems();

        return parent::getInput($value ? $value : array(0));

    }

}