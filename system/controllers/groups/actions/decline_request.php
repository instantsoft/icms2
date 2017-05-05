<?php

class actionGroupsDeclineRequest extends cmsAction {

    public function run($group_id, $invited_id){

        if (!$this->request->isInternal()){ cmsCore::error404(); }

        $group = $this->model->getGroup($group_id);
        if (!$group) { return false; }

        $group['access'] = $this->getGroupAccess($group);

        if ($group['access']['member_role'] != groups::ROLE_STAFF){
            return false;
        }

        if(!$group['is_closed']){
            return false;
        }

        $send_request = $this->model->getInviteRequest($group['id'], $invited_id);
        if(!$send_request){ return false; }

        $messenger = cmsCore::getController('messages');

        $messenger->clearRecipients()->addRecipient($invited_id);

        //
        // Личное сообщение
        //
        $admin_link = '<a href="'.href_to('users', $this->cms_user->id).'">'.$this->cms_user->nickname.'</a>';
        $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

        $notice = array(
            'content' => sprintf(LANG_GROUPS_REQUEST_NOTICE_DECLINE, $group_link, $admin_link),
            'options' => array(
                'is_closeable' => true
            )
        );

        $messenger->sendNoticePM($notice, 'groups_invite');

        //
        // E-mail
        //
        $messenger->sendNoticeEmail('groups_request_decline', array(
            'user_nickname' => $this->cms_user->nickname,
            'user_url'      => href_to_abs('users', $this->cms_user->id),
            'group_title'   => $group['title'],
            'group_url'     => href_to_abs('groups', $group['id'])
        ), 'groups_invite');

        $this->model->filterEqual('user_id', $invited_id)->filterIsNull('invited_id');
        $this->model->deleteFiltered('groups_invites');

        return true;

    }

}
