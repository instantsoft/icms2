<?php

class actionBillingPlans extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_plans';
        $this->grid_name  = 'plans';
        $this->title      = LANG_BILLING_CP_PLANS;

        $this->external_action_prefix = 'plans_';

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_BILLING_PLAN_ADD,
                'href'  => $this->cms_template->href_to('plans', 'add')
            ]
        ];
    }

}
