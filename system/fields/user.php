<?php

class fieldUser extends cmsFormField {

    public $title       = LANG_PARSER_USER;
    public $is_public   = false;
    public $sql         = 'varchar(255) NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_USER_FILTER_HINT;
    public $var_type    = 'string';
    public $allow_index = false;

    public function getInput($value) {
        return html_input('text', $this->name, $value);
    }

    public function parse($value) {
        if (!$value) {
            return '';
        }
        if (is_array($value)) {
            return '<a href="' . href_to_profile($value) . '">' . htmlspecialchars($value['nickname']) . '</a>';
        }
        return htmlspecialchars($value);
    }

    public function getStringValue($value) {
        if (!$value) {
            return '';
        }
        if (is_array($value)) {
            return htmlspecialchars($value['nickname']);
        }
        return htmlspecialchars($value);
    }

    public function applyFilter($model, $value) {

        $users_model = cmsCore::getModel('users');

        $users_ids = $users_model->filterLike('nickname', "%{$value}%")->getUsersIds();

        if (!$users_ids) {
            return $model->filterIsNull($this->name . '_id');
        } else {
            return $model->filterIn($this->name . '_id', $users_ids);
        }
    }

}
