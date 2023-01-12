<?php

class actionAdminCtypesFieldsAdd extends cmsAction {

    use icms\traits\controllers\actions\formFieldItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->form_name   = 'ctypes_field';
        $this->tpl_name    = 'ctypes_field';
        $this->success_url = $this->cms_template->href_to('ctypes', ['fields', '{id}']);

        $this->form_hooks = ['ctype_field_form'];

        $this->form_ctype_hooks = ['{name}_ctype_field_form'];
    }

}
