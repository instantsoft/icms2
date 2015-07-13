<?php

class actionGroupsGroupJoin extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();

        if ($this->model->getMembership($group['id'], $user->id)){
            $this->redirectToAction($group['id']);
        }

        $invite = $this->model->getInvite($group['id'], $user->id);

        if ($group['join_policy'] != groups::JOIN_POLICY_FREE && !$invite){
            cmsCore::error404();
        }

        $this->model->addMembership($group['id'], $user->id);

        if ($invite){ $this->model->deleteInvite($invite['id']); }

        cmsCore::getController('activity')->addEntry($this->name, "join", array(
            'subject_title' => $group['title'],
            'subject_id' => $group['id'],
            'subject_url' => href_to($this->name, $group['id']),
            'group_id' => $group['id']
        ));

        cmsUser::addSessionMessage(LANG_GROUPS_JOIN_MESSAGE, 'success');

        $this->redirectToAction($group['id']);

    }

}
