<?php
/**
 * @property \modelBackendBilling $model
 */
class actionBillingPlansFreeAll extends cmsAction {

    public function run($id = false) {

        if (!$id || !cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->startTransaction();

        $success = true;

        $plan = $this->model->getPlan($id);

        if (!$plan || $plan['prices']) {
            return cmsCore::error404();
        }

        $user_ids = $this->model->limit(false)->selectOnly('i.id', 'id')->
                filterIsNull('plan_id')->get('{users}', function($user) {
            return $user['id'];
        }) ?: [];

        foreach ($user_ids as $user_id) {
            $success = $success && $this->model->addUserPlanSubscribtion($user_id, $plan, []);
        }

        cmsUser::addSessionMessage(sprintf(LANG_BILLING_PLAN_RUN_FREE_SUCCESS, $plan['title'], count($user_ids)), 'success');

        $this->model->endTransaction($success);

        return $this->redirectToAction('plans');
    }

}
