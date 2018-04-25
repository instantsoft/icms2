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
