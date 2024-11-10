<?php
/**
 * @property \modelUsers $model_users
 */
class actionAdminUsersGroupPermsSave extends cmsAction {

    public function run() {

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return $this->redirectBack();
        }

        $new_values = $this->request->get('value', []);
        $group_id   = $this->request->get('group_id', 0);

        if (!$new_values || !$group_id) {
            return cmsCore::error404();
        }

        $group = $this->model_users->getGroup($group_id);
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

        foreach ($owners as $controller_name => $controller) {
            foreach ($controller['subjects'] as $subject) {

                $formatted_values = [];

                foreach ($controller['rules'] as $rule) {

                    $value = $new_values[$rule['id']][$subject['name']] ?? null;

                    $formatted_values[$rule['id']][$group_id] = $value;
                }

                cmsPermissions::savePermissions($subject['name'], $formatted_values);
            }
        }

        cmsUser::addSessionMessage(LANG_CP_PERMISSIONS_SUCCESS, 'success');

        return $this->redirectBack();
    }

}
