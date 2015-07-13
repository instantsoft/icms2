<?php

class onWallUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserEntries($user['id']);

        return $user;

    }

}
