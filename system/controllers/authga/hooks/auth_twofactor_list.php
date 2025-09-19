<?php

class onAuthgaAuthTwofactorList extends cmsAction {

    public function run() {
        return [
            'name'  => $this->name,
            'types' => [
                $this->name => 'Google Authenticator'
            ]
        ];
    }

}
