<?php
class files extends cmsFrontend {

    public function actionDownload($id, $url_key){

        if (!$id || !$url_key) { return cmsCore::error404(); }

        $file = $this->model->getFile($id);

        if (files_user_file_hash($file['path']) != $url_key) { cmsCore::errorForbidden(); }

        $filename = cmsConfig::get('upload_path') . $file['path'];
        $original_filename = $file['name'];

        $this->model->incrementDownloadsCounter($file['id']);

        header("Content-Type: application/force-download");
        header("Content-Length: " . filesize($filename));
        header('Content-Disposition: attachment; filename="' . $original_filename . '"');

        if(ob_get_length()) { ob_end_clean(); }

        readfile($filename);

        $this->halt();

    }

    public function actionDelete(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))){
            cmsCore::error404();
        }

        $path = $this->request->get('path', '');

        if (!$path) {
            return $this->cms_template->renderJSON(array(
                'error' => false
            ));
        }

        if(strpos($path, $this->cms_config->upload_root) === 0){
            $path = str_replace($this->cms_config->upload_root, '', $path);
        }

        $file = $this->model->getFileByPath($path);
        if (!$file) {
            return $this->cms_template->renderJSON(array(
                'error' => false
            ));
        }

        if($this->cms_user->id != $file['user_id'] && !$this->cms_user->is_admin){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => LANG_FILES_NOT_DELETE_NO_OWNER
            ));
        }

        $is_deleted = $this->model->deleteFile($file['id']);

        if(!$is_deleted){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => LANG_FILES_NOT_DELETE_NO_PERM
            ));
        }

        return $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
