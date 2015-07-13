<?php

class onRatingUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserVotes($user['id']);

        return $user;

    }

}
