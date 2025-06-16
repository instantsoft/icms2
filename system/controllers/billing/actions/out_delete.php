<?php
/**
 * @property \modelBilling $model
 */
class actionBillingOutDelete extends cmsAction {

    use \icms\controllers\billing\traits\validateout;

    public function run($id) {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $out = $this->model->forUpdate()->getOut($id);
        if (!$out) {
            return cmsCore::error404();
        }

        if (($out['user_id'] != $this->cms_user->id) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        if (($out['status'] != modelBilling::OUT_STATUS_CREATED) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $success = $this->model->deleteOut($id);

        $this->model->endTransaction($success);

        cmsUser::addSessionMessage(LANG_BILLING_OUT_DELETE, 'success');

        return $this->redirectToAction('out');
    }

}
