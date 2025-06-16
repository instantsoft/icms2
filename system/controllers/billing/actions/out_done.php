<?php
/**
 * @property \modelBilling $model
 */
class actionBillingOutDone extends cmsAction {

    public function run($code) {

        if (!preg_match('/^([a-zA-Z0-9]{32})$/i', $code)) {
            return cmsCore::error404();
        }

        if (!$this->options['is_out']) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return $this->redirectToLogin();
        }

        if (!$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $out = $this->model->forUpdate()->getOutByDoneCode($code);

        if (!$out || $out['status'] == modelBilling::OUT_STATUS_DONE) {
            return cmsCore::error404();
        }

        $success = $this->model->doneOut($out['id']);

        $this->model->endTransaction($success);

        cmsUser::addSessionMessage(LANG_BILLING_OUT_DONE, 'success');

        return $this->redirectToHome();
    }

}
