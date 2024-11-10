<?php
/**
 * @property \modelUsers $model_users
 */
class actionAdminUsersGroupPerms extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        $group = $this->model_users->getGroup($id);
        if (!$group) {
            return cmsCore::error404();
        }

        $controllers = cmsPermissions::getControllersWithRules();

        $owners = [];

        foreach ($controllers as $controller_name) {

            if (!cmsCore::isControllerExists($controller_name)) {
                continue;
            }

            $controller = cmsCore::getController($controller_name);

            $subjects = $controller->getPermissionsSubjects();
            $rules    = cmsPermissions::getRulesList($controller_name);
            $values   = [];

            foreach ($subjects as $subject) {
                $values[$subject['name']] = cmsPermissions::getPermissions($subject['name']);
            }

            $owners[$controller_name] = [
                'subjects' => $subjects,
                'rules'    => $rules,
                'values'   => $values
            ];
        }

        $owners = cmsEventsManager::hook('users_group_perms', $owners);

        $subjects = [];

        foreach ($owners as $controller_name => $controller) {
            foreach($controller['subjects'] as $subject){
                $subjects[$controller_name.'_'.$subject['name']] = $subject['title'];
            }
        }

        return $this->cms_template->render('users_group_perms', [
            'group'    => $group,
            'menu'     => $this->getUserGroupsMenu('view', $group['id']),
            'subjects' => $subjects,
            'owners'   => $owners
        ]);
    }

}
