<?php

class actionAdminCtypesPerms extends cmsAction {

    public function run($ctype_id = null){

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        cmsCore::loadControllerLanguage('content');

        $rules  = cmsPermissions::getRulesList('content');
        $values = cmsPermissions::getPermissions($ctype['name']);

		list($ctype, $rules, $values) = cmsEventsManager::hook('content_perms', array($ctype, $rules, $values));
		list($ctype, $rules, $values) = cmsEventsManager::hook("content_{$ctype['name']}_perms", array($ctype, $rules, $values));

        $groups = $this->model_users->getGroups(false);

        return $this->cms_template->render('ctypes_perms', array(
            'ctype'  => $ctype,
            'rules'  => $rules,
            'values' => $values,
            'groups' => $groups
        ));

    }

}
