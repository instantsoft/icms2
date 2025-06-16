<?php
/**
 * @property \modelBilling $model
 */
class actionBillingOutsDone extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $out = $this->model->forUpdate()->getOut($id);

        if (!$out || $out['status'] == modelBilling::OUT_STATUS_DONE) {
            return cmsCore::error404();
        }

        $success = $this->model->doneOut($out['id']);

        $this->model->endTransaction($success);

        return $this->redirectToAction('outs');
    }

}
