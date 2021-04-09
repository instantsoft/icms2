<?php

class actionGroupsGroupLeave extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group) {

        if ($group['access']['is_member'] && !$group['access']['is_owner']) {

            $group = cmsEventsManager::hook('group_before_leave', $group);

            $this->model->deleteMembership($group['id'], $this->cms_user->id);

            $group = cmsEventsManager::hook('group_after_leave', $group);

            cmsUser::addSessionMessage(LANG_GROUPS_LEAVE_MESSAGE, 'info');

            $this->redirectToAction($group['slug']);
        }

        cmsCore::error404();
    }

}
