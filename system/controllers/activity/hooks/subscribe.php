<?php

class onActivitySubscribe extends cmsAction {

    public function run($data){

        list($target, $subscribe, $now_create_list_id, $sid) = $data;

        if(empty($subscribe['user_id'])){
            return $data;
        }

        $this->addEntry('subscriptions', 'subscribe', array(
            'user_id'       => $subscribe['user_id'],
            'subject_title' => $target['title'],
            'subject_id'    => $sid,
            'subject_url'   => $target['subject_url']
        ));

        return $data;

    }

}
