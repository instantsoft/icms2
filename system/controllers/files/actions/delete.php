<?php

class actionFilesDelete extends cmsAction {

    public function run(){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array('error' => false));
        }

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
