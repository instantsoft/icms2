<?php
/**
 * Wallet One
 * https://www.walletone.com/ru/merchant/documentation/
 */
class systemW1 extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        $fields = [
            'WMI_MERCHANT_ID'        => $this->options['merchant_id'] ?? '',
            'WMI_PAYMENT_AMOUNT'     => $this->getPaymentOrderSumm($order['summ']),
            'WMI_CURRENCY_ID'        => $this->options['currency_id'] ?? '',
            'WMI_PAYMENT_NO'         => $order['id'],
            'WMI_CUSTOMER_EMAIL'     => $order['email'],
            'WMI_DESCRIPTION'        => 'BASE64:' . base64_encode($order['description']),
            'WMI_SUCCESS_URL'        => href_to_abs('billing', 'success', [], ['tid' => $order['id']]),
            'WMI_FAIL_URL'           => href_to_abs('billing', 'fail'),
            'WMI_RESULT_LINK_EXPIRE' => 300
        ];

        $fields['WMI_SIGNATURE'] = $this->getSignature($fields);

        return $fields;
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id = $request->get('WMI_PAYMENT_NO', 0);
        if (!$op_id) {
            return $this->answer('RETRY', LANG_BILLING_ERR_ORDER_ID);
        }

        $operation = $model->getOperation($op_id);
        if (!$operation) {
            return $this->answer('RETRY', LANG_BILLING_ERR_ORDER_ID);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->answer('RETRY', LANG_BILLING_ERR_STATUS);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        $params = $request->getAll();

        foreach (['WMI_SIGNATURE', 'WMI_ORDER_STATE', 'WMI_PAYMENT_AMOUNT'] as $param_name) {
            if (empty($params[$param_name])) {
                return $this->answer('RETRY', sprintf(LANG_BILLING_ERR_PARAM, $param_name));
            }
        }

        if ($summ != $params['WMI_PAYMENT_AMOUNT']) {
            return $this->answer('RETRY', LANG_BILLING_ERR_SUMM);
        }

        $wmi_signature = $params['WMI_SIGNATURE'];
        unset($params['WMI_SIGNATURE']);

        $signature = $this->getSignature($params);

        if ($signature !== $wmi_signature) {
            return $this->answer('RETRY', LANG_BILLING_ERR_SIG);
        }

        if (strtoupper($params['WMI_ORDER_STATE']) !== 'ACCEPTED') {
            return $this->answer('RETRY', 'Incorrect status ' . $params['WMI_ORDER_STATE']);
        }

        if (!$model->acceptPayment($op_id)) {
            return $this->answer('RETRY', LANG_BILLING_ERR_TRANS);
        }

        return $this->answer('OK');
    }

    private function keySortAsc(array &$array) {

        uksort($array, 'strcasecmp');

        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->keySortAsc($value);
            }
        }
    }

    private function getStringValue(array $array) {

        $str = '';

        foreach ($array as $value) {
            if (is_array($value)) {
                $str .= $this->getStringValue($value);
            } else {
                $str .= iconv('utf-8', 'windows-1251', (string) $value);
            }
        }

        return $str;
    }

    private function getSignature(array $fields) {

        $this->keySortAsc($fields);

        $values_str = $this->getStringValue($fields);

        return base64_encode(pack("H*", md5($values_str . $this->options['key'])));
    }

    private function answer(string $result, $description = null) {
        return 'WMI_RESULT=' . strtoupper($result) . ($description ? '&WMI_DESCRIPTION=' . urlencode($description) : '');
    }

}
