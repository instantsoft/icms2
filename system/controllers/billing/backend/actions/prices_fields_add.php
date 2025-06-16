<?php

class actionBillingPricesFieldsAdd extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('prices', 'fields');

        $this->table_name  = 'billing_paid_fields';
        $this->form_name   = 'field';
        $this->success_url = $list_url;
        $this->title       = [
            'add'  => LANG_BILLING_CP_FIELDS_ADD,
            'edit' => LANG_BILLING_CP_FIELDS_EDIT
        ];

        $this->breadcrumbs = [
            [LANG_BILLING_CP_PRICES, $this->cms_template->href_to('prices')],
            [LANG_BILLING_CP_PRICES_PAID_FIELDS, $list_url],
            isset($params[0]) ? LANG_BILLING_CP_FIELDS_EDIT : LANG_BILLING_CP_FIELDS_ADD
        ];

        $this->use_default_tool_buttons = true;
    }

}
