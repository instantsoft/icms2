<?php

class systemMoneta extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        return [
            'MNT_ID'             => $this->options['mnt_id'] ?? '',
            'MNT_AMOUNT'         => $this->getPaymentOrderSumm($order['summ']),
            'MNT_TRANSACTION_ID' => $order['id'],
            'MNT_CURRENCY_CODE'  => $this->options['currency'] ?? '',
            'MNT_DESCRIPTION'    => $order['description'],
            'MNT_TEST_MODE'      => 0
        ];
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('MNT_TRANSACTION_ID', 0);
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $mnt_id     = $request->get('MNT_ID');
        $mnt_op_id  = $request->get('MNT_OPERATION_ID');
        $mnt_cur    = $request->get('MNT_CURRENCY_CODE');
        $mnt_sub_id = $request->get('MNT_SUBSCRIBER_ID', '');
        $mnt_test   = $request->get('MNT_TEST_MODE', 0);

        $op_id         = $request->get('MNT_TRANSACTION_ID', 0);
        $out_summ      = $request->get('MNT_AMOUNT');
        $out_signature = $request->get('MNT_SIGNATURE');

        if (!$op_id) {
            return 'FAIL';
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            return 'FAIL';
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return 'FAIL';
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $out_summ) {
            return 'FAIL';
        }

        $sig = [$mnt_id, $op_id, $mnt_op_id, $out_summ, $mnt_cur, $mnt_sub_id, $mnt_test, $this->options['key']];
        $sig = md5(implode('', $sig));

        if ($sig != $out_signature) {
            return 'FAIL';
        }

        if(!$model->acceptPayment($op_id)) {
            return 'FAIL';
        }

        return 'SUCCESS';
    }

}
