<?php

class actionFormsFormFields extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $form_id = $params[0] ?? 0;

        $form_data = $this->model->getForm($form_id);

        if (!$form_data) {
            return cmsCore::error404();
        }

        $this->table_name = 'forms_fields';
        $this->grid_name  = 'form_fields';
        $this->grid_args  = [$form_data];

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_FIELD_ADD,
                'href'  => $this->cms_template->href_to('fields_add', $form_data['id'])
            ]
        ];

        $this->cms_template->addBreadcrumb($form_data['title'], $this->cms_template->href_to('edit', [$form_data['id']]));

        $this->cms_template->addMenuItems('admin_toolbar', $this->getFormMenu('edit', $form_data['id']));

        $this->list_callback = function ($model) use($form_data) {

            $model->filterEqual('form_id', $form_data['id']);

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
