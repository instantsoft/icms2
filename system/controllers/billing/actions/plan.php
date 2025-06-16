<?php
/**
 * @property \modelBilling $model
 */
class actionBillingPlan extends cmsAction {

    public function run() {

        if (!$this->options['is_plans']) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return $this->redirectToLogin();
        }

        $plans = $this->model->getPlans();

        $plan_id = $this->request->get('plan_id', 0);

        $current_plan = $this->model->getUserCurrentPlan($this->cms_user->id) ?: [];

        // Продление?
        $is_renew_plan = $plan_id && $current_plan && $current_plan['id'] == $plan_id;

        if ($this->request->has('submit')) {

            if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                return $this->redirectToAction('plan') . "?plan_id={$plan_id}";
            }

            $system_name = $this->request->get('system', '');

            if ($system_name && $this->validate_sysname($system_name) !== true) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                return $this->redirectToAction('plan') . "?plan_id={$plan_id}";
            }

            $period  = $this->request->get("len{$plan_id}", 0);

            if (!isset($plans[$plan_id])) {
                return cmsCore::error404();
            }

            $plan = $plans[$plan_id];

            if (!isset($plan['prices'][$period])) {
                return cmsCore::error404();
            }

            $price = $plan['prices'][$period];

            $description_text = sprintf($is_renew_plan ? LANG_BILLING_PLAN_TICKET_RENEW :LANG_BILLING_PLAN_TICKET, $plan['title']);

            if ($plan['is_real_price']) {

                if ($this->options['in_mode'] === 'disabled') {

                    cmsUser::addSessionMessage(LANG_BILLING_DEPOSIT_DISABLE, 'error');

                    return $this->redirect(href_to_profile($this->cms_user, ['balance']));
                }

                if (!$system_name) {

                    cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                    return $this->redirectToAction('plan') . "?plan_id={$plan_id}";
                }

                cmsUser::sessionSet('billing_ticket', [
                    'is_plan_ticket' => true,
                    'title'          => $description_text,
                    'amount'         => $price['amount'],
                    'system'         => $system_name,
                    'plan_id'        => $plan_id,
                    'plan_period'    => $period
                ]);

                return $this->redirectTo('billing', 'order', [], ['csrf_token' => cmsForm::getCSRFToken()]);
            }

            $this->model->startTransaction();

            $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

            if ($price['amount'] > $balance) {

                cmsUser::sessionSet('billing_ticket', [
                    'title'       => $description_text,
                    'amount'      => $price['amount'],
                    'diff_amount' => round($price['amount'] - $balance, 2),
                    'back_url'    => href_to('billing', 'plan') . "?plan_id={$plan_id}"
                ]);

                return $this->redirectToAction('deposit');
            }

            $success = $this->model->decrementUserBalance($this->cms_user->id, $price['amount'], $description_text);

            $date_until = $success ? $this->model->addUserPlanSubscribtion($this->cms_user->id, $plan, $price) : false;

            $this->model->endTransaction(!empty($date_until));

            if (!$date_until) {

                cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');

                return $this->redirectToAction('plan');
            }

            cmsUser::addSessionMessage(sprintf(LANG_BILLING_PLAN_SUCCESS, $plan['title'], $date_until), 'success');

            return $this->redirect(href_to_profile($this->cms_user, ['balance']));
        }

        $real_price_plans = array_column(
            array_filter($plans, function($p) {
                return !empty($p['is_real_price']);
            }),
            'id'
        );

        $systems = $this->model->getPaymentSystems();

        $title = LANG_BILLING_BUY_PLAN;
        if ($current_plan) {
            $title = $is_renew_plan ? LANG_BILLING_EXTEND_PLAN : LANG_BILLING_CHANGE_PLAN;
        }

        return $this->cms_template->render([
            'user'             => $this->cms_user,
            'real_price_plans' => $real_price_plans,
            'current_plan'     => $current_plan,
            'systems_list'     => array_collection_to_list($systems, 'name', 'title'),
            'plans_list'       => $this->getPlansList($plans, $current_plan),
            'b_spellcount'     => $this->options['currency'],
            'curr'             => $this->options['currency_real'],
            'is_renew_plan'    => $is_renew_plan,
            'title'            => $title,
            'plans'            => $plans,
            'selected_plan'    => $plan_id
        ]);
    }

    private function getPlansList($plans, $current_plan) {

        $list = [];

        foreach ($plans as $p) {
            $list[$p['id']] = $p['title'] . (($current_plan && $current_plan['id'] == $p['id']) ? ' (' . LANG_BILLING_PLAN_CURRENT . ')' : '');
        }

        return $list;
    }

}
