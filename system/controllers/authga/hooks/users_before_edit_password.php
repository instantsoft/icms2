<?php

class onAuthgaUsersBeforeEditPassword extends cmsAction {

    public function run($_data){

        list($profile, $data, $form) = $_data;

        if(array_key_exists('2fa', $data) && $data['2fa'] !== $this->name && $profile['2fa'] === $this->name){
            $profile['ga_secret'] = null;
        }

        return [$profile, $data, $form];

    }

}
