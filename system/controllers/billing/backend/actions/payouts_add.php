<?php

class actionBillingPayoutsAdd extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('payouts');

        $this->table_name  = 'billing_payouts';
        $this->form_name   = 'payout';
        $this->success_url = $list_url;
        $this->title       = [
            'add'  => LANG_BILLING_CP_PO_ADD,
            'edit' => '{title}'
        ];

        $this->breadcrumbs = [
            [LANG_BILLING_CP_PAYOUTS, $list_url],
            isset($params[0]) ? '{title}' : LANG_BILLING_CP_PO_ADD
        ];

        $this->use_default_tool_buttons = true;
    }

}
