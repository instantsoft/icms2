<?php

class actionGroupsGroupClosed extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();

        return cmsTemplate::getInstance()->render('group_closed', array(
            'group' => $group,
            'user' => $user,
        ));

    }

}
