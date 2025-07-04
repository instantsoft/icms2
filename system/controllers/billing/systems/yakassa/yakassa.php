<?php
/**
 * https://yookassa.ru/developers
 */
class systemYakassa extends billingPaymentSystem {

    const API_ENDPOINT = 'https://api.yookassa.ru/v3';

    const PAYMENTS_PATH = '/payments';

    private $trusted_list = [
        '185.71.76.0/27',
        '185.71.77.0/27',
        '77.75.153.0/25',
        '77.75.154.128/25',
        '77.75.156.11',
        '77.75.156.35',
        [
            '2a02:5180:0000:1509:0000:0000:0000:0000',
            '2a02:5180:0000:1509:ffff:ffff:ffff:ffff'
        ],
        [
            '2a02:5180:0000:2655:0000:0000:0000:0000',
            '2a02:5180:0000:2655:ffff:ffff:ffff:ffff'
        ],
        [
            '2a02:5180:0000:1533:0000:0000:0000:0000',
            '2a02:5180:0000:1533:ffff:ffff:ffff:ffff'
        ],
        [
            '2a02:5180:0000:2669:0000:0000:0000:0000',
            '2a02:5180:0000:2669:ffff:ffff:ffff:ffff'
        ]
    ];

    /**
     * Успешно оплачен покупателем, ожидает подтверждения магазином
     */
    const PAYMENT_WAITING_FOR_CAPTURE = 'payment.waiting_for_capture';

    /**
     * Успешно оплачен и подтвержден магазином
     */
    const PAYMENT_SUCCEEDED = 'payment.succeeded';

    /**
     * Неуспех оплаты или отменен магазином
     */
    const PAYMENT_CANCELED = 'payment.canceled';

    public function getPaymentFormFields($order) {
        return [
            'order_id' => $order['id'],
            'email'    => $order['email'],
            'comment'  => $order['description']
        ];
    }

    public function preparePayment(cmsRequest $request, modelBilling $model) {

        $order_id = $request->get('order_id', 0);

        $operation = $model->getOperation($order_id);
        if (!$operation || $operation['user_id'] != cmsUser::get('id')) {
            return $this->error(LANG_BILLING_ERR_ORDER_ID);
        }

        $data = [
            'amount' => [
                'value' => $this->getPaymentOrderSumm($operation['summ']),
                'currency' => 'RUB'
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => href_to_abs('billing', 'success', 'yakassa') . '?tid=' . $operation['id']
            ],
            'metadata' => [
                'order_id' => $operation['id']
            ],
            'description' => mb_substr(strip_tags($request->get('comment', '')), 0, 128)
        ];

        if (!empty($this->options['fiscal_on'])) {
            $data['receipt'] = $this->buildReceipt($data['description'], $data['amount']['value'], $operation['user_email']);
        }

        $result = $this->execute(self::PAYMENTS_PATH, $data);

        if ($result->error) {
            return $this->error($result->error);
        }

        if (!is_array($result->body)) {
            return $this->error(LANG_BILLING_ERR);
        }

        if (isset($result->body['type']) && $result->body['type'] === 'error') {
            return $this->error($result->body['description']);
        }

        if (!isset($result->body['confirmation']['confirmation_url'])) {
            return $this->error(LANG_BILLING_ERR);
        }

        return $result->body['confirmation']['confirmation_url'];
    }

    private function buildReceipt($description, $summ, $email) {
        return [
            'customer' => [
                'email' => $email
            ],
            'items' => [
                [
                    'description'    => $description,
                    'quantity'       => 1,
                    'amount' => [
                        'value' => $summ,
                        'currency' => 'RUB'
                    ],
                    'measure' => 'another',
                    'payment_mode'    => $this->options['fiscal_method'],
                    'payment_subject' => $this->options['fiscal_object'],
                    'vat_code'        => $this->options['fiscal_tax']
                ]
            ]
       ];
    }

    public function processPayment(cmsRequest $request, modelBilling $model) {

        $ip = new cmsIp($this->trusted_list);

        if (!$ip->isIPTrusted(cmsUser::getIp())) {
            return $this->log('Attempting to connect from ' . cmsUser::getIp());
        }

        $source = (string) $request->getContent();
        $data = json_decode($source, true);

        if ($data === false) {
            return $this->log('JSON Input Error: ' . json_last_error_msg());
        }

        if (!array_key_exists('event', $data) || !is_string($data['event'])) {
            return $this->log(sprintf(LANG_BILLING_ERR_PARAM, 'event'));
        }

        if (!in_array($data['event'], [self::PAYMENT_CANCELED, self::PAYMENT_WAITING_FOR_CAPTURE, self::PAYMENT_SUCCEEDED])) {
            return $this->log(sprintf(LANG_BILLING_ERR_PARAM, 'event'));
        }

        if (!array_key_exists('object', $data) || !is_array($data['object'])) {
            return $this->log(sprintf(LANG_BILLING_ERR_PARAM, 'object'));
        }

        $object = $data['object'];

        if (empty($object['metadata']['order_id']) || !is_numeric($object['metadata']['order_id'])) {
            return $this->log(LANG_BILLING_ERR_ORDER_ID);
        }

        if (empty($object['amount']['value']) || !is_numeric($object['amount']['value'])) {
            return $this->log(sprintf(LANG_BILLING_ERR_PARAM, 'amount'));
        }

        $operation = $model->getOperation($object['metadata']['order_id']);
        if (!$operation) {
            return $this->log(LANG_BILLING_ERR_ORDER_ID);
        }

        if ($operation['status'] != modelBilling::STATUS_CREATED) {
            return $this->log(LANG_BILLING_ERR_STATUS);
        }

        $summ = $this->getPaymentOrderSumm($operation['summ']);

        if ($summ != $object['amount']['value']) {
            return $this->log(LANG_BILLING_ERR_SUMM . 'withdraw_amount: '.$object['amount']['value']);
        }

        // Отменён
        if ($data['event'] === self::PAYMENT_CANCELED) {

            $model->cancelPayment($operation['id']);

            return true;
        }

        // Двухстадийный платеж не используем
        if ($data['event'] === self::PAYMENT_SUCCEEDED) {

            if (!$model->acceptPayment($operation['id'])) {
                return $this->log(LANG_BILLING_ERR_TRANS);
            }

            return true;
        }

        $this->log('Unprocessed request: ' . $data['event']);

        // На все другие уведомления отвечаем кодом 200
        return true;
    }

    private function execute(string $path, array $data, array $headers = []) {

        $attempts = 3;
        $response = $this->callHttp($path, $data, $headers);

        while (in_array($response->http_code, [202, 500], true) && $attempts > 0) {
            --$attempts;
            $response = $this->callHttp($path, $data, $headers);
        }

        return $response;
    }

    protected function callHttp(string $path, $data, array $headers = []) {

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Idempotence-Key: ' . uniqid();
        $headers[] = 'Authorization: Basic ' . base64_encode($this->options['shop_id'] . ':' . $this->options['key']);

        return parent::callHttp(self::API_ENDPOINT . $path, json_encode($data), $headers);
    }

}
