<?php

class actionGroupsGroupEditStaff extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        // проверяем наличие доступа
        if ($group['owner_id'] != $user->id && !$user->is_admin) { cmsCore::error404(); }

        $members = $this->model->getMembers($group['id']);
        $staff = $this->model->getMembers($group['id'], groups::ROLE_STAFF);

        if ($this->request->isAjax()){
            return $this->submit($group, $members, $staff);
        }

        return $template->render('group_edit_staff', array(
            'id' => $group['id'],
            'group' => $group,
            'members' => $members,
            'staff' => $staff,
            'user' => $user,
        ));

    }

    public function submit($group, $members, $staff){

        $template = cmsTemplate::getInstance();

        $name = $this->request->get('name');
        $name = mb_strtolower(trim($name));

        $member = false;

        foreach($members as $user){
            if (mb_strtolower($user['nickname']) == $name && !isset($staff[$user['id']])){
                $member = $user;
                break;
            }
        }

        if ($member===false){
            return $template->renderJSON(array(
                'error' => true,
                'message' => sprintf(LANG_GROUPS_STAFF_NOT_MEMBER, $name)
            ));
        }

        $this->model->updateMembershipRole($group['id'], $member['id'], groups::ROLE_STAFF);

        return $template->renderJSON(array(
            'error' => false,
            'name' => $name,
            'html' => $template->render('group_edit_staff_item', array(
                'member' => $member,
                'group' => $group
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL)),
            'id' => $member['id'],
        ));

    }

}
