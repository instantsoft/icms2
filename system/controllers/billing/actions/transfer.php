<?php
/**
 * @property \modelUsers $model_users
 * @property \modelBilling $model
 * @property \messages $controller_messages
 */
class actionBillingTransfer extends cmsAction {

    use \icms\controllers\billing\traits\validatetransfer;

    public function run($user_id) {

        $receiver = $this->model_users->getUser($user_id);

        if (!$receiver || $receiver['id'] == $this->cms_user->id) {
            return cmsCore::error404();
        }

        $amount      = floatval(str_replace(',', '.', $this->request->get('amount', '')));
        $description = trim(strip_tags($this->request->get('description', '')));

        $this->model->startTransaction();

        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        if ($this->request->has('submit')) {

            if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                return $this->redirectToAction('transfer', [$receiver['id']]);
            }

            if (($amount <= 0) || ($amount > $balance)) {

                cmsUser::addSessionMessage(LANG_BILLING_TRANSFER_INCORRECT_AMOUNT, 'error');

                return $this->redirectToAction('transfer', [$receiver['id']]);
            }

            if (mb_strlen($description) > 255) {

                cmsUser::addSessionMessage(sprintf(LANG_BILLING_TRANSFER_INCORRECT_DESC, 255), 'error');

                return $this->redirectToAction('transfer', [$receiver['id']]);
            }

            $transfer = [
                'from_id'     => $this->cms_user->id,
                'to_id'       => $receiver['id'],
                'amount'      => $amount,
                'description' => $description,
                'code'        => string_random(32, $this->cms_user->email)
            ];

            $transfer['id'] = $this->model->addTransfer($transfer);

            if (!$transfer['id']) {

                $this->model->endTransaction(false);

                cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');

                return $this->redirectToAction('transfer', [$receiver['id']]);
            }

            if ($this->options['is_transfers_mail']) {

                $this->model->endTransaction(true);

                $letter = ['name' => 'billing_transfer'];

                $letter_data = [
                    'to_name'     => $receiver['nickname'],
                    'to_url'      => href_to_abs('users', $receiver['id']),
                    'amount'      => html_spellcount($transfer['amount'], $this->options['currency']),
                    'description' => $transfer['description'] ? $transfer['description'] : '---',
                    'confirm_url' => href_to_abs('billing', 'confirm_tf', $transfer['code'])
                ];

                $to = ['email' => $this->cms_user->email, 'name' => $this->cms_user->nickname];

                $this->controller_messages->sendEmail($to, $letter, $letter_data);

                cmsUser::addSessionMessage(LANG_BILLING_TRANSFER_CF_NOTE, 'info');

            } else {

                $success = $this->acceptTransfer($transfer);

                $this->model->endTransaction($success);

                if (!$success) {

                    cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');

                    return $this->redirectToAction('transfer', [$receiver['id']]);
                }

                cmsUser::addSessionMessage(LANG_BILLING_TRANSFER_SUCCESS, 'success');
            }

            return $this->redirect(href_to_profile($receiver));
        }

        $b_spellcount = $this->options['currency'];
        $b_spellcount_arr = explode('|', $b_spellcount);

        return $this->cms_template->render([
            'title'            => sprintf(LANG_BILLING_TRANSFER_TO_USER, $this->options['currency_title']),
            'b_spellcount'     => $b_spellcount,
            'b_spellcount_arr' => $b_spellcount_arr,
            'receiver'         => $receiver,
            'receiver_url'     => href_to_profile($receiver),
            'balance'          => $balance,
            'description'      => $description,
            'amount'           => $amount
        ]);
    }

}
