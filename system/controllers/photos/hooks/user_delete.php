<?php

class onPhotosUserDelete extends cmsAction {

    public function run($user){

        $this->model->deleteUserPhotos($user['id']);

        return $user;

    }

}
