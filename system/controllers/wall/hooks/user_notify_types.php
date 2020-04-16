<?php

class onWallUserNotifyTypes extends cmsAction {

    public function run(){

        $users_options = cmsCore::getController('users')->getOptions();

        if (!$users_options['is_wall']) { return false; }

        return array(
            'users_wall_write' => array(
                'title' => LANG_WALL_NOTIFY_NEW,
                'options' => array('', 'email')
            ),
        );

    }

}
