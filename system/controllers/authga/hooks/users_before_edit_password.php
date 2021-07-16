<?php

class onAuthgaUsersBeforeEditPassword extends cmsAction {

    public function run($_data) {

        list($profile, $data, $form) = $_data;

        // 2fa выключен в админке
        if(!array_key_exists('2fa', $data)){
            return $_data;
        }

        // Если 2fa не был задан и не включали
        if(!$profile['2fa'] && !$data['2fa']){
            $data['ga_secret'] = null;
        }

        // Если другой 2fa, а был задан Google
        if ($data['2fa'] !== $this->name && $profile['2fa'] === $this->name) {
            $data['ga_secret'] = null;
        }

        return [$profile, $data, $form];
    }

}
