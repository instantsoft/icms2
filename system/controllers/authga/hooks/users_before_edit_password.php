<?php

class onAuthgaUsersBeforeEditPassword extends cmsAction {

    public function run($data){

        list($profile, $data, $form) = $data;

        if($data['2fa'] !== $this->name && $profile['2fa'] === $this->name){
            $profile['ga_secret'] = null;
        }

        return [$profile, $data, $form];

    }

}
