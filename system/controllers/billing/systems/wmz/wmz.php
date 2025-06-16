<?php

class systemWmz extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        return [
            'LMI_PAYEE_PURSE'         => $this->options['purse'] ?? '',
            'LMI_PAYMENT_AMOUNT'      => $this->getPaymentOrderSumm($order['summ']),
            'LMI_PAYMENT_NO'          => $order['id'],
            'LMI_PAYMENT_DESC_BASE64' => base64_encode($order['description']),
            'LMI_SIM_MODE'            => $this->options['test_mode'] ?? 0
        ];
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('LMI_PAYMENT_NO', 0);
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id = $request->get('LMI_PAYMENT_NO', 0);

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

        if ($request->get('LMI_PREREQUEST') == 1) {
            return $this->preRequest($request, $summ);
        }

        $data = $request->getAll();

        $common_string = $data['LMI_PAYEE_PURSE'] . $data['LMI_PAYMENT_AMOUNT'] . $data['LMI_PAYMENT_NO'] .
                $data['LMI_MODE'] . $data['LMI_SYS_INVS_NO'] . $data['LMI_SYS_TRANS_NO'] .
                $data['LMI_SYS_TRANS_DATE'] . $this->options['secret_key'] . $data['LMI_PAYER_PURSE'] . $data['LMI_PAYER_WM'];

        $hash = strtoupper(hash('sha256', $common_string));

        if ($hash != $data['LMI_HASH']) {
            return LANG_BILLING_ERR_SIG;
        }

        return $model->acceptPayment($op_id);
    }

    public function preRequest($request, $summ) {

        $out_summ  = $request->get('LMI_PAYMENT_AMOUNT', '');
        $out_purse = $request->get('LMI_PAYEE_PURSE', '');

        if ($summ != $out_summ) {

            return $this->processPaymentResult(LANG_BILLING_ERR_SUMM, [
                'Content-Type' => 'text/html; charset=iso-8859-1'
            ]);
        }

        if ($this->options['purse'] != $out_purse) {

            return $this->processPaymentResult(LANG_BILLING_ERR_SHOP_ID, [
                'Content-Type' => 'text/html; charset=iso-8859-1'
            ]);
        }

        return true;
    }

}
