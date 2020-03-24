<?php

class onActivityUserAddStatusAfter extends cmsAction {

    public function run($data){

        list($status_id,
            $user_id,
            $content,
            $status_content,
            $status_link) = $data;

        $this->addEntry('users', 'status', array(
            'subject_title' => $status_content,
            'reply_url'     => $status_link
        ));

        return [
            $status_id,
            $user_id,
            $content,
            $status_content,
            $status_link
        ];

    }

}
