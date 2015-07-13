<?php

class onContentUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserContent($user['id']);

        return $user;

    }

}
