<?php
/**
 * https://docs.robokassa.ru/
 */
class systemRobokassa extends billingPaymentSystem {

    const PAYMENTCURL = 'https://auth.robokassa.ru/Merchant/Indexjson.aspx';
    const PAYMENTURL  = 'https://auth.robokassa.ru/Merchant/Index/';

    private $hash_type = 'md5';

    protected $options = [
        'merchant_login' => '',
        'test_mode'      => 0,
        'password1'      => '',
        'password2'      => '',
        'password1_test' => '',
        'password2_test' => '',
        'fiscal_name'    => '',
        'fiscal_on'      => false,
        'fiscal_sno'     => 'osn',
        'fiscal_method'  => 'full_payment',
        'fiscal_object'  => 'service',
        'fiscal_tax'     => 'none'
    ];

    public function getPaymentFormFields($order) {
        return [
            'order_id' => $order['id'],
            'comment'  => $order['description']
        ];
    }

    public function preparePayment(cmsRequest $request, modelBilling $model) {

        $order_id = $request->get('order_id', 0);

        $operation = $model->getOperation($order_id);
        if (!$operation || $operation['user_id'] != cmsUser::get('id')) {
            return $this->error(LANG_BILLING_ERR_ORDER_ID);
        }

        $payload = [
            'InvoiceID'   => $operation['id'],
            'OutSum'      => $this->getPaymentOrderSumm($operation['summ']),
            'Description' => mb_substr(strip_tags($request->get('comment', '')), 0, 100)
        ];

        if ($this->options['fiscal_on']) {
            $payload['Receipt'] = $this->buildReceipt($payload['Description'], $payload['OutSum']);
        }

        if (!empty($this->options['test_mode'])) {
            $payload['IsTest'] = 1;
        }

        $result = $this->sendPaymentRequest($payload);

        if ($result->error) {
            return $this->error($result->error);
        }

        if (!is_array($result->body)) {
            return $this->error(LANG_BILLING_ERR);
        }

        if (!empty($result->body['errorCode'])) {
            return $this->error('Error Code ' . $result->body['errorCode']);
        }

        if (empty($result->body['invoiceID'])) {
            return $this->error(LANG_BILLING_ERR);
        }

        return self::PAYMENTURL . $result->body['invoiceID'];
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
        if ($summ != $out_summ) {
            return $this->log(LANG_BILLING_ERR_SUMM . 'OutSum: '.$out_summ);
        }

        $password2 = !empty($this->options['test_mode']) ?
                $this->options['password2_test'] :
                $this->options['password2'];

        $sig = [$out_summ, $op_id, $password2];
        $sig = strtoupper(hash($this->hash_type, implode(':', $sig)));

        if ($sig !== $out_signature) {
            return $this->log(LANG_BILLING_ERR_SIG);
        }

        if (!$model->acceptPayment($operation['id'])) {
            return $this->log(LANG_BILLING_ERR_TRANS);
        }

        return 'OK' . $op_id;
    }

    private function sendPaymentRequest(array $payload) {

        $payload['MerchantLogin'] = $this->options['merchant_login'];

        $signature_params = [
            'OutSum'    => $payload['OutSum'],
            'InvoiceID' => $payload['InvoiceID']
        ];

        if (!empty($payload['Receipt'])) {
            $signature_params['Receipt'] = $payload['Receipt'];
        }

        $payload['SignatureValue'] = $this->generateSignature($signature_params);

        return parent::callHttp(self::PAYMENTCURL, $payload);
    }

    private function generateSignature($params) {

        $required = [
            $this->options['merchant_login'],
            $params['OutSum'],
            $params['InvoiceID']
        ];

        if (!empty($params['Receipt'])) {
            $required[] = $params['Receipt'];
        }

        $required[] = !empty($this->options['test_mode']) ?
                $this->options['password1_test'] :
                $this->options['password1'];

        $hash = implode(':', $required);

        return strtoupper(hash($this->hash_type, $hash));
    }

}
