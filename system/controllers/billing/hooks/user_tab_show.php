<?php
/**
 * @property \modelBilling $model
 */
class onBillingUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab) {

        $page    = $this->request->get('page', 1);
        $perpage = $this->options['limit_log'];

        $this->model->filterEqual('user_id', $profile['id'])->
                filterEqual('status', modelBilling::STATUS_DONE)->
                orderBy('id', 'desc')->
                limitPage($page, $perpage);

        $total      = $this->model->getOperationsCount();
        $operations = $this->model->getOperations();

        // Тарифные планы
        $plan  = [];
        $plans = [];

        if ($this->options['is_plans']) {

            $plan = $this->model->getUserCurrentPlan($profile['id']) ?: [];

            $plans = $this->model->getPlans();
        }

        $is_own_profile = $this->cms_user->id == $profile['id'];

        $dep_link_text  = LANG_BILLING_BALANCE_ADD;
        $dep_link_title = LANG_BILLING_OP_DEPOSIT;
        $deposit_url    = '';
        if ($this->options['in_mode'] === 'enabled') {
            $deposit_url = href_to($this->name, 'deposit');
        }
        if ($this->cms_user->is_admin) {
            $deposit_url    = href_to($this->name, 'add_balance', [$profile['id']]);
            $dep_link_title = $profile['nickname'] . ': ' . LANG_BILLING_BALANCE_CHANGE;
            $dep_link_text  = LANG_BILLING_BALANCE_CHANGE;
        }

        $plan_link_title = LANG_BILLING_BUY_PLAN;
        $plan_url = '';
        if ($this->options['is_plans'] && $is_own_profile && $plans) {
            $plan_url = href_to($this->name, 'plan');
            if ($plan) {
                $plan_link_title = LANG_BILLING_CHANGE_PLAN;
            }
        }

        $is_exchange = ($this->options['is_rtp'] || $this->options['is_ptr']) && $this->cms_user->isInGroups($this->options['rtp_groups']);

        $is_out = $this->options['is_out'] && $is_own_profile && $this->cms_user->isInGroups($this->options['out_groups']);

        $balance = $this->model->getUserBalance($profile['id'], true);

        return $this->cms_template->renderInternal($this, 'profile_tab', [
            'tab'             => $tab,
            'balance'         => $balance,
            'currency_real'   => $this->options['currency_real'],
            'b_spellcount'    => $this->options['currency'],
            'deposit_url'     => $deposit_url,
            'dep_link_title'  => $dep_link_title,
            'dep_link_text'   => $dep_link_text,
            'plan_link_title' => $plan_link_title,
            'plan_url'        => $plan_url,
            'is_own_profile'  => $is_own_profile,
            'is_exchange'     => $is_exchange,
            'is_out'          => $is_out,
            'is_admin'        => $this->cms_user->is_admin,
            'profile'         => $profile,
            'operations'      => $operations,
            'total'           => $total,
            'page'            => $page,
            'perpage'         => $perpage,
            'plans'           => $plans,
            'plan'            => $plan
        ]);
    }

}
