<?php

class fieldListMultiple extends cmsFormField {

    public $title       = LANG_PARSER_LIST_MULTIPLE;
    public $is_public   = false;
    public $sql         = 'text NULL DEFAULT NULL';
    public $allow_index = false;
    public $var_type    = 'array';
    public $is_vertical = false;

    public function getOptions() {

        return [
            new fieldCheckbox('show_all', [
                'title'   => LANG_PARSER_LIST_MULTIPLE_SHOW_ALL,
                'default' => 1
            ])
        ];
    }

    public function getInput($value) {

        $this->data['items'] = ($this->getProperty('show_all') ? [0 => LANG_ALL] : []) + $this->getListItems();

        if (is_array($value) && $value) {
            foreach ($value as $k => $v) {
                if (!is_array($v) && is_numeric($v)) {
                    $value[$k] = (int) $v;
                }
            }
        }

        return parent::getInput($value ? $value : [0]);
    }

}
