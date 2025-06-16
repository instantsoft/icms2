<?php

class actionBillingStatusPoll extends cmsAction {

    use \icms\controllers\billing\traits\validatepay;

    protected $use_csrf_token = true;

    public function run($order_id) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $operation = $this->model->getOperation($order_id);

        if (!$operation) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        if ($operation['user_id'] != $this->cms_user->id) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        return $this->cms_template->renderJSON([
            'error'   => false,
            'status'  => intval($operation['status']),
            'balance' => $operation['status'] == modelBilling::STATUS_DONE ?
                    html_spellcount($this->cms_user->balance, $this->options['currency'], null, null, '0') :
                    false
        ]);
    }

}
