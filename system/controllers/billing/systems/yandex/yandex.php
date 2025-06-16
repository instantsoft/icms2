<?php

class systemYandex extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        return [
            'receiver'      => $this->options['receiver'] ?? '',
            'targets'       => $order['description'],
            'formcomment'   => $order['description'],
            'short-desc'    => $order['description'],
            'quickpay-form' => 'small',
            'paymentType'   => 'PC',
            'sum'           => $this->getPaymentOrderSumm($order['summ']),
            'label'         => $order['id'],
            'successURL'    => href_to_abs('billing', 'success') . '?tid=' . $order['id']
        ];
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id      = $request->get('label', '');
        $op_tid     = $request->get('operation_id', '');
        $op_cur     = $request->get('currency', '');
        $op_time    = $request->get('datetime', '');
        $op_sender  = $request->get('sender', '');
        $op_type    = $request->get('notification_type', '');
        $op_amount  = $request->get('amount', '');
        $op_sum     = $request->get('withdraw_amount', 0.0);
        $op_codepro = $request->get('codepro', '');
        $op_sig     = $request->get('sha1_hash', '');

        if ($op_type !== 'p2p-incoming' && $op_type !== 'card-incoming') {
            return LANG_BILLING_ERR;
        }

        if (!$op_id) {
            return LANG_BILLING_ERR_ORDER_ID;
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            return LANG_BILLING_ERR_ORDER_ID;
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return LANG_BILLING_ERR_ORDER_ID;
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $op_sum) {
            return LANG_BILLING_ERR_SUMM;
        }

        $sig = hash('sha1', implode('&', [
            $op_type, $op_tid, $op_amount, $op_cur, $op_time, $op_sender, $op_codepro,
            $this->options['secret_key'], $op_id
        ]));

        if ($sig !== $op_sig) {
            return LANG_BILLING_ERR_SIG;
        }

        return $model->acceptPayment($op_id);
    }

}
