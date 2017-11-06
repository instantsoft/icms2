<?php

class actionAdminCtypesDatasetsDelete extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        cmsCore::getModel('content')->deleteContentDataset($dataset_id);

        $this->redirectBack();

    }

}
