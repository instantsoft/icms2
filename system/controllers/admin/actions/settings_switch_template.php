<?php

class actionAdminSettingsSwitchTemplate extends cmsAction {

    public function run($name) {

        $tpls = cmsCore::getTemplates();

        if (!$name || !in_array($name, $tpls)) {

            return cmsCore::error404();
        }

        $result = cmsConfig::getInstance()->update('template_admin', $name);

        if (!$result) {
            cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');
        }

        return $this->redirectToAction('');
    }

}
