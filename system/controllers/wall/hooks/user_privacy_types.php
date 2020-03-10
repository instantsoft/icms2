<?php

class onWallUserPrivacyTypes extends cmsAction {

    public function run(){

        $users_options = cmsCore::getController('users')->getOptions();

        $types = [];

        if ($users_options['is_wall']) {

            $types['users_profile_wall'] = array(
                'title'   => LANG_USERS_PRIVACY_PROFILE_WALL,
                'options' => array('', 'anyone', 'friends')
            );

            $types['users_profile_wall_reply'] = array(
                'title'   => LANG_USERS_PRIVACY_PROFILE_WALL_REPLY,
                'options' => array('', 'anyone', 'friends')
            );

        }

        return $types;

    }

}
