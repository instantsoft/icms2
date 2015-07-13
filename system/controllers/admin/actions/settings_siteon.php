<?php

class actionAdminSettingsSiteon extends cmsAction {

    public function run(){

        // если нужно, передаем управление другому экшену
        $config = cmsConfig::getInstance();

        $values = $config->getAll();

        $values['is_site_on'] = 1;

        $result = $config->save($values);

        if (!$result){
            cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');
        }

        $this->redirectBack();

    }

}
