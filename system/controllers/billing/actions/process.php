<?php
/**
 * @property \modelBilling $model
 */
class actionBillingProcess extends cmsAction {

    public function run($system_name) {

        if ($this->options['in_mode'] === 'disabled') {
            return cmsCore::error404();
        }

        if ($this->validate_sysname($system_name) !== true) {
            return cmsCore::error404();
        }

        $system = $this->getPaymentSystem($system_name);
        if (!$system) {
            return cmsCore::error404();
        }

        $response = $system->processPayment($this->request, $this->model);

        $result = [
            'headers' => [],
            'body'    => ''
        ];

        if (is_string($response)) {

            $result['body'] = $response;

        } else if (is_array($response)) {

            $result = array_merge($result, $response);

        } else if ($response === false) {

            $this->cms_core->response->setStatusCode(500);

            $result['body'] = 'An error occurred in payment confirmation processing';
        }

        $this->cms_core->response->addHeaders($result['headers']);

        return $this->cms_core->response->setContent($result['body'])->sendAndExit();
    }

}
