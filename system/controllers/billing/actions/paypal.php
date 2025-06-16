<?php
/**
 * @property \modelBilling $model
 */
class actionBillingPaypal extends cmsAction {

    use \icms\controllers\billing\traits\validatepay;

    const PAYPAL_OAUTH_API = '%s/v1/oauth2/token/';
    const PAYPAL_ORDER_API = '%s/v2/checkout/orders/%s';

    private $token;
    private $base_url;
    private $account;
    private $client_id;
    private $secret;

    public function run() {

        $action            = $this->request->get('action', '');
        $billing_order_id  = $this->request->get('bid', 0);
        $paypal_order_id   = $this->request->get('pid', '');
        $billing_order_sig = $this->request->get('sig', '');

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!in_array($action, ['check'])) {
            return cmsCore::error404();
        }

        if (!$billing_order_id) {
            return cmsCore::error404();
        }

        if (!$paypal_order_id) {
            return cmsCore::error404();
        }

        if (!$billing_order_sig) {
            return cmsCore::error404();
        }

        $order = $this->model->getOperation($billing_order_id);
        if (!$order) {
            return cmsCore::error404();
        }

        $paypal = $this->getPaymentSystem('paypal');
        if (!$paypal) {
            return cmsCore::error404();
        }

        $this->account   = $paypal->getOption('account');
        $this->client_id = $paypal->getOption('client_id');
        $this->secret    = $paypal->getOption('secret');
        $this->base_url  = $paypal->getPaymentURL();

        $amount = $paypal->getPaymentOrderSumm($order['summ']);

        $correct_sig = md5(implode(':', [$order['id'], $amount, $this->client_id]));
        if ($billing_order_sig != $correct_sig) {
            $this->error(LANG_BILLING_SYSTEM_PAYPAL_ERROR_ORDER);
        }

        $this->token = $this->getPaypalAccessToken();

        if (!$this->token) {
            $this->error(LANG_BILLING_SYSTEM_PAYPAL_ERROR_AUTH);
        }

        if (!$this->validatePaypalOrder($order['id'], $paypal_order_id, $amount)) {
            $this->error(LANG_BILLING_SYSTEM_PAYPAL_ERROR_AUTH);
        }

        $this->success($order['id']);
    }

    private function error($error) {
        return $this->cms_template->renderJSON([
            'success' => false,
            'error'   => $error,
            'url'     => href_to('billing', 'fail')
        ]);
    }

    private function success($billing_order_id) {
        return $this->cms_template->renderJSON([
            'success' => true,
            'error'   => false,
            'url'     => href_to('billing/success/paypal?order_id=' . $billing_order_id)
        ]);
    }

    private function validatePaypalOrder($billing_order_id, $paypal_order_id, $amount) {

        $ch = curl_init(sprintf(self::PAYPAL_ORDER_API, $this->base_url, $paypal_order_id));

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        $result = curl_exec($ch);
        $error  = curl_error($ch);

        if ($error) {
            return false;
        }

        $result = json_decode($result, true);

        if (empty($result['purchase_units'][0]['amount']['value']) || $result['purchase_units'][0]['amount']['value'] != $amount) {
            return false;
        }

        return $this->model->acceptPayment($billing_order_id);
    }

    private function getPaypalAccessToken() {

        $ch = curl_init(sprintf(self::PAYPAL_OAUTH_API, $this->base_url));

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_USERPWD, $this->client_id . ':' . $this->secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US',
        ]);

        $result = curl_exec($ch);
        $error  = curl_error($ch);

        if ($error) {
            return false;
        }

        $result = json_decode($result, true);

        return $result['access_token'] ?? false;
    }

}
