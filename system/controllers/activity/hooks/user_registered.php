<?php

class onActivityUserRegistered extends cmsAction {

    public function run($user){

        $this->addEntry('users', 'signup', array(
            'user_id' => $user['id']
        ));

        return $user;
    }

}
