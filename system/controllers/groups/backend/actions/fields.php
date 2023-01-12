<?php

class actionGroupsFields extends cmsAction {

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

        $this->items_callback = function ($items) {

            if($items){
                foreach ($items as $key => $item) {

                    $field_class = 'field' . string_to_camel('_', $item['type']);

                    $handler = new $field_class($item['name']);

                    $items[$key]['handler_title'] = $handler->getTitle();
                }
            }

            return $items;
        };

    }

}
