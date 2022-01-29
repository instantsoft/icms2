<?php

class fieldHidden extends cmsFormField {

    public $title        = LANG_PARSER_HIDDEN;
    public $sql          = 'varchar(255) NULL DEFAULT NULL';
    public $filter_type  = 'str';
    public $var_type     = 'string';
    public $show_id_attr = true;

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        return html($value, false);
    }

    public function getFilterInput($value) {
        return '';
    }

    public function getInput($value) {

        $this->data['dom_attr'] = [];

        if ($this->show_id_attr) {
            $this->data['dom_attr'] = ['id' => $this->id];
        }

        return parent::getInput($value);
    }

}
