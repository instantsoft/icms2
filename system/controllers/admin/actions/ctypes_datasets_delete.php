<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesDatasetsDelete extends cmsAction {

    public function run($dataset_id = null) {

        if (!$dataset_id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model_backend_content->deleteContentDataset($dataset_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectBack();
    }

}
