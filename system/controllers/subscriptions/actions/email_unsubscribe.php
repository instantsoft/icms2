<?php

class actionSubscriptionsEmailUnsubscribe extends cmsAction {

    public function run($confirm_token){

        $subscription = $this->model->getSubscriptionByToken($confirm_token);

        if(!$subscription){
            cmsCore::error404();
        }

        $list_item = $this->model->getSubscriptionItem($subscription['subscription_id']);

        if(!$list_item){
            cmsCore::error404();
        }

        $this->model->unsubscribe($list_item, $subscription);

        cmsEventsManager::hook('unsubscribe', array($list_item, $subscription));

        cmsUser::addSessionMessage(LANG_SBSCR_UNSUBSCRIBE_SUCCESS, 'success');

        $this->redirectToAction('view_list', $list_item['id']);

    }

}
