<?php

class actionGroupsGroupEditStaffDelete extends cmsAction {

    public function run($group){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        // проверяем наличие доступа
        if ($group['owner_id'] != $user->id && !$user->is_admin) { cmsCore::error404(); }

        $staff_id = $this->request->get('staff_id');

        if (!$staff_id) { cmsCore::error404(); }

        $membership = $this->model->getMembership($group['id'], $staff_id);

        if (!$membership || $membership['role'] != groups::ROLE_STAFF){
            return $template->renderJSON(array(
                'error' => true,
            ));
        }

        $this->model->updateMembershipRole($group['id'], $staff_id, groups::ROLE_MEMBER);

        return $template->renderJSON(array(
            'error' => false,
        ));

    }

}
