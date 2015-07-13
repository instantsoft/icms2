<?php

class onAuthUserProfileUpdate extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();

        if ($user->is_admin) { return true; }

        if (!$profile) { return false; }

        if (!$this->isEmailAllowed($profile['email'])){
            cmsUser::addSessionMessage(sprintf(LANG_AUTH_RESTRICTED_EMAIL, $profile['email']), 'error');
            return false;
        }

        if (!$this->isNameAllowed($profile['nickname'])){
            cmsUser::addSessionMessage(sprintf(LANG_AUTH_RESTRICTED_NAME, $profile['nickname']), 'error');
            return false;
        }

        return true;

    }

}
