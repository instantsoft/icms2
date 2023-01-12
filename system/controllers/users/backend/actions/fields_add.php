<?php

class actionUsersFieldsAdd extends cmsAction {

    use icms\traits\controllers\actions\formFieldItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->form_name   = 'field';
        $this->tpl_name    = 'backend/field';
        $this->success_url = $this->cms_template->href_to('fields');

        $this->form_hooks = ['user_field_form'];
    }

}
