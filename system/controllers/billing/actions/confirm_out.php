<?php
/**
 * @property \modelBilling $model
 */
class actionBillingConfirmOut extends cmsAction {

    use \icms\controllers\billing\traits\validateout;

    public function run($code) {

        if (!preg_match('/^([a-zA-Z0-9]{32})$/i', $code)) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $out = $this->model->forUpdate()->getOutByCode($code);

        if (!$out || $out['status'] > modelBilling::OUT_STATUS_CREATED) {
            return cmsCore::error404();
        }

        if ($out['user_id'] != $this->cms_user->id) {
            return cmsCore::error404();
        }

        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        if ($out['amount'] > $balance) {

            $this->model->endTransaction($this->model->deleteOut($out['id']));

            cmsUser::addSessionMessage(LANG_BILLING_TRANSFER_INCORRECT_AMOUNT, 'error');

            return $this->redirectToAction('out');
        }

        $success = $this->confirmOut($out);

        $this->model->endTransaction($success);

        cmsUser::addSessionMessage(LANG_BILLING_OUT_CONFIRMED, 'success');

        return $this->redirectToAction('out');
    }

}
