<?php
/**
 * @property \modelSubscriptions $model
 */
class actionSubscriptionsEmailUnsubscribe extends cmsAction {

    public function run($confirm_token) {

        $subscription = $this->model->getSubscriptionByToken($confirm_token);

        if (!$subscription) {
            return cmsCore::error404();
        }

        $list_item = $this->model->getSubscriptionItem($subscription['subscription_id']);

        if (!$list_item) {
            return cmsCore::error404();
        }

        $this->model->unsubscribe($list_item, $subscription);

        cmsEventsManager::hook('unsubscribe', [$list_item, $subscription]);

        cmsUser::addSessionMessage(LANG_SBSCR_UNSUBSCRIBE_SUCCESS, 'success');

        return $this->redirectToAction('view_list', $list_item['id']);
    }

}
