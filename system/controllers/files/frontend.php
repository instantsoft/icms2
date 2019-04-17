<?php
class files extends cmsFrontend {

    public $request_params = array(
        'target_controller' => array(
            'default' => '',
            'rules'   => array(
                array('sysname'),
                array('max_length', 32)
            )
        ),
        'target_subject' => array(
            'default' => '',
            'rules'   => array(
                array('sysname'),
                array('max_length', 32)
            )
        ),
        'target_id' => array(
            'default' => 0,
            'rules'   => array(
                array('digits')
            )
        )
    );

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

    public function actionFilesList($type) {

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array());
        }

        $target_controller = $this->request->get('target_controller');
        $target_subject    = $this->request->get('target_subject');
        $target_id         = $this->request->get('target_id');

        if(!$target_controller){
            return $this->cms_template->renderJSON(array());
        }
        $this->model->filterEqual('target_controller', $target_controller);

        if(!$target_subject){
            return $this->cms_template->renderJSON(array());
        }
        $this->model->filterEqual('target_subject', $target_subject);

        if($target_id){
            $this->model->filterEqual('target_id', $target_id);
        }

        $this->model->filterEqual('user_id', $this->cms_user->id);

        $this->model->limit(100);

        $files = $this->model->filterFileType($type)->getFiles();

        if(!$files){
            return $this->cms_template->renderJSON(array());
        }

        return $this->cms_template->renderJSON($files);

    }

}
