<?php

class onSubscriptionsContentAfterAddApprove extends cmsAction {

    public function run($data){

        if(!empty($data['item']['is_private'])){
            return $data;
        }

        $is_pub = (isset($data['item']['is_pub']) ? $data['item']['is_pub'] : 1);

        if(empty($is_pub)){
            return $data;
        }

        // здесь только типы контента
        $ctype = cmsCore::getModel('content')->getContentTypeByName($data['ctype_name']);
        if(!$ctype){
            return $data;
        }

        $subscriptions_list = $this->model->filterEqual('controller', 'content')->
                filterEqual('subject', $data['ctype_name'])->
                filterGt('subscribers_count', 0)->
                getSubscriptionsList();

        if(!$subscriptions_list){
            return $data;
        }

        cmsQueue::pushOn('subscriptions', array(
            'controller' => $this->name,
            'hook'       => 'send_letters',
            'params'     => array(
                'content', $data['ctype_name'], array($data['item'])
            )
        ));

        return $data;

    }

}
