<?php

class actionAdminUsersGroupPermsSave extends cmsAction {

    public function run() {

        $new_values = $this->request->get('value', array());
        $group_id   = $this->request->get('group_id', 0);

        if (!$new_values || !$group_id) {
            cmsCore::error404();
        }

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

        foreach ($owners as $controller_name => $controller) {
            foreach ($controller['subjects'] as $subject) {

                $formatted_values = array();

                foreach ($controller['rules'] as $rule) {

                    $value = isset($new_values[$rule['id']][$subject['name']]) ?
                            $new_values[$rule['id']][$subject['name']] :
                            null;

                    $formatted_values[$rule['id']][$group_id] = $value;
                }

                cmsPermissions::savePermissions($subject['name'], $formatted_values);
            }
        }

        cmsUser::addSessionMessage(LANG_CP_PERMISSIONS_SUCCESS, 'success');

        $this->redirectBack();
    }

}
