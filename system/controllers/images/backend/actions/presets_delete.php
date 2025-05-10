<?php

class actionImagesPresetsDelete extends cmsAction {

    public function run($id) {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $preset = $this->model->getPreset($id);
        if (!$preset) {
            return cmsCore::error404();
        }

        $this->model->deletePreset($preset['id']);

        $this->deleteDefaultImages($preset);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('presets');
    }

}
