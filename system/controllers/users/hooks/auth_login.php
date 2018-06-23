<?php

class onUsersAuthLogin extends cmsAction {

    public function run($user_id){

        if (!$user_id) { return $user_id; }

        $user = $this->model->getUser($user_id);

        if (!$user['is_locked']) { return $user_id; }

        $this->logoutLockedUser($user);

        return $user_id;

    }

}
