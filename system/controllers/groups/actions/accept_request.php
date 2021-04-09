<?php

class actionGroupsAcceptRequest extends cmsAction {

    public function run($group_id, $invited_id) {

        $group = $this->model->getGroup($group_id);
        if (!$group) {
            return $this->successRequest(false);
        }

        $group['access'] = $this->getGroupAccess($group);

        if ($group['access']['member_role'] != groups::ROLE_STAFF && !$this->cms_user->is_admin) {
            return $this->successRequest(false);
        }

        $send_request = $this->model->getInviteRequest($group['id'], $invited_id);
        if (!$send_request) {
            return $this->successRequest(false);
        }

        $this->model->filterEqual('user_id', $invited_id)->filterIsNull('invited_id');
        $this->model->deleteFiltered('groups_invites');

        $this->model->addMembership($group['id'], $invited_id);

        // роли по умолчанию
        if (!empty($group['join_roles'])) {
            $this->model->setUserRoles($group['id'], $group['join_roles'], $invited_id);
        }

        list($group, $invited_id) = cmsEventsManager::hook('groups_after_accept_request', [$group, $invited_id]);

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($invited_id);

        $group_link = '<a href="' . href_to('groups', $group['id']) . '">' . $group['title'] . '</a>';

        $notice = [
            'content' => sprintf(LANG_GROUPS_REQUEST_NOTICE_ACCEPT, $group_link),
            'options' => [
                'is_closeable' => true
            ]
        ];

        $messenger->sendNoticePM($notice, 'groups_invite');

        return $this->successRequest(true);
    }

    private function successRequest($success) {

        if ($this->request->isInternal()) {
            return $success;
        }

        if ($success) {
            return $this->cms_template->renderJSON([
                'error' => false
            ]);
        } else {
            cmsCore::error404();
        }
    }

}
