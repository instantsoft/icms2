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
            return $this->log(LANG_BILLING_ERR_ORDER_ID);
        }

        $operation = $model->getOperation($op_id);
        if (!$operation) {
            return $this->log(LANG_BILLING_ERR_ORDER_ID);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->log(LANG_BILLING_ERR_STATUS);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($request->get('LMI_PREREQUEST', 0) === 1) {
            return $this->preRequest($request, $summ);
        }

        $data = $request->getAll();

        foreach ([
            'LMI_PAYEE_PURSE', 'LMI_PAYMENT_AMOUNT', 'LMI_PAYMENT_NO',
            'LMI_MODE', 'LMI_SYS_INVS_NO', 'LMI_SYS_TRANS_NO', 'LMI_SYS_TRANS_DATE',
            'LMI_PAYER_PURSE', 'LMI_PAYER_WM'
            ] as $param_name) {
            if (!array_key_exists($param_name, $data)) {
                return $this->log(sprintf(LANG_BILLING_ERR_PARAM, $param_name));
            }
        }

        $common_string = $data['LMI_PAYEE_PURSE'] . $data['LMI_PAYMENT_AMOUNT'] . $data['LMI_PAYMENT_NO'] .
                $data['LMI_MODE'] . $data['LMI_SYS_INVS_NO'] . $data['LMI_SYS_TRANS_NO'] .
                $data['LMI_SYS_TRANS_DATE'] . $this->options['secret_key'] . $data['LMI_PAYER_PURSE'] . $data['LMI_PAYER_WM'];

        $hash = strtoupper(hash('sha256', $common_string));

        if ($hash != $data['LMI_HASH']) {
            return $this->log(LANG_BILLING_ERR_SIG);
        }

        if (!$model->acceptPayment($operation['id'])) {
            return $this->log(LANG_BILLING_ERR_TRANS);
        }

        return true;
    }

    public function preRequest($request, $summ) {

        $out_summ  = $request->get('LMI_PAYMENT_AMOUNT', 0.00);
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

        return $this->processPaymentResult('YES');
    }

}
