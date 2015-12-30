<?php

class actionComplaintsDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $model = cmsCore::getModel('complaints');

        $model->deleteComplaints($id);

        $this->redirectBack();

    }
}
