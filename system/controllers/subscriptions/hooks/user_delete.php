<?php

class onSubscriptionsUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserSubscriptions($user['id']);

        return $user;

    }

}
