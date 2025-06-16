<?php

class actionBillingPlansAdd extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('plans');

        $this->table_name  = 'billing_plans';
        $this->form_name   = 'plan';
        $this->form_opts   = [$this->controller->getOptions()];

        $this->default_item = [
            'prices' => [
                [
                    'length' => 1,
                    'int'    => 'MONTH',
                    'amount' => 10
                ]
            ]
        ];

        $this->success_url = $list_url;
        $this->title       = [
            'add'  => LANG_BILLING_PLAN_ADD,
            'edit' => '{title}'
        ];

        $this->breadcrumbs = [
            [LANG_BILLING_CP_PLANS, $list_url],
            isset($params[0]) ? '{title}' : LANG_BILLING_PLAN_ADD
        ];

        $this->use_default_tool_buttons = true;
    }

}
