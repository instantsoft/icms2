<?php

class actionUsersMigrationsAdd extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('migrations');

        $this->table_name  = '{users}_groups_migration';
        $this->form_name   = 'migration';
        $this->success_url = $list_url;
        $this->title       = LANG_USERS_MIG_ADD;

        $this->breadcrumbs = [
            [LANG_USERS_CFG_MIGRATION, $list_url],
            LANG_USERS_MIG_ADD
        ];

        $this->tool_buttons = [
            [
                'class' => 'save',
                'title' => LANG_SAVE,
                'href'  => 'javascript:icms.forms.submit()'
            ],
            [
                'class' => 'cancel',
                'title' => LANG_CANCEL,
                'href'  => $list_url
            ],
            [
                'class'  => 'help',
                'title'  => LANG_HELP,
                'target' => '_blank',
                'href'   => LANG_HELP_URL_COM_USERS_MIGRATON
            ]
        ];

    }

}
