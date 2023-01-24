<?php

class fieldListGroups extends cmsFormField {

    public $title       = LANG_PARSER_LIST_GROUPS;
    public $is_public   = false;
    public $sql         = 'text NULL DEFAULT NULL';
    public $allow_index = false;
    public $var_type    = 'array';

    public function getOptions() {

        return [
            new fieldCheckbox('show_all', [
                'title'   => LANG_PARSER_LIST_MULTIPLE_SHOW_ALL,
                'default' => 1
            ]),
            new fieldCheckbox('show_guests', [
                'title'   => LANG_PARSER_LIST_GROUPS_SHOW_GUESTS,
                'default' => 0
            ])
        ];
    }

    public function getListItems() {

        $users_model = cmsCore::getModel('users');

        $items = $this->getProperty('show_all') ? [0 => LANG_ALL] : [];

        $groups = $users_model->getGroups((bool) $this->getProperty('show_guests'));

        foreach ($groups as $group) {
            $items[$group['id']] = $group['title'];
        }

        return $items;
    }

    public function getInput($value) {

        $this->data['groups'] = $this->getListItems();

        if (!is_array($value)) {
            $value = cmsModel::yamlToArray($value);
        }

        if ($value) {
            foreach ($value as $k => $v) {
                if (is_numeric($v)) {
                    $value[$k] = (int) $v;
                }
            }
        }

        return parent::getInput($value ? $value : [0]);
    }

    public function store($value, $is_submitted, $old_value = null) {

        if (is_array($value)) {
            $value = array_filter($value);
        }

        return parent::store($value, $is_submitted, $old_value);
    }

}
