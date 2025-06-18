<?php
/**
 * @property \modelBilling $model
 * @property \messages $controller_messages
 */
class actionBillingOut extends cmsAction {

    use \icms\controllers\billing\traits\validateout;

    public function run() {

        $page    = $this->request->get('page', 1);
        $perpage = $this->options['limit_out'];

        $this->model->filterEqual('user_id', $this->cms_user->id)->
                orderBy('id', 'desc')->
                limitPage($page, $perpage);

        $total = $this->model->getOutsCount();
        $outs  = $this->model->getOuts();

        $this->model->startTransaction();

        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        $plan = [];

        if (!empty($this->cms_user->plan_id)) {
            $plan = $this->model->getPlan($this->cms_user->plan_id);
        }

        $max_amount = (float) (empty($plan['max_out']) ? $balance : min($balance, $plan['max_out']));
        $min_amount = (float) $this->options['out_min'];
        $amount     = $min_amount;
        $out_rate   = (float) $this->options['out_rate'];
        $systems    = array_map(function ($s) {

            $parts = array_map('trim', explode('|', $s, 2));

            return [
                'title' => $parts[0],
                'placeholder' => $parts[1] ?? ''
            ];
        }, explode("\n", $this->options['out_systems']));

        $system = $this->request->get('system', 0);
        $purse  = '';

        $is_pending = $this->model->isUserHasPendingOuts($this->cms_user->id);

        $is_wait_period = $this->options['out_period_days'] ?
                $this->model->isUserHasOutsInPeriod($this->cms_user->id, $this->options['out_period_days']) :
                false;

        $is_can_out = !($balance <= 0 ||
                $balance < $min_amount ||
                $is_pending || $is_wait_period);

        if ($this->request->has('submit')) {

            if (!$is_can_out) {

                cmsUser::addSessionMessage(LANG_BILLING_OUT_NOT_CAN, 'error');

                return $this->redirectToAction('out');
            }

            if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                return $this->redirectToAction('out');
            }

            $amount = (float) str_replace(',', '.', $this->request->get('amount', ''));

            if ($amount < $min_amount || $amount > $max_amount) {

                cmsUser::addSessionMessage(LANG_BILLING_OUT_INCORRECT_AMOUNT, 'error');

                return $this->redirectToAction('out');
            }

            if (!isset($systems[$system]['title'])) {

                cmsUser::addSessionMessage(LANG_BILLING_OUT_INCORRECT_SYSTEM, 'error');

                return $this->redirectToAction('out');
            }

            $system_title = $systems[$system]['title'];

            $purse = trim(strip_tags($this->request->get('purse', '')));

            if (!$purse || mb_strlen($purse) > 32) {

                cmsUser::addSessionMessage(LANG_BILLING_OUT_INCORRECT_PURSE, 'error');

                return $this->redirectToAction('out');
            }

            $summ = $amount * $out_rate;

            $out = [
                'user_id'   => $this->cms_user->id,
                'amount'    => $amount,
                'summ'      => $summ,
                'system'    => $system_title,
                'purse'     => $purse,
                'code'      => string_random(32, $this->cms_user->email),
                'done_code' => string_random(32, $this->cms_user->email . $this->options['out_email'])
            ];

            $out['id'] = $this->model->addOut($out);

            if (!$out['id']) {

                $this->model->endTransaction(false);

                cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');

                return $this->redirectToAction('out');
            }

            if ($this->options['is_out_mail']) {

                $this->model->endTransaction(true);

                $letter = ['name' => 'billing_out'];

                $letter_data = [
                    'amount'      => html_spellcount($out['amount'], $this->options['currency']),
                    'system'      => $system_title,
                    'purse'       => $purse,
                    'summ'        => "{$summ} {$this->options['currency_real']}",
                    'confirm_url' => href_to_abs('billing', 'confirm_out', $out['code'])
                ];

                $to = ['email' => $this->cms_user->email, 'name' => $this->cms_user->nickname];

                $this->controller_messages->sendEmail($to, $letter, $letter_data);

                cmsUser::addSessionMessage(LANG_BILLING_OUT_CF_NOTE, 'info');

            } else {

                $success = $this->confirmOut($out);

                $this->model->endTransaction($success);

                cmsUser::addSessionMessage(LANG_BILLING_OUT_CONFIRMED, 'success');
            }

            return $this->redirectToAction('out');
        }

        return $this->cms_template->render([
            'user'             => $this->cms_user,
            'balance'          => $balance,
            'b_spellcount'     => $this->options['currency'],
            'b_spellcount_arr' => explode('|', $this->options['currency']),
            'currency_real'    => $this->options['currency_real'],
            'out_rate'         => $out_rate,
            'amount'           => $amount,
            'min_amount'       => $min_amount,
            'max_amount'       => $max_amount,
            'systems'          => $systems,
            'system'           => $system,
            'purse'            => $purse,
            'page'             => $page,
            'perpage'          => $perpage,
            'total'            => $total,
            'outs'             => $outs,
            'is_pending'       => $is_pending,
            'is_wait_period'   => $is_wait_period,
            'out_period_days'  => $this->options['out_period_days'],
            'is_can_out'       => $is_can_out,
            'plan'             => $plan
        ]);
    }

}
