<?php

class onUsersUserPrivacyTypes extends cmsAction {

    public function run(){

        $types = array();

        $types['users_profile_view'] = array(
            'title'   => LANG_USERS_PRIVACY_PROFILE_VIEW,
            'options' => array('anyone', 'friends')
        );

        if ($this->options['is_wall']) {

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
