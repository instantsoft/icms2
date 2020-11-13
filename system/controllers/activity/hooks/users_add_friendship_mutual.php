<?php

class onActivityUsersAddFriendshipMutual extends cmsAction {

    public function run($data){

        list($user_id, $friend) = $data;

        $this->addEntry('users', 'friendship', array(
            'subject_title' => $friend['nickname'],
            'subject_id'    => $friend['id'],
            'subject_url'   => href_to_rel('users', (empty($friend['slug']) ? $friend['id'] : $friend['slug']))
        ));

        return [$user_id, $friend];
    }

}
