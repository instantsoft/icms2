<?php

class actionUsersMigrations extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = '{users}_groups_migration';
        $this->grid_name  = 'migrations';
        $this->title      = LANG_USERS_CFG_MIGRATION;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_USERS_MIG_ADD,
                'href'  => $this->cms_template->href_to('migrations_add')
            ]
        ];
    }

}
