<?php

class systemLiqpay extends billingPaymentSystem {

    public function __construct(array $system) {

        parent::__construct($system);

        cmsCore::includeFile('system/controllers/billing/systems/liqpay/liqpay.sdk.php');
    }

    public function getPaymentFormFields($order) {

        $liqpay = new LiqPay($this->options['public_key'] ?? '', $this->options['private_key'] ?? '');

        $params = $liqpay->cnb_form_raw([
            'amount'      => $this->getPaymentOrderSumm($order['summ']),
            'currency'    => $this->options['currency'] ?? '',
            'action'      => $this->options['action'] ?? '',
            'order_id'    => $order['id'],
            'description' => $order['description'],
            'server_url'  => href_to_abs('billing', 'process', $this->name, ['tid' => $order['id']]),
            'version'     => '3'
        ]);

        unset($params['url']);

        return $params;
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $data      = $request->get('data', '');
        $signature = $request->get('signature'. '');

        $request_log = json_encode($_REQUEST);

        if (!$data || !$signature) {
            return $this->log("No data in request: {$request_log}");
        }

        $correct_signature = base64_encode(sha1($this->options['private_key'] . $data . $this->options['private_key'], 1));

        if ($signature != $correct_signature) {
            $this->log("Signature doesnt match: {$signature} != {$correct_signature}");
            return $this->log("Request Log: {$request_log}");
        }

        $liqpay = new LiqPay($this->options['public_key'], $this->options['private_key']);

        $params = $liqpay->decode_params($data);

        $op_id    = $params['order_id'];
        $status   = $params['status'];
        $out_summ = $params['amount'];

        if (!in_array($status, ['success', 'wait_accept'])) {
            $this->log("Bad order status: {$status}");
            return $this->log("Request Log: {$request_log}");
        }

        if (!$op_id) {
            $this->log("Order Id not found");
            return $this->log("Request Log: {$request_log}");
        }

        $operation = $model->getOperation($op_id);

        if (!$operation) {
            $this->log("Bad Order Id: {$op_id}");
            return $this->log("Request Log: {$request_log}");
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            $this->log("Bad Order Status: {$op_id}, {$operation['status']}");
            return $this->log("Request Log: {$request_log}");
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $out_summ) {
            $this->log("Bad Summ: {$summ} != {$out_summ}");
            return $this->log("Request Log: {$request_log}");
        }

        return $model->acceptPayment($op_id);
    }

}
