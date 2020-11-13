<?php

class onAuthgaAuthTwofactorList extends cmsAction {

    public function run(){
        return array(
            'name'  => $this->name,
            'types' => array(
                $this->name => 'Google Authenticator'
            )
        );
    }

}
