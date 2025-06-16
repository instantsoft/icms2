<?php

class systemQiwi extends billingPaymentSystem {

    const BILL_URL    = 'https://w.qiwi.com/api/v2/prv/{shop_id}/bills/{order_id}';
    const SUCCESS_URL = 'billing/success/qiwi?tid=';
    const FAIL_URL    = 'billing/fail';

    public function getPaymentFormFields($order) {

        return [
            'summ'     => $this->getPaymentOrderSumm($order['summ']),
            'order_id' => $order['id'],
            'comment'  => $order['description'],
            'phone'    => new fieldString('phone', [
                'title' => LANG_BILLING_SYSTEM_QIWI_PHONE,
                'hint'  => LANG_BILLING_SYSTEM_QIWI_PHONE_HINT
            ])
        ];
    }

    public function preparePayment(cmsRequest $request, modelBilling $model) {

        $shop_id      = $this->options['shop_id'];
        $api_id       = $this->options['api_id'];
        $api_password = $this->options['api_password'];

        $order_id = $request->get('order_id', 0);
        $phone    = $request->get('phone', '');
        $phone    = str_replace([' ', '-', '(', ')'], '', trim($phone, '+'));

        $data = [
            'user'       => 'tel:+' . $phone,
            'amount'     => $request->get('summ', ''),
            'ccy'        => 'RUB',
            'comment'    => $request->get('comment', ''),
            'lifetime'   => date('c', time() + 60 * 60 * 24 * 2),
            'pay_source' => 'qw',
            'prv_name'   => cmsConfig::get('sitename')
        ];

        $url = string_replace_keys_values(self::BILL_URL, [
            'shop_id'  => $shop_id,
            'order_id' => $order_id,
            'api_id'   => $api_id
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $api_id . ':' . $api_password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false) {
            return $this->error(curl_error($ch));
        }

        $success_url = href_to_abs(self::SUCCESS_URL . $order_id);
        $fail_url    = href_to_abs(self::FAIL_URL);

        return 'https://w.qiwi.com/order/external/main.action?' . http_build_query([
            'shop'        => $shop_id,
            'transaction' => $order_id,
            'successUrl'  => $success_url,
            'failUrl'     => $fail_url,
            'qiwi_phone'  => $phone
        ], '', '&');
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $this->log('processPayment Start');

        $op_id         = $request->get('bill_id', 0);
        $out_summ      = $request->get('amount', '');
        $out_status    = $request->get('status', '');
        $out_signature = $request->getHeader('X-Api-Signature');

        if (!$op_id) {
            return $this->result(false);
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            return $this->result(false);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->result(false);
        }

        if ($out_status != 'paid') {
            return $this->result(false);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $out_summ) {
            return $this->result(false);
        }

        $params = $request->getAll();
        sort($params, SORT_STRING);

        $sig = hash_hmac('sha1', implode('|', $params), $this->options['password']);
        $sig = base64_encode($sig);

        $this->log("QIWI SIG:\n\n{$out_signature}\n\nMY SIG:\n\n{$sig}");

        return $model->acceptPayment($op_id);
    }

    private function result($code = 0) {

        if (!$code) {
            $code = 151;
        } else {
            $code = 0;
        }

        return $this->processPaymentResult('<?xml version="1.0"?><result><result_code>' . $code . '</result_code></result>', [
            'Content-Type' => 'text/xml'
        ]);
    }

}
