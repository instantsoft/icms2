<?php

class actionGroupsGroupEditRoleDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        // проверяем наличие доступа
        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $role_id = $this->request->get('role_id', 0);
        if (!$role_id) {
            return cmsCore::error404();
        }

        $this->model->deleteRole($group, $role_id);

        return $this->cms_template->renderJSON([
            'error' => false
        ]);
    }

}
