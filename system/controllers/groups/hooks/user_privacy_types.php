<?php

class onGroupsUserPrivacyTypes extends cmsAction {

    public function run(){

        $types['view_user_groups'] = array(
            'title'   => sprintf(LANG_USERS_PRIVACY_PROFILE_CTYPE, LANG_GROUPS10),
            'options' => array('anyone', 'friends')
        );

        $types['invite_group_users'] = array(
            'title'   => LANG_GROUPS_INVITE_GROUP_USERS,
            'options' => array('', 'anyone', 'friends')
        );

        return $types;

    }

}
