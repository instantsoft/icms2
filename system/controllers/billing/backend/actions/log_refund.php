<?php
/**
 * @property \modelBilling $model
 */
class actionBillingLogRefund extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $operation = $this->model->forUpdate()->getOperation($id);

        if (!$operation || $operation['status'] != modelBilling::STATUS_DONE) {
            return cmsCore::error404();
        }

        if ($operation['system_id']) {
            return cmsCore::error404();
        }

        $success = $this->model->refundPayment($operation);

        $this->model->endTransaction($success);

        if ($success) {
            cmsEventsManager::hook('billing_after_refund_payment', $operation);
        }

        cmsUser::addSessionMessage(LANG_BILLING_OUT_CANCEL, 'success');

        return $this->redirectToAction('log');
    }

}
