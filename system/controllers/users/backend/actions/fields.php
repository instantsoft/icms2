<?php

class actionUsersFields extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = '{users}_fields';
        $this->grid_name  = 'fields';
        $this->title      = LANG_USERS_CFG_FIELDS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_FIELD_ADD,
                'href'  => $this->cms_template->href_to('fields_add', ['users'])
            ]
        ];

        $this->list_callback = function ($model) {

            $model->selectTranslatedField('i.values', $this->table_name, 'default');

            return $model;
        };

        $this->item_callback = function ($item, $model) {

            $field_class = 'field' . string_to_camel('_', $item['type']);

            $handler = new $field_class($item['name']);

            $item['handler_title'] = $handler->getTitle();

            return $item;
        };

    }

}
