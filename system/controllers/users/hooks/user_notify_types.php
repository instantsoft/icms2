<?php

class onUsersUserNotifyTypes extends cmsAction {

    public function run(){

        return array(
            'users_friend_add' => array(
                'title' => LANG_USERS_NOTIFY_FRIEND_ADD,
                'options' => array('both', 'pm')
            ),
            'users_friend_accept' => array(
                'title' => LANG_USERS_NOTIFY_FRIEND_ACCEPT,
                'options' => array('', 'pm')
            ),
            'users_friend_delete' => array(
                'title' => LANG_USERS_NOTIFY_FRIEND_DELETE,
            ),
        );

    }

}
