<?php
/**
 * @property \modelBackendContent $model_backend_content
 * @property \modelUsers $model_users
 */
class actionAdminCtypesPermsSave extends cmsAction {

    public function run($ctype_name = null) {

        $values = $this->request->get('value', []);

        if (!$values || !$ctype_name) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            cmsCore::error404();
        }

        $rules = cmsPermissions::getRulesList('content');

        list($ctype, $rules, $values) = cmsEventsManager::hook('content_perms', [$ctype, $rules, $values]);
        list($ctype, $rules, $values) = cmsEventsManager::hook("content_{$ctype['name']}_perms", [$ctype, $rules, $values]);

        $groups = $this->model_users->getGroups(false);

        // перебираем правила
        foreach ($rules as $rule) {

            // если для этого правила вообще ничего нет,
            // то присваиваем null
            if (empty($values[$rule['id']])) {
                $values[$rule['id']] = null;
                continue;
            }

            // перебираем группы, заменяем на нуллы
            // значения отсутствующих правил
            foreach ($groups as $group) {
                if (empty($values[$rule['id']][$group['id']])) {
                    $values[$rule['id']][$group['id']] = null;
                }
            }
        }

        cmsUser::addSessionMessage(LANG_CP_PERMISSIONS_SUCCESS, 'success');

        cmsPermissions::savePermissions($ctype_name, $values);

        return $this->redirectBack();
    }

}
