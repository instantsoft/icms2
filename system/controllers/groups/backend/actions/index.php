<?php

class actionGroupsIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'groups_fields';
        $this->grid_name  = 'fields';
        $this->title      = LANG_GROUPS_FIELDS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_FIELD_ADD,
                'href'  => $this->cms_template->href_to('fields_add', ['groups'])
            ]
        ];

        $this->list_callback = function ($model) {

            $model->orderBy('ordering', 'asc');

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
