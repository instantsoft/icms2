<?php

class actionGroupsFieldsAdd extends cmsAction {

    use icms\traits\controllers\actions\formFieldItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->form_name   = 'field';
        $this->tpl_name    = 'backend/field';
        $this->success_url = $this->cms_template->href_to('');

        $this->form_hooks = ['group_field_form'];

    }

}
