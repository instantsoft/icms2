<?php

class onUsersUserLoaded extends cmsAction {

    public function run($user){

        if (!$user['is_locked']) { return $user; }
        
        $this->logoutLockedUser($user);
        
        return $user;
        
    }

}
