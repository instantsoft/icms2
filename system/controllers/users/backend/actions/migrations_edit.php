<?php

class actionUsersMigrationsEdit extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('migrations');

        $this->table_name  = '{users}_groups_migration';
        $this->form_name   = 'migration';
        $this->success_url = $list_url;
        $this->title       = '{title}';

        $this->breadcrumbs = [
            [LANG_USERS_CFG_MIGRATION, $list_url],
            '{title}'
        ];

        $this->use_default_tool_buttons = true;

        $this->tool_buttons = [
            [
                'title'  => LANG_HELP,
                'target' => '_blank',
                'href'   => LANG_HELP_URL_COM_USERS_MIGRATON,
                'icon'   => 'question-circle'
            ]
        ];

    }

}
