<?php

class systemTest extends billingPaymentSystem {

    public function getPaymentFormFields($order) {
        return [
            'order_id' => $order['id'],
        ];
    }

    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('order_id', 0);
    }

    public function preparePayment(cmsRequest $request, modelBilling $model) {

        $order_id = $request->get('order_id', 0);

        if (!$model->acceptPayment($order_id)) {
            return false;
        }

        return href_to('billing', 'success', [$this->name], ['order_id' => $order_id]);
    }

}
