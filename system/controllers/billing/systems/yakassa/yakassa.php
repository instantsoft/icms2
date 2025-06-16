<?php

class systemYakassa extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        return [
            'paymentType'    => 'PC',
            'shopId'         => $this->options['shop_id'] ?? '',
            'scid'           => $this->options['scid'] ?? '',
            'sum'            => $this->getPaymentOrderSumm($order['summ']),
            'customerNumber' => cmsUser::get('id'),
            'orderNumber'    => $order['id'],
            'cps_phone'      => '',
            'cps_email'      => $order['email'],
            'successURL'     => href_to_abs('billing', 'success') . '?tid=' . $order['id'],
            'failURL'        => href_to_abs('billing', 'fail')
        ];
    }

    private function check(cmsRequest $request, modelBilling $model) {

        $r['action']                  = $request->get('action');
        $r['orderSumAmount']          = $request->get('orderSumAmount');
        $r['orderSumCurrencyPaycash'] = $request->get('orderSumCurrencyPaycash');
        $r['orderSumBankPaycash']     = $request->get('orderSumBankPaycash');
        $r['shopId']                  = $request->get('shopId');
        $r['invoiceId']               = $request->get('invoiceId');
        $r['customerNumber']          = $request->get('customerNumber');

        $op_id     = $request->get('orderNumber');
        $signature = $request->get('md5');

        if ($this->buildSignature($r) != $signature) {
            return $this->response('checkOrderResponse', '1', $r['invoiceId'], $this->options['shop_id'], LANG_BILLING_ERR_SIG);
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            return $this->response('checkOrderResponse', '100', $r['invoiceId'], $this->options['shop_id'], LANG_BILLING_ERR_ORDER_ID);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->response('checkOrderResponse', '100', $r['invoiceId'], $this->options['shop_id'], LANG_BILLING_ERR_ORDER_ID);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $r['orderSumAmount']) {
            return $this->response('checkOrderResponse', '100', $r['invoiceId'], $this->options['shop_id'], LANG_BILLING_ERR_SUMM);
        }

        return $this->response('checkOrderResponse', '0', $r['invoiceId'], $this->options['shop_id']);
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $action = $request->get('action', '');

        if ($action == 'checkOrder') {
            return $this->check($request, $model);
        }

        if ($action != 'paymentAviso') {
            return false;
        }

        $r['action']                  = $action;
        $r['orderSumAmount']          = $request->get('orderSumAmount');
        $r['orderSumCurrencyPaycash'] = $request->get('orderSumCurrencyPaycash');
        $r['orderSumBankPaycash']     = $request->get('orderSumBankPaycash');
        $r['shopId']                  = $request->get('shopId');
        $r['invoiceId']               = $request->get('invoiceId');
        $r['customerNumber']          = $request->get('customerNumber');

        $op_id     = $request->get('orderNumber');
        $signature = $request->get('md5');

        if ($this->buildSignature($r) != $signature) {
            return $this->response('paymentAvisoResponse', '1', $r['invoiceId'], $this->options['shop_id'], LANG_BILLING_ERR_SIG);
        }

        if (!$model->acceptPayment($op_id)) {
            return $this->response('paymentAvisoResponse', '1', $r['invoiceId'], $this->options['shop_id'], LANG_BILLING_ERR);
        }

        return $this->response('paymentAvisoResponse', '0', $r['invoiceId'], $this->options['shop_id']);
    }

    private function response($type, $code, $invoice_id, $shop_id, $message = false) {

        $date = date('Y-m-d') . 'T' . date('H:i:s') . '.000' . date('P');

        $xml = '<?xml version="1.0" encoding="UTF-8"?><' . $type . ' performedDatetime="' . $date . '" code="' . $code . '" invoiceId="' . $invoice_id . '" shopId="' . $shop_id . '"';
        if ($message) {
            $xml .= ' message="' . $message . '" techMessage="' . $message . '"';
        }
        $xml .= ' />';

        return $this->processPaymentResult($xml, [
            'Content-Type' => 'text/xml'
        ]);
    }

    private function buildSignature($r) {
        return strtoupper(md5(implode(';', $r) . ';' . $this->options['key']));

    }

}
