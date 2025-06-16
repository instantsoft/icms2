<?php
/**
 * @property \modelBilling $model
 */
class actionBillingPrepare extends cmsAction {

    // Подключаем валидацию перед выполнением метода run
    use \icms\controllers\billing\traits\validatepay;

    protected $use_csrf_token = true;

    public function run($system_name) {

        if ($this->validate_sysname($system_name) !== true) {
            return cmsCore::error404();
        }

        $system = $this->getPaymentSystem($system_name);
        if (!$system) {
            return cmsCore::error404();
        }

        $result = $system->preparePayment($this->request, $this->model);

        if (is_string($result)) {
            return $this->redirect($result);
        }

        cmsUser::addSessionMessage($system->getLastError(), 'error');

        return $this->redirectToAction('deposit');
    }

}
