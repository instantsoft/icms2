<?php

class systemOnpay extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        $summ = $this->getPaymentOrderSumm($order['summ']);

        $sig = implode(':', [$this->options['merchant_login']??'', $summ, $order['id'], $this->options['password1']??'']);
        $sig = md5($sig);

        return [
            'MrchLogin'      => $this->options['merchant_login']??'',
            'OutSum'         => $summ,
            'InvId'          => $order['id'],
            'Desc'           => $order['description'],
            'SignatureValue' => $sig
        ];
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('InvId', 0);
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id         = $request->get('InvId', 0);
        $out_summ      = $request->get('OutSum', '');
        $out_signature = $request->get('SignatureValue', '');

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

        if ($summ != $out_summ) {
            return LANG_BILLING_ERR_SUMM;
        }

        $sig = [$out_summ, $op_id, strrev($this->options['password1'])];
        $sig = mb_strtoupper(md5(implode(':', $sig)));

        if ($sig != $out_signature) {
            return LANG_BILLING_ERR_SIG;
        }

        return $model->acceptPayment($op_id);
    }

}
