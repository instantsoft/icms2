<?php

class onUsersUserPrivacyTypes extends cmsAction {

    public function run(){

        $types = array();

        $types['users_profile_view'] = array(
            'title'   => LANG_USERS_PRIVACY_PROFILE_VIEW,
            'options' => array('', 'anyone', 'friends')
        );

        if(!empty($this->options['is_friends_on'])){
            $types['users_friendship'] = array(
                'title'   => LANG_USERS_PRIVACY_FRIENDSHIP,
                'options' => array('', 'anyone')
            );
        }

        return $types;

    }

}
