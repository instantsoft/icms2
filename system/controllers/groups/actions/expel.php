<?php

class actionGroupsExpel extends cmsAction {

    public function run($group_id, $user_id){

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $membership = $this->model->getMembership($group['id'], $user_id);
        if (!$membership || $membership['role'] == groups::ROLE_STAFF) { cmsCore::error404(); }

        $member = cmsCore::getModel('users')->getUser($membership['user_id']);
        if (!$member) { cmsCore::error404(); }

        if ($this->request->has('submit')){

            $csrf_token = $this->request->get('csrf_token', '');
            if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

            $group = cmsEventsManager::hook('group_before_leave', $group);

            $this->model->deleteMembership($group['id'], $member['id']);

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_STAFF_EXPEL_SUCCESS, $member['nickname']));

            $this->redirectToAction($group['slug'], array('members'));

        } else {

            // спрашиваем подтверждение
            return $this->cms_template->render('group_confirm', array(
                'confirm'  => array(
                    'title'  => sprintf(LANG_GROUPS_STAFF_EXPEL_CONFIRM, $member['nickname']),
                    'action' => href_to('groups', 'expel', array($group['id'], $user_id))
                )
            ));

        }

    }

}
