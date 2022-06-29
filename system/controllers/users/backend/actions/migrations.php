<?php

class actionUsersMigrations extends cmsAction {

    use icms\controllers\admin\traits\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->setProperty('table_name', '{users}_groups_migration');
        $this->setProperty('grid_name', 'migrations');
        $this->setProperty('grid_url', $this->cms_template->href_to('migrations'));
        $this->setProperty('title', LANG_USERS_CFG_MIGRATION);
        $this->setProperty('tool_buttons', [
            [
                'class' => 'add',
                'title' => LANG_USERS_MIG_ADD,
                'href'  => $this->cms_template->href_to('migrations_add')
            ]
        ]);

    }

}
