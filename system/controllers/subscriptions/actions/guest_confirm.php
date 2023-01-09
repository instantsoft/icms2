<?php
/**
 * @property \modelSubscriptions $model
 */
class actionSubscriptionsGuestConfirm extends cmsAction {

    public function run($confirm_token) {

        if ($this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        $subscription = $this->model->getSubscriptionByToken($confirm_token);

        if (!$subscription) {
            return cmsCore::error404();
        }

        $this->model->verifySubscription($confirm_token);

        $this->model->reCountSubscribers($subscription['subscription_id']);

        cmsUser::addSessionMessage(LANG_SBSCR_VERIFY_SUCCESS, 'success');

        return $this->redirectToHome();
    }

}
