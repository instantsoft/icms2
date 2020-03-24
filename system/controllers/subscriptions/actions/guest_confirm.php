<?php

class actionSubscriptionsGuestConfirm extends cmsAction {

    public function run($confirm_token){

        if($this->cms_user->is_logged){ cmsCore::error404(); }

        $subscription = $this->model->getSubscriptionByToken($confirm_token);

        if(!$subscription){ cmsCore::error404(); }

        $this->model->verifySubscription($confirm_token);

        $this->model->reCountSubscribers($subscription['subscription_id']);

        cmsUser::addSessionMessage(LANG_SBSCR_VERIFY_SUCCESS, 'success');

        $this->redirectToHome();

    }

}
