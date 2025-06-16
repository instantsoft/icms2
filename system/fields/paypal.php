<?php
/**
 * Поле используется только для Биллинга
 */
class fieldPaypal extends cmsFormField {

    public $title       = 'PayPal';
    public $is_public   = false;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = false;
    public $var_type    = 'string';
    public $type        = 'text';

    public function parse($value) {
        return html($value, false);
    }

    public function getInput($value) {

        $client_id = $this->getOption('client_id');

        $url_params = [
            'client-id' => $client_id,
            'currency'  => $this->getOption('currency')
        ];

        $paypal_script_url = 'https://www.paypal.com/sdk/js?' . http_build_query($url_params);

        cmsTemplate::getInstance()->addJS($paypal_script_url);
        cmsTemplate::getInstance()->addControllerJS('paypal');

        $amount    = $this->getOption('amount');
        $order_id  = $this->getOption('order_id');
        $order_sig = md5(implode(':', [$order_id, $amount, $client_id]));

        return '<div id="paypal-button-container" style="min-height:50px" data-amount="' . $amount . '" data-bid="' . $order_id . '" data-bid-sig="' . $order_sig . '"></div>';
    }

}
