<?php

class onGroupsUserNotifyTypes extends cmsAction {

    public function run(){

        return array(
            'groups_invite' => array(
                'title'   => LANG_GROUPS_NOTIFY_INVITE,
                'default' => 'both'
            )
        );

    }

}
