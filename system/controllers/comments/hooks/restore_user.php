<?php

class onCommentsRestoreUser extends cmsAction {

    public function run($user){

        $this->model->restoreUserComments($user['id']);

        return $user;
    }

}
