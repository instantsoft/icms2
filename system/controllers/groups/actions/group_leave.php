<?php

class actionGroupsGroupLeave extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if ($group['access']['is_member'] && !$group['access']['is_owner']){

            $group = cmsEventsManager::hook('group_before_leave', $group);

            $this->model->deleteMembership($group['id'], $this->cms_user->id);

            cmsCore::getController('activity')->addEntry($this->name, 'leave', array(
                'subject_title' => $group['title'],
                'subject_id'    => $group['id'],
                'subject_url'   => href_to_rel($this->name, $group['slug']),
                'group_id'      => $group['id']
            ));

            cmsUser::addSessionMessage(LANG_GROUPS_LEAVE_MESSAGE, 'info');

            $this->redirectToAction($group['slug']);

        }

        cmsCore::error404();

    }

}
