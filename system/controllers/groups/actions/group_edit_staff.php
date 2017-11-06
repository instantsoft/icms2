<?php

class actionGroupsGroupEditStaff extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

         // проверяем наличие доступа
        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $members = $this->model->getMembers($group['id']);
        $staff = $this->model->getMembers($group['id'], groups::ROLE_STAFF);

        if ($this->request->isAjax()){
            return $this->submit($group, $members, $staff);
        }

        $this->cms_template->setPageTitle(LANG_GROUPS_EDIT_STAFF);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_EDIT, href_to('groups', $group['slug'], 'edit'));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_EDIT_STAFF);

        return $this->cms_template->render('group_edit_staff', array(
            'id'      => $group['id'],
            'group'   => $group,
            'members' => $members,
            'staff'   => $staff,
            'user'    => $this->cms_user
        ));

    }

    public function submit($group, $members, $staff){

        $email = mb_strtolower(trim($this->request->get('name', '')));
        if ($this->validate_email($email) !== true){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => ERR_VALIDATE_EMAIL
            ));
        }

        $member = false;

        foreach($members as $user){
            if (mb_strtolower($user['email']) == $email && !isset($staff[$user['id']])){
                $member = $user;
                break;
            }
        }

        if ($member === false){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => sprintf(LANG_GROUPS_STAFF_NOT_MEMBER, $email)
            ));
        }

        $this->model->updateMembershipRole($group['id'], $member['id'], groups::ROLE_STAFF);

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

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'name'  => $member['nickname'],
            'html'  => $this->cms_template->render('group_edit_staff_item', array(
                    'member' => $member,
                    'group'  => $group
                ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL)),
            'id'    => $member['id']
        ));

    }

}
