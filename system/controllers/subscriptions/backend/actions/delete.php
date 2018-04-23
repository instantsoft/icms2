<?php

class actionSubscriptionsDelete extends cmsAction {

    public function run($id = null){

        if($id){
            $items = array($id);
        } else {
            $items = $this->request->get('selected', array());
        }

        if (!$items) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        foreach ($items as $id) {
            if(is_numeric($id)){

                $subscriptions = $this->model->deleteSubscriptionsList($id);

                if($subscriptions){
                    foreach ($subscriptions as $subscription) {
                        $this->controller_activity->deleteEntry('subscriptions', 'subscribe', $subscription['id']);
                    }
                }

            }
        }

        cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

        $this->redirectToAction('list');

    }

}
