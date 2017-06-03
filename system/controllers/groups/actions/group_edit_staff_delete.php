<?php

class actionGroupsGroupEditStaffDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        // проверяем наличие доступа
        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $staff_id = $this->request->get('staff_id', 0);
        if (!$staff_id) { cmsCore::error404(); }

        $membership = $this->model->getMembership($group['id'], $staff_id);

        if (!$membership || $membership['role'] != groups::ROLE_STAFF){
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
        }

        $this->model->updateMembershipRole($group['id'], $staff_id, groups::ROLE_MEMBER);

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($staff_id);

        $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

        $notice = array(
            'content' => sprintf(LANG_GROUPS_STAFF_REMOVE_NOTICE, $group_link),
            'options' => array(
                'is_closeable' => true
            )
        );

        $messenger->sendNoticePM($notice, 'groups_invite');

        return $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
