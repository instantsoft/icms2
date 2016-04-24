<?php

class actionAdminSettingsSiteon extends cmsAction {

    public function run(){

        $result = cmsConfig::getInstance()->update('is_site_on', 1);

        if (!$result){
            cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');
        }

        $this->redirectBack();

    }

}
