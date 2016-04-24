<?php

class actionGroupsGroupEditStaffDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        // проверяем наличие доступа
        if ($group['owner_id'] != $this->cms_user->id && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $staff_id = $this->request->get('staff_id', 0);
        if (!$staff_id) { cmsCore::error404(); }

        $membership = $this->model->getMembership($group['id'], $staff_id);

        if (!$membership || $membership['role'] != groups::ROLE_STAFF){
            return $this->cms_template->renderJSON(array(
                'error' => true,
            ));
        }

        $this->model->updateMembershipRole($group['id'], $staff_id, groups::ROLE_MEMBER);

        return $this->cms_template->renderJSON(array(
            'error' => false,
        ));

    }

}
