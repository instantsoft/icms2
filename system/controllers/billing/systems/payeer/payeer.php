<?php
/**
 * PAYEER
 * https://payeer.com/ru/account/api/
 */
class systemPayeer extends billingPaymentSystem {

    private $trusted_list = [
        '149.202.17.210',
        '185.71.65.92',
        '185.71.65.189'
    ];

    public function getPaymentFormFields($order) {

        $data = [
            'm_curr'    => $this->options['curr'] ?? '',
            'm_shop'    => $this->options['shop_id'] ?? '',
            'm_amount'  => $this->getPaymentOrderSumm($order['summ']),
            'm_orderid' => $order['id'],
            'm_desc'    => base64_encode($order['description'])
        ];

        $ar_hash = [
            $data['m_shop'],
            $data['m_orderid'],
            $data['m_amount'],
            $data['m_curr'],
            $data['m_desc'],
            $this->options['secret_key'] ?? ''
        ];

        $data['m_sign'] = strtoupper(hash('sha256', implode(':', $ar_hash)));

        return $data;
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('m_orderid', 0);
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $op_id = $request->get('m_orderid', 0);
        if (!$op_id) {
            return $this->log([$op_id, LANG_BILLING_ERR_ORDER_ID]);
        }

        $ip = new cmsIp($this->trusted_list);

        if (!$ip->isIPTrusted(cmsUser::getIp())) {
            return $this->log([$op_id, 'Attempting to connect from ' . cmsUser::getIp()]);
        }

        $operation = $model->getOperation($op_id);
        if (!$operation) {
            return $this->log([$op_id, LANG_BILLING_ERR_ORDER_ID]);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->log([$op_id, LANG_BILLING_ERR_STATUS]);
        }

        $data = $request->getAll();

        foreach ([
            'm_operation_id', 'm_operation_ps', 'm_operation_date',
            'm_operation_pay_date', 'm_shop', 'm_amount', 'm_curr',
            'm_desc', 'm_status', 'm_sign'
            ] as $param_name) {
            if (!array_key_exists($param_name, $data) || is_array($data[$param_name])) {
                return $this->log([$op_id, sprintf(LANG_BILLING_ERR_PARAM, $param_name)]);
            }
        }

        $ar_hash = [
            $data['m_operation_id'],
            $data['m_operation_ps'],
            $data['m_operation_date'],
            $data['m_operation_pay_date'],
            $data['m_shop'],
            $data['m_orderid'],
            $data['m_amount'],
            $data['m_curr'],
            $data['m_desc'],
            $data['m_status'],
            $this->options['secret_key'] ?? ''
        ];

        $sign_hash = strtoupper(hash('sha256', implode(':', $ar_hash)));

        if ($sign_hash !== $data['m_sign']) {
            return $this->log([$op_id, LANG_BILLING_ERR_SIG]);
        }

        if ('success' !== $data['m_status']) {
            return $this->log([$op_id, 'Error Status: ' . $data['m_status']]);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);
        if ($summ != $data['m_amount']) {
            return $this->log([$op_id, LANG_BILLING_ERR_SUMM . 'OutSum: '.$data['m_amount']]);
        }

        if (!$model->acceptPayment($operation['id'])) {
            return $this->log([$op_id, LANG_BILLING_ERR_TRANS]);
        }

        return $op_id.'|success';
    }

    protected function log($text) {

        [$op_id, $text] = $text;

        parent::log($text);

        return $op_id.'|error';
    }

}
