<?php

class fieldHidden extends cmsFormField {

    public $title       = LANG_PARSER_HIDDEN;
    public $sql         = 'varchar(255) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $var_type    = 'string';

    public function getFilterInput($value){
        return false;
    }

}
