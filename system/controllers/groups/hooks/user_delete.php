<?php

class onGroupsUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserGroupsAndMemberships($user['id']);

        return $user;

    }

}
