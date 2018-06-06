<?php

class actionGroupsGroupEditRoles extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

         // проверяем наличие доступа
        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) { cmsCore::error404(); }

        if ($this->request->isAjax()){
            return $this->submit($group);
        }

        $this->cms_template->setPageTitle(LANG_GROUPS_EDIT_ROLES);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_EDIT, href_to('groups', $group['slug'], 'edit'));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_EDIT_ROLES);

        return $this->cms_template->render('group_edit_roles', array(
            'group'   => $group,
            'user'    => $this->cms_user
        ));

    }

    public function submit($group){

        $role = trim(strip_tags($this->request->get('role', '')));
        if (!$role){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => ERR_VALIDATE_REQUIRED
            ));
        }

        $role_id = $this->request->get('role_id', 0);

        if ($role_id) {

            $this->model->editRole($group, $role, $role_id);

            return $this->cms_template->renderJSON(array(
                'error' => false,
                'role'  => $role
            ));

        }

        $role_id = $this->model->addRole($group, $role);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'html'  => $this->cms_template->render('group_edit_role', array(
                'roles' => array($role_id => $role)
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL))
        ));

    }

}
