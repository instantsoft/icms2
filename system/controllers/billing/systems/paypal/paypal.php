<?php

class systemPaypal extends billingPaymentSystem {

    public function getPaymentFormFields($order) {

        return [
            'account'   => $this->options['account'] ?? '',
            'client_id' => $this->options['client_id'] ?? '',
            'paypal'    => new fieldPaypal('paypal', [
                'options' => array_merge($this->options, [
                    'amount'   => $this->getPaymentOrderSumm($order['summ']),
                    'order_id' => $order['id'],
                ])
            ])
        ];
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('order_id', 0);
    }

}
