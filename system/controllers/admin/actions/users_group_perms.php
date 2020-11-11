<?php

class actionAdminUsersGroupPerms extends cmsAction {

    public function run($id = false) {

        if (!$id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $group = $users_model->getGroup($id);
        if (!$group) { cmsCore::error404(); }

        $controllers = cmsPermissions::getControllersWithRules();

        $owners = array();

        foreach ($controllers as $controller_name) {

            if (!cmsCore::isControllerExists($controller_name)) {
                continue;
            }

            $controller = cmsCore::getController($controller_name);

            $subjects = $controller->getPermissionsSubjects();
            $rules    = cmsPermissions::getRulesList($controller_name);
            $values   = array();

            foreach ($subjects as $subject) {
                $values[$subject['name']] = cmsPermissions::getPermissions($subject['name']);
            }

            $owners[$controller_name] = array(
                'subjects' => $subjects,
                'rules'    => $rules,
                'values'   => $values
            );
        }

        $owners = cmsEventsManager::hook('users_group_perms', $owners);

        return $this->cms_template->render('users_group_perms', array(
            'group'  => $group,
            'menu'   => $this->getUserGroupsMenu('view', $group['id']),
            'owners' => $owners
        ));
    }

}
