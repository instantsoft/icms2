<?php

class actionAdminUsersGroupPerms extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');
        $group = $users_model->getGroup($id);
        if (!$group) { cmsCore::error404(); }

        $controllers = cmsPermissions::getControllersWithRules();

        $owners = array();

        foreach($controllers as $controller_name){

            $controller = cmsCore::getController($controller_name);

            $subjects = $controller->getPermissionsSubjects();
            $rules = cmsPermissions::getRulesList($controller_name);
            $values = array();

            foreach($subjects as $subject){
                $values[ $subject['name'] ] = cmsPermissions::getPermissions($subject['name']);
            }

            $owners[$controller_name] = array(
                'subjects' => $subjects,
                'rules' => $rules,
                'values' => $values
            );

        }

        $template = cmsTemplate::getInstance();

        $template->setMenuItems('users_group', array(
            array(
                'title' => LANG_CONFIG,
                'url' => href_to($this->name, 'users', array('group_edit', $id))
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'url' => href_to($this->name, 'users', array('group_perms', $id))
            )
        ));

        return $template->render('users_group_perms', array(
            'group' => $group,
            'owners' => $owners
        ));

    }

}
