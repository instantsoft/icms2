<?php

class actionUsersFields extends cmsAction {

    use icms\controllers\admin\traits\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->setProperty('table_name', '{users}_fields');
        $this->setProperty('grid_name', 'fields');
        $this->setProperty('grid_url', $this->cms_template->href_to('fields'));
        $this->setProperty('title', LANG_USERS_CFG_FIELDS);
        $this->setProperty('tool_buttons', [
            [
                'class' => 'add',
                'title' => LANG_CP_FIELD_ADD,
                'href'  => $this->cms_template->href_to('fields_add')
            ]
        ]);
        $this->setProperty('list_callback', function ($model) {

            $model->orderBy('ordering');

            return $model;
        });

    }

}
