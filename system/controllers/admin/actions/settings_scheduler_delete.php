<?php

class actionAdminSettingsSchedulerDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $this->model->deleteSchedulerTask($id);

        $this->redirectToAction('settings', array('scheduler'));

    }

}
