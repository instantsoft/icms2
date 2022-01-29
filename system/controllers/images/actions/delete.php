<?php

class actionImagesDelete extends cmsAction {

    public function run() {

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => 'Please reload page'
            ]);
        }

        $paths = $this->request->get('paths', []);
        if (!$paths) {
            return $this->cms_template->renderJSON([
                'error' => false
            ]);
        }

        foreach ($paths as $path) {
            if (is_array($path) || !$path) {
                continue;
            }
            $file = $this->model_files->getFileByPath($path);
            if (!$file) {
                continue;
            }
            if ($this->cms_user->id != $file['user_id'] && !$this->cms_user->is_admin) {
                continue;
            }
            $this->model_files->deleteFile($file['id']);
        }

        return $this->cms_template->renderJSON([
            'error' => false
        ]);
    }

}
