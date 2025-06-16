<?php

class actionBillingExchange extends cmsAction {

    public function run() {

        if (!$this->options['is_rtp'] && !$this->options['is_ptr']) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->isInGroups($this->options['rtp_groups'])) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        $is_can_rtp      = $this->options['is_rtp'] && $this->cms_user->rating > 0;
        $is_can_ptr      = $this->options['is_ptr'] && $balance > 0;
        $is_can_exchange = $is_can_ptr || $is_can_rtp;

        if ($this->request->has('submit')) {

            if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                return $this->redirectToAction('exchange');
            }

            $error = false;

            $mode   = $this->request->get('mode', '');
            $amount = (float) str_replace(',', '.', $this->request->get('amount', ''));

            if (!in_array($mode, ['rtp', 'ptr'])) {
                $error = true;
            }
            if ($mode === 'rtp' && !$is_can_rtp) {
                $error = true;
            }
            if ($mode === 'ptr' && !$is_can_ptr) {
                $error = true;
            }
            if ($amount <= 0) {
                $error = true;
            }
            if ($mode === 'rtp') {
                $max_amount = $this->cms_user->rating;
            }
            if ($mode === 'ptr') {
                $max_amount = $balance;
            }

            if ($amount > $max_amount) {
                $error = true;
            }

            if (!$error) {

                $success = $this->exchangeRating($this->cms_user->id, $mode, $amount);

                $this->model->endTransaction($success);

                if (!$success) {
                    cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');
                } else {
                    cmsUser::addSessionMessage(LANG_BILLING_EXCHANGE_SUCCESS, 'success');
                }

                return $this->redirect(href_to_profile($this->cms_user, ['balance']));
            }

            cmsUser::addSessionMessage(LANG_BILLING_EXCHANGE_ERROR, 'error');

            return $this->redirectToAction('exchange');
        }

        $b_spellcount = $this->options['currency'];
        $b_spellcount_arr = explode('|', $b_spellcount);

        $modes = [];

        if ($is_can_rtp) {
            $modes['rtp'] = sprintf(LANG_BILLING_EXCHANGE_RTP, $this->options['currency_title']);
        }

        if ($is_can_ptr) {
            $modes['ptr'] = sprintf(LANG_BILLING_EXCHANGE_PTR, $this->options['currency_title']);
        }

        $rtp_rate = (float) $this->options['rtp_rate'];
        $ptr_rate = (float) $this->options['ptr_rate'];

        return $this->cms_template->render([
            'user'             => $this->cms_user,
            'balance'          => $balance,
            'b_spellcount'     => $b_spellcount,
            'b_spellcount_arr' => $b_spellcount_arr,
            'is_can_exchange'  => $is_can_exchange,
            'is_can_rtp'       => $is_can_rtp,
            'is_can_ptr'       => $is_can_ptr,
            'rtp_rate'         => $rtp_rate,
            'ptr_rate'         => $ptr_rate,
            'modes'            => $modes
        ]);
    }

}
