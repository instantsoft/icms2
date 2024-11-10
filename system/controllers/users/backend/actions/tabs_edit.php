<?php

class actionUsersTabsEdit extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('tabs');

        $this->table_name  = '{users}_tabs';
        $this->cache_key   = 'users.tabs';
        $this->form_name   = 'tab';
        $this->success_url = $list_url;
        $this->title       = '{title}';

        $this->breadcrumbs = [
            [LANG_USERS_CFG_TABS, $list_url],
            '{title}'
        ];

        $this->use_default_tool_buttons = true;
    }

}
