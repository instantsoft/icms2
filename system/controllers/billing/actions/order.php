<?php
/**
 * @property \modelBilling $model
 */
class actionBillingOrder extends cmsAction {

    use \icms\controllers\billing\traits\validatepay;

    protected $use_csrf_token = true;

    public function run() {

        $ticket         = cmsUser::sessionGet('billing_ticket');
        $is_plan_ticket = !empty($ticket['is_plan_ticket']);

        if ($is_plan_ticket) {
            $amount      = (float) $ticket['amount'];
            $system_name = $ticket['system'];
            $min_pack    = 0.0;
        } else {
            $amount      = round($this->request->get('amount', 0.0), 2);
            $system_name = $this->request->get('system', '');
            $min_pack    = (float) ($ticket['diff_amount'] ?? $this->options['min_pack']);
        }

        if ($this->validate_sysname($system_name) !== true) {
            return cmsCore::error404();
        }

        // Проверка минимальной суммы
        if ($amount < $min_pack) {

            cmsUser::addSessionMessage(sprintf(
                LANG_BILLING_DEPOSIT_MIN_ERROR,
                html_spellcount($min_pack, $this->options['currency'], null, null, '0')
            ), 'error');

            return $this->redirectToAction('deposit');
        }

        // Объект класса оплаты
        $system = $this->getPaymentSystem($system_name);
        if (!$system) {
            return cmsCore::error404();
        }

        // Сумма к оплате
        $summ = $this->model->getDepositSumm($amount);

        // Добавляем операцию
        $operation_id = $this->model->addOperation([
            'system_id'   => $system->getDbId(),
            'type'        => modelBilling::OP_TYPE_INCOME,
            'amount'      => $amount,
            'summ'        => $summ,
            'user_id'     => $this->cms_user->id,
            'sender_id'   => $this->cms_user->id,
            'description' => LANG_BILLING_OP_DEPOSIT,
            'status'      => modelBilling::STATUS_CREATED,
            'plan_id'     => $is_plan_ticket ? $ticket['plan_id'] : null,
            'plan_period' => $is_plan_ticket ? $ticket['plan_period'] : null
        ]);

        // Готовим заказ
        $order = [
            'id'          => $operation_id,
            'amount'      => $amount,
            'summ'        => $summ,
            'user_id'     => $this->cms_user->id,
            'email'       => $this->cms_user->email,
            'description' => sprintf(LANG_BILLING_OP_DEPOSIT_DESC, $this->cms_user->nickname)
        ];

        // Поля в форму оплаты
        $system_fields = $system->getPaymentFormFields($order);

        // Пришёл редирект
        if (is_string($system_fields)) {
            return $this->redirect($system_fields);
        }

        // Ошибка
        if (is_null($system_fields)) {

            cmsUser::addSessionMessage($system->getLastError(), 'error');

            return $this->redirectToAction('deposit');
        }

        // URL оплаты
        $payment_url = $system->getPaymentURL();

        return $this->cms_template->render([
            'b_spellcount'  => $this->options['currency'],
            'curr'          => $this->options['currency_real'],
            'is_plan_order' => $is_plan_ticket,
            'user'          => $this->cms_user,
            'amount'        => $amount,
            'summ'          => $summ,
            'system'        => $system,
            'system_fields' => $system_fields,
            'payment_url'   => $payment_url,
            'ticket'        => $ticket
        ]);
    }

}
