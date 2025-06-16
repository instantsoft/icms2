<?php
/**
 * @property \modelBackendBilling $model
 */
class actionBillingIndex extends cmsAction {

    public function run($do = false) {

        if ($do) {
            return $this->runAction('index_' . $do, array_slice($this->params, 1));
        }

        $total  = $this->model->getTotalBalance();
        $debt   = round($this->options['out_rate'] * $total, 2);
        $profit = $this->model->getProfitStats();

        $users = [
            $this->model->getTopBalanceUsers('desc'),
            $this->model->getTopBalanceUsers('asc'),
        ];

        $plans = $this->model->getPlans();

        return $this->cms_template->render([
            'options' => $this->options,
            'total'   => $total,
            'debt'    => $debt,
            'profit'  => $profit,
            'users'   => $users,
            'plans'   => $plans,
        ]);
    }

}
