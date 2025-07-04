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

        $operation = $model->getOperation($order_id);
        if (!$operation || $operation['user_id'] != cmsUser::get('id')) {
            return $this->error(LANG_BILLING_ERR_ORDER_ID);
        }

        if (!$model->acceptPayment($operation['id'])) {
            return $this->error(LANG_BILLING_ERR_TRANS);
        }

        return href_to('billing', 'success', [$this->name], ['order_id' => $operation['id']]);
    }

}
