<?php

class actionAdminUsersGroupDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $users_model->deleteGroup($id);

        $this->redirectToAction('users');

    }

}
