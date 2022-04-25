<?php

class onUsersUserPreloaded extends cmsAction {

    public function run($user) {

        if (!$user['is_locked']) {
            return $user;
        }

        return $this->logoutLockedUser($user);
    }

}
