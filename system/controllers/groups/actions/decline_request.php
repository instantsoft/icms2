<?php

class actionGroupsDeclineRequest extends cmsAction {

    public function run($group_id, $invited_id){

        $group = $this->model->getGroup($group_id);
        if (!$group) { return $this->successRequest(false); }

        $group['access'] = $this->getGroupAccess($group);

        if ($group['access']['member_role'] != groups::ROLE_STAFF && !$this->cms_user->is_admin){
            return $this->successRequest(false);
        }

        $send_request = $this->model->getInviteRequest($group['id'], $invited_id);
        if(!$send_request){ return $this->successRequest(false); }

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($invited_id);

        $admin_link = '<a href="'.href_to_profile($this->cms_user).'">'.$this->cms_user->nickname.'</a>';
        $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

        $notice = array(
            'content' => sprintf(LANG_GROUPS_REQUEST_NOTICE_DECLINE, $group_link, $admin_link),
            'options' => array(
                'is_closeable' => true
            )
        );

        $messenger->sendNoticePM($notice, 'groups_invite');

        $messenger->sendNoticeEmail('groups_request_decline', array(
            'user_nickname' => $this->cms_user->nickname,
            'user_url'      => href_to_profile($this->cms_user, false, true),
            'group_title'   => $group['title'],
            'group_url'     => href_to_abs('groups', $group['id'])
        ), 'groups_invite');

        $this->model->filterEqual('user_id', $invited_id)->filterIsNull('invited_id');
        $this->model->deleteFiltered('groups_invites');

        return $this->successRequest(true);

    }

    private function successRequest($success) {

        if ($this->request->isInternal()){ return $success; }

        if($success){

            return $this->cms_template->renderJSON(array(
                'error' => false
            ));

        } else {

            cmsCore::error404();

        }

    }

}
