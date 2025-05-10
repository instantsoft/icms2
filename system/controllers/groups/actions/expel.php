<?php

class actionGroupsExpel extends cmsAction {

    public function run($group_id, $user_id) {

        $group = $this->model->getGroup($group_id);
        if (!$group) {
            return cmsCore::error404();
        }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $membership = $this->model->getMembership($group['id'], $user_id);
        if (!$membership || $membership['role'] == groups::ROLE_STAFF) {
            return cmsCore::error404();
        }

        $member = cmsCore::getModel('users')->getUser($membership['user_id']);
        if (!$member) {
            return cmsCore::error404();
        }

        if ($this->request->has('submit')) {

            $csrf_token = $this->request->get('csrf_token', '');
            if (!cmsForm::validateCSRFToken($csrf_token)) {
                return cmsCore::error404();
            }

            $group = cmsEventsManager::hook('group_before_leave', $group);

            $this->model->deleteMembership($group['id'], $member['id']);

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_STAFF_EXPEL_SUCCESS, $member['nickname']));

            return $this->redirectToAction($group['slug'], ['members']);

        } else {

            // спрашиваем подтверждение
            return $this->cms_template->render('group_confirm', [
                'confirm' => [
                    'title'  => sprintf(LANG_GROUPS_STAFF_EXPEL_CONFIRM, $member['nickname']),
                    'action' => href_to('groups', 'expel', [$group['id'], $user_id])
                ]
            ]);
        }
    }

}
