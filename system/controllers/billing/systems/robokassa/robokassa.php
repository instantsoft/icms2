<?php

class systemRobokassa extends billingPaymentSystem {

    protected $options = [
        'merchant_login' => '',
        'password1'      => '',
        'password2'      => '',
        'fiscal_name'    => '',
        'fiscal_on'      => false,
        'fiscal_sno'     => 'osn',
        'fiscal_method'  => 'full_payment',
        'fiscal_object'  => 'service',
        'fiscal_tax'     => 'none'
    ];

    public function getPaymentFormFields($order) {

        $summ = $this->getPaymentOrderSumm($order['summ']);

        if ($this->options['fiscal_on']) {
            $receipt = $this->buildReceipt($order['description'], $summ);
            $sig     = [$this->options['merchant_login'], $summ, $order['id'], $receipt, $this->options['password1']];
        } else {
            $sig = [$this->options['merchant_login'], $summ, $order['id'], $this->options['password1']];
        }

        $sig = md5(implode(':', $sig));

        $fields = [
            'MrchLogin'      => $this->options['merchant_login'],
            'OutSum'         => $summ,
            'InvId'          => $order['id'],
            'Desc'           => $order['description'],
            'SignatureValue' => $sig
        ];

        if ($this->options['fiscal_on']) {
            $fields['Receipt'] = $receipt;
        }

        return $fields;
    }

    private function buildReceipt($description, $summ) {
        return urlencode(json_encode([
            'sno'   => $this->options['fiscal_sno'],
            'items' => [
                [
                    'name'           => $this->options['fiscal_name'] ?: $description,
                    'quantity'       => 1,
                    'sum'            => $summ,
                    'payment_method' => $this->options['fiscal_method'],
                    'payment_object' => $this->options['fiscal_object'],
                    'tax'            => $this->options['fiscal_tax']
                ]
            ]
       ]));
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

        $sig = [$out_summ, $op_id, $this->options['password2']];
        $sig = strtoupper(md5(implode(':', $sig)));

        if ($sig != $out_signature) {
            return LANG_BILLING_ERR_SIG;
        }

        return $model->acceptPayment($op_id);
    }

}
