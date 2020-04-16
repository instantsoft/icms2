<?php

class actionAdminCtypesDatasetsDelete extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $this->model_content->deleteContentDataset($dataset_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();

    }

}
