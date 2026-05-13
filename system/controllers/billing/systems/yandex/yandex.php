<?php
/**
 * ЮMoney
 * https://yoomoney.ru/docs/wallet/using-api/notification-p2p-incoming
 */
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

        $op_id   = $request->get('label', '');
        $op_type = $request->get('notification_type', '');
        $op_sum  = $request->get('withdraw_amount', 0.0);
        $op_sig  = $request->get('sign', '');

        if ($request->has('test_notification')) {
            return LANG_BILLING_ERR;
        }

        if (!in_array($op_type, ['p2p-incoming', 'card-incoming'])) {
            return $this->log(LANG_BILLING_ERR . 'notification_type: '.$op_type);
        }

        if (!$op_id) {
            return $this->log(LANG_BILLING_ERR_ORDER_ID);
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            return $this->log(LANG_BILLING_ERR_ORDER_ID . 'label: '.$op_id);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->log(LANG_BILLING_ERR_STATUS);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $op_sum) {
            return $this->log(LANG_BILLING_ERR_SUMM . 'withdraw_amount: '.$op_sum);
        }

        $dataSet = [];

        foreach ($request->getAll() as $key => $val) {
            $dataSet[$key] = is_empty_value($val) ? '' : $val;
        }

        unset($dataSet['sign']);

        ksort($dataSet, SORT_STRING);

        $data_set_str = http_build_query($dataSet, '', '&', PHP_QUERY_RFC3986);

        $sig = hash_hmac('sha256', $data_set_str, $this->options['secret_key']);

        if ($sig !== $op_sig) {
            return $this->log(LANG_BILLING_ERR_SIG);
        }

        if (!$model->acceptPayment($op_id)) {
            return $this->log(LANG_BILLING_ERR_TRANS);
        }

        return true;
    }

}
