<?php
/**
 * @property \modelBackendContent $model_backend_content
 * @property \modelUsers $model_users
 */
class actionAdminCtypesPerms extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->dispatchEvent('ctype_loaded', [$ctype, 'perms']);

        cmsCore::loadControllerLanguage('content');

        $rules  = cmsPermissions::getRulesList('content');
        $values = cmsPermissions::getPermissions($ctype['name']);

        list($ctype, $rules, $values) = cmsEventsManager::hook('content_perms', [$ctype, $rules, $values]);
        list($ctype, $rules, $values) = cmsEventsManager::hook("content_{$ctype['name']}_perms", [$ctype, $rules, $values]);

        $groups = $this->model_users->getGroups(false);

        return $this->cms_template->render('ctypes_perms', [
            'ctype'  => $ctype,
            'rules'  => $rules,
            'values' => $values,
            'groups' => $groups
        ]);
    }

}
