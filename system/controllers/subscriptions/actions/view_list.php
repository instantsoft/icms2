<?php

class actionSubscriptionsViewList extends cmsAction {

    public function run($subscription_id){

        $subscription = $this->model->getSubscriptionItem($subscription_id);

        if(!$subscription){ return cmsCore::error404(); }

        $controller = cmsCore::getController($subscription['controller'], $this->request);

        $list_url = $controller->runHook('subscribe_item_url', array($subscription), false);

        $this->redirect($list_url ? rel_to_href($list_url) : href_to_home());

    }

}
