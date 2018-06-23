<?php

class onActivityUnsubscribe extends cmsAction {

    public function run($data){

        list($target, $subscribe) = $data;

        if(empty($subscribe['user_id'])){
            return $data;
        }

        $this->deleteEntry('subscriptions', 'subscribe', $subscribe['id']);

        return $data;

    }

}
