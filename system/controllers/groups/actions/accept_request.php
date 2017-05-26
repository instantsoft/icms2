<?php

class actionGroupsAcceptRequest extends cmsAction {

    public function run($group_id, $invited_id){

        if (!$this->request->isInternal()){ cmsCore::error404(); }

        $group = $this->model->getGroup($group_id);
        if (!$group) { return false; }

        $group['access'] = $this->getGroupAccess($group);

        if ($group['access']['member_role'] != groups::ROLE_STAFF){
            return false;
        }

        if(!$group['is_closed']){ return false; }

        $send_request = $this->model->getInviteRequest($group['id'], $invited_id);
        if(!$send_request){ return false; }

        $this->model->filterEqual('user_id', $invited_id)->filterIsNull('invited_id');
        $this->model->deleteFiltered('groups_invites');

        $this->model->addMembership($group['id'], $invited_id);

        cmsCore::getController('activity')->addEntry($this->name, 'join', array(
            'subject_title' => $group['title'],
            'subject_id'    => $group['id'],
            'subject_url'   => href_to_rel($this->name, $group['slug']),
            'group_id'      => $group['id'],
            'user_id'       => $invited_id
        ));

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($invited_id);

        $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

        $notice = array(
            'content' => sprintf(LANG_GROUPS_REQUEST_NOTICE_ACCEPT, $group_link),
            'options' => array(
                'is_closeable' => true
            )
        );

        $messenger->sendNoticePM($notice, 'groups_invite');

        return true;

    }

}
