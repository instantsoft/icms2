<?php
/**
 * доделать https://www.walletone.com/ru/merchant/documentation/
 */
class systemW1 extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        $fields = [
            'WMI_MERCHANT_ID'        => $this->options['merchant_id'],
            'WMI_PAYMENT_AMOUNT'     => $this->getPaymentOrderSumm($order['summ']),
            'WMI_CURRENCY_ID'        => $this->options['currency_id'],
            'WMI_PAYMENT_NO'         => $order['id'],
            'WMI_CUSTOMER_EMAIL'     => $order['email'],
            'WMI_DESCRIPTION'        => 'BASE64:' . base64_encode($order['description']),
            'WMI_SUCCESS_URL'        => href_to_abs('billing', 'success', [], ['tid' => $order['id']]),
            'WMI_FAIL_URL'           => href_to_abs('billing', 'fail'),
            'WMI_RESULT_LINK_EXPIRE' => 300
        ];

        uksort($fields, 'strcasecmp');
        $fieldValues = '';

        foreach ($fields as $value) {
            $value       = iconv('utf-8', 'windows-1251', $value);
            $fieldValues .= $value;
        }

        $signature = base64_encode(pack("H*", md5($fieldValues . $this->options['key'])));

        $fields['WMI_SIGNATURE'] = $signature;

        return $fields;
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id = $request->get('WMI_PAYMENT_NO', 0);

        if (!$op_id) {
            return $this->answer('Error', LANG_BILLING_ERR_ORDER_ID);
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            return $this->answer('Error', LANG_BILLING_ERR_ORDER_ID);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->answer('Error', LANG_BILLING_ERR_ORDER_ID);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if (!isset($_POST['WMI_SIGNATURE'])) {
            return $this->answer('Retry', 'Отсутствует параметр WMI_SIGNATURE');
        }

        if (!isset($_POST["WMI_ORDER_STATE"])) {
            return $this->answer('Retry', 'Отсутствует параметр WMI_ORDER_STATE');
        }

        $params = array();

        foreach ($_POST as $name => $value) {
            if ($name == 'WMI_SIGNATURE') {
                continue;
            }
            $params[$name] = $value;
        }

        uksort($params, 'strcasecmp');
        $values = "";

        foreach ($params as $name => $value) {
            $value  = iconv('utf-8', 'windows-1251', $value);
            $values .= $value;
        }

        $signature = base64_encode(pack("H*", md5($values . $this->options['key'])));

        if ($signature != $_POST['WMI_SIGNATURE']) {
            if (strtoupper($_POST['WMI_ORDER_STATE']) == 'ACCEPTED') {

                if (!$model->acceptPayment($op_id)) {
                    return $this->answer('Retry', 'Заказ #' . $_POST["WMI_PAYMENT_NO"] . ' оплачен, но транзакция не выполнилась!');
                }

                return $this->answer('Ok', 'Заказ #' . $_POST["WMI_PAYMENT_NO"] . ' оплачен!');

            } else {
                return $this->answer('Retry', 'Неверное состояние ' . $_POST["WMI_ORDER_STATE"]);
            }
        }

        return $this->answer('Retry', 'Неверная подпись ' . $_POST["WMI_SIGNATURE"]);
    }

    private function getSignature($param) {

    }

    private function answer($result, $description) {
        return 'WMI_RESULT=' . strtoupper($result) . '&WMI_DESCRIPTION=' . urlencode($description);
    }

}
