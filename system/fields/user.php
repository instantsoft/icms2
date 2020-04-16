<?php

class fieldUser extends cmsFormField {

    public $title       = LANG_PARSER_USER;
    public $is_public   = false;
    public $sql         = 'varchar(255) NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_USER_FILTER_HINT;
    public $allow_index = false;

    public function getInput($value) {
        return html_input('text', $this->name, $value);
    }

    public function parse($value) {
        if (is_array($value)) {
            return '<a href="' . href_to('users', $value['id']) . '">' . htmlspecialchars($value['nickname']) . '</a>';
        }
        return htmlspecialchars($value);
    }

    public function getStringValue($value) {
        if (is_array($value)) {
            return htmlspecialchars($value['nickname']);
        }
        return htmlspecialchars($value);
    }

    public function applyFilter($model, $value) {

        $users_model = cmsCore::getModel('users');

        $users = $users_model->filterLike('nickname', "%{$value}%")->getUsers();

        if (!$users) {
            return $model->filterIsNull($this->name . '_id');
        } else {
            $users_ids = array_collection_to_list($users, 'id', 'id');
            return $model->filterIn($this->name . '_id', $users_ids);
        }

    }

}
