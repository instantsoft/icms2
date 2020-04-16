<?php

class actionGroupsGroupEditRoleDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        // проверяем наличие доступа
        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $role_id = $this->request->get('role_id', 0);
        if (!$role_id) { cmsCore::error404(); }

        $this->model->deleteRole($group, $role_id);

        return $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
