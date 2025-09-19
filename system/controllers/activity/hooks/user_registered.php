<?php

class onActivityUserRegistered extends cmsAction {

    public function run($user) {

        $this->addEntry('users', 'signup', [
            'user_id' => $user['id']
        ]);

        return $user;
    }

}
