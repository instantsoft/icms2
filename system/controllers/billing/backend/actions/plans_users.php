<?php
/**
 * @property \modelBilling $model
 */
class actionBillingPlansUsers extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    private $plan = [];

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $plan_id = $params[0] ?? 0;

        $this->plan = $this->model->getPlan($plan_id);
        if (!$this->plan) {
            return cmsCore::error404();
        }

        $this->table_name = 'billing_plans_log';
        $this->grid_name  = 'plans_users';
        $this->title      = sprintf(LANG_BILLING_CP_PLANS_USERS, $this->plan['title']);

        $this->cms_template->addBreadcrumb(LANG_BILLING_CP_PLANS, $this->cms_template->href_to('plans'));

        $this->list_callback = function (cmsModel $model) {

            $model->joinUserLeft();

            return $model->filterEqual('plan_id', $this->plan['id']);
        };
    }

}
