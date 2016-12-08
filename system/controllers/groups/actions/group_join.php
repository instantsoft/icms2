<?php

class actionGroupsGroupJoin extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if ($this->model->getMembership($group['id'], $this->cms_user->id)){
            $this->redirectToAction($group['id']);
        }

        $invite = $this->model->getInvite($group['id'], $this->cms_user->id);

        if ($group['join_policy'] != groups::JOIN_POLICY_FREE && !$invite){
            cmsCore::error404();
        }

        $result = cmsEventsManager::hook('group_before_join', array(
            'allow'  => true,
            'group'  => $group,
            'invite' => $invite
        ));

        if (!$result['allow']){

            if(isset($result['access_text'])){

                cmsUser::addSessionMessage($result['access_text'], 'error');

                if(isset($result['redirect_url'])){
                    $this->redirect($result['redirect_url']);
                } else {
                    $this->redirectToAction($group['id']);
                }

            }

            cmsCore::error404();

        }

        $group  = $result['group'];
        $invite = $result['invite'];

        $this->model->addMembership($group['id'], $this->cms_user->id);

        if ($invite){ $this->model->deleteInvite($invite['id']); }

        cmsCore::getController('activity')->addEntry($this->name, 'join', array(
            'subject_title' => $group['title'],
            'subject_id'    => $group['id'],
            'subject_url'   => href_to_rel($this->name, $group['id']),
            'group_id'      => $group['id']
        ));

        cmsUser::addSessionMessage(LANG_GROUPS_JOIN_MESSAGE, 'success');

        $this->redirectToAction($group['id']);

    }

}
