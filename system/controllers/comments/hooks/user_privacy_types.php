<?php

class onCommentsUserPrivacyTypes extends cmsAction {

    public function run(){

        $types['view_user_comments'] = array(
            'title'   => sprintf(LANG_USERS_PRIVACY_PROFILE_CTYPE, LANG_COMMENT10),
            'options' => array('anyone', 'friends')
        );

        return $types;

    }

}
