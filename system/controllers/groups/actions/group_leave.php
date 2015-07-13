<?php

class actionGroupsGroupLeave extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();

        $is_member = $this->model->getMembership($group['id'], $user->id);
        $is_owner = $group['owner_id'] == $user->id;

        if ($is_member && !$is_owner){

            $this->model->deleteMembership($group['id'], $user->id);

            cmsCore::getController('activity')->addEntry($this->name, "leave", array(
                'subject_title' => $group['title'],
                'subject_id' => $group['id'],
                'subject_url' => href_to($this->name, $group['id']),
                'group_id' => $group['id']
            ));

        }

        cmsUser::addSessionMessage(LANG_GROUPS_LEAVE_MESSAGE, 'info');

        $this->redirectToAction($group['id']);

    }

}
