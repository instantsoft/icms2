<?php

class actionGroupsGroupClosed extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        $group['description'] = cmsEventsManager::hook('html_filter', $group['description']);

        return $this->cms_template->render('group_closed', array(
            'group' => $group,
            'user'  => $this->cms_user
        ));

    }

}
