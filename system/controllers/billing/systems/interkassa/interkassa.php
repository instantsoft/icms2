<?php
/**
 * https://docs.interkassa.com/
 */
class systemInterkassa extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        return [
            'ik_co_id' => $this->options['ik_co_id'] ?? '',
            'ik_am'    => $this->getPaymentOrderSumm($order['summ']),
            'ik_pm_no' => $order['id'],
            'ik_desc'  => $order['description'],
            'ik_cur'   => 'RUB'
        ];
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('ik_pm_no', 0);
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id      = $request->get('ik_pm_no', 0);
        $out_co_id  = $request->get('ik_co_id', '');
        $out_am     = $request->get('ik_am', '');
        $out_sign   = $request->get('ik_sign', '');
        $out_inv_st = $request->get('ik_inv_st', '');

        if ($out_inv_st !== 'process' && $out_inv_st !== 'success') {
            return LANG_BILLING_ERR;
        }

        if ($this->options['ik_co_id'] !== $out_co_id) {
            return LANG_BILLING_ERR_SHOP_ID;
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

        if ($summ != $out_am) {
            return LANG_BILLING_ERR_SUMM;
        }

        $dataSet = [];

        foreach ($request->getAll() as $key => $val) {
            if (mb_strpos($key, 'ik_') === 0) {
                $dataSet[$key] = $val;
            }
        }

        unset($dataSet['ik_sign']);

        ksort($dataSet, SORT_STRING);
        array_push($dataSet, trim($this->options['ik_secret_key']));

        $sig = implode(':', $dataSet);
        $sig = base64_encode(md5($sig, true));

        if ($sig !== $out_sign) {
            return LANG_BILLING_ERR_SIG;
        }

        if (!$model->acceptPayment($operation['id'])) {
            return $this->log(LANG_BILLING_ERR_TRANS);
        }

        return true;
    }

}
