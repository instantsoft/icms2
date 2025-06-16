<?php

class actionBillingPlansDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'billing_plans';
        $this->success_url = $this->cms_template->href_to('plans');

        $this->delete_callback = function ($item, $model) {
            return $model->cancelPlan($item['id']);
        };

    }

}
