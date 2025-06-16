<?php
/**
 * @property \modelUsers $model_users
 * @property \modelBilling $model
 * @property \messages $controller_messages
 */
class actionBillingConfirmTf extends cmsAction {

    use \icms\controllers\billing\traits\validatetransfer;

    public function run($code) {

        if (!preg_match('/^([a-zA-Z0-9]{32})$/i', $code)) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $transfer = $this->model->forUpdate()->getTransferByCode($code);

        if (!$transfer || $transfer['status'] || $transfer['from_id'] != $this->cms_user->id) {
            return cmsCore::error404();
        }

        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        if ($transfer['amount'] > $balance) {

            $this->model->endTransaction($this->model->deleteTransfer($transfer['id']));

            cmsUser::addSessionMessage(LANG_BILLING_TRANSFER_INCORRECT_AMOUNT, 'error');

            return $this->redirectToAction('transfer', [$transfer['to_id']]);
        }

        $success = $this->acceptTransfer($transfer);

        $this->model->endTransaction($success);

        $to_user = $this->model_users->getUser($transfer['to_id']);

        if (!$success) {
            cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');
        } else {
            cmsUser::addSessionMessage(LANG_BILLING_TRANSFER_SUCCESS, 'success');
        }

        return $this->redirect(href_to_profile($to_user));
    }

}
