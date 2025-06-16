<?php

class actionBillingPayouts extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_payouts';
        $this->grid_name  = 'payouts';
        $this->title      = LANG_BILLING_CP_PAYOUTS;

        $this->external_action_prefix = 'payouts_';

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_BILLING_CP_PO_ADD,
                'href'  => $this->cms_template->href_to('payouts', 'add')
            ]
        ];

    }

}
