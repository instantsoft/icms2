<?php

class actionGroupsSetStaff extends cmsAction {

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

            $this->model->updateMembershipRole($group['id'], $member['id'], groups::ROLE_STAFF);

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_STAFF_SET_SUCCESS, $member['nickname']));

            $messenger = cmsCore::getController('messages');

            $messenger->addRecipient($member['id']);

            $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

            $notice = array(
                'content' => sprintf(LANG_GROUPS_STAFF_SUCCESS_NOTICE, $group_link),
                'options' => array(
                    'is_closeable' => true
                )
            );

            $messenger->sendNoticePM($notice, 'groups_invite');

            $this->redirectToAction($group['slug'], array('members', '-1'));

        } else {

            // спрашиваем подтверждение
            return $this->cms_template->render('group_confirm', array(
                'confirm'  => array(
                    'title'  => sprintf(LANG_GROUPS_STAFF_SET_CONFIRM, $member['nickname']),
                    'action' => href_to('groups', 'set_staff', array($group['id'], $user_id))
                )
            ));

        }

    }

}
