<?php

class onAdminUserLogin extends cmsAction {

    public function run($user){

        if (!$user['is_admin']) { return $user; }
        if (!cmsConfig::get('is_check_updates')) { return $user; }

        $updater = new cmsUpdater();
        $update = $updater->checkUpdate();

        if (!empty($update['version'])) {
            $message = sprintf(LANG_CP_UPDATE_AVAILABLE, $update['version']);
            $message .= '&mdash; <a href="'.href_to('admin', 'update').'">'.LANG_INSTALL.'</a>';
            cmsUser::addSessionMessage($message);
        }

        return $user;

    }

}
