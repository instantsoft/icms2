<?php

class onCommentsUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserComments($user['id']);

        return $user;

    }

}
