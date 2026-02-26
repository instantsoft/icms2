<?php

class widgetBillingPlans extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $show_list = array_filter($this->getOption('show_list', []));

        if ($show_list) {
            $this->controller_billing->model->filterIn('id', $show_list);
        }

        $plans = $this->controller_billing->model->getPlans();

        if (!$plans) {
            return false;
        }

        if ($this->cms_user->is_logged) {
            $current_plan = $this->controller_billing->model->getUserCurrentPlan($this->cms_user->id);
        }

        if (empty($current_plan)) {
            $current_plan = ['id' => 0];
        }

        foreach ($plans as &$plan) {

            $price = [];
            if ($plan['prices']) {
                $price = reset($plan['prices']);
            }

            $plan['price'] = [
                'amount'  => $price['price'] ?? 0,
                'int_str' => !isset($price['int']) ? LANG_MONTH1: string_lang($price['int'] . '1')
            ];

            if ($plan['is_subscribe_after_reg'] && !$this->cms_user->is_logged) {

                $plan['buy_link'] = href_to('auth', 'register');
                $plan['buy_text'] = LANG_BILLING_PLAN_REGISTER;

            } else {

                $plan['buy_link'] = href_to('billing', 'plan', [], ['plan_id' => $plan['id']]);
                $plan['buy_text'] = LANG_BILLING_PLAN_CHOOSE;

                if ($plan['id'] == $current_plan['id']) {

                    $plan['buy_link'] = href_to_profile($this->cms_user, ['balance']);
                    $plan['buy_text'] = LANG_BILLING_PLAN_CURRENT_HINT;
                    $plan['buy_link_disable'] = true;
                }
            }

            if (!$price && $plan['is_subscribe_after_reg'] && $this->cms_user->is_logged && $plan['id'] != $current_plan['id']) {
                $plan['buy_link'] = '';
                $plan['buy_text'] = LANG_BILLING_PLAN_DEFAULT;
                $plan['buy_link_disable'] = true;
            }
        }

        return [
            'curr'               => $this->controller_billing->options['currency_real'],
            'cur_real_symb'      => $this->controller_billing->options['cur_real_symb'],
            'plans_desc'         => $this->getOption('plans_desc', ''),
            'default_plan_id'    => $this->getOption('default_plan_id', 0),
            'default_plan_badge' => $this->getOption('default_plan_badge', ''),
            'current_plan'       => $current_plan,
            'plans'              => $plans
        ];
    }

}
