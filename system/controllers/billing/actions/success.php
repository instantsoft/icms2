<?php

class actionBillingSuccess extends cmsAction {

    use \icms\controllers\billing\traits\validatepay;

    public function run($system_name = null) {

        if ($system_name) {

            if ($this->validate_sysname($system_name) !== true) {
                return cmsCore::error404();
            }

            $system = $this->getPaymentSystem($system_name);
            if (!$system) {
                return cmsCore::error404();
            }

            $order_id = $system->getSuccessOrderId($this->request);
        }

        $ticket = cmsUser::sessionGet('billing_ticket', true);

        $is_plan_ticket    = !empty($ticket['is_plan_ticket']);
        $is_plan_activated = false;
        $plan              = [];

        if ($is_plan_ticket) {
            $plan = $this->model->getUserCurrentPlan($this->cms_user->id);
            $is_plan_activated = ($plan && $plan['id'] == ($ticket['plan_id'] ?? 0));
        }

        $next_url = !empty($ticket['back_url']) ? $ticket['back_url'] : href_to_profile($this->cms_user, ['balance']);

        $b_spellcount = $this->options['currency'];
        $b_spellcount_arr = explode('|', $b_spellcount);

        return $this->cms_template->render('success', [
            'b_spellcount'      => $b_spellcount,
            'b_spellcount_arr'  => $b_spellcount_arr,
            'system_name'       => $system_name,
            'next_url'          => $next_url,
            'order_id'          => $order_id ?? false,
            'is_plan_ticket'    => $is_plan_ticket,
            'is_plan_activated' => $is_plan_activated,
            'plan'              => $plan
        ]);
    }

}
