<?php

class actionAdminCtypesDatasetsDelete extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->deleteContentDataset($dataset_id);

        $this->redirectBack();

    }

}
