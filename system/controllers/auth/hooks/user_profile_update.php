<?php

class onAuthUserProfileUpdate extends cmsAction {

    public function run($profile){

        if (!$profile) { return array('csrf_token'=>''); }

        $success = array();

        if ($this->cms_user->is_admin) { return $success; }

        if (!$this->isEmailAllowed($profile['email'])){
            $success['email'] = sprintf(LANG_AUTH_RESTRICTED_EMAIL, $profile['email']);
        }

        if (!$this->isNameAllowed($profile['nickname'])){
            $success['nickname'] = sprintf(LANG_AUTH_RESTRICTED_NAME, $profile['nickname']);
        }

        return $success;

    }

}
