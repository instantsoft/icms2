<?php
class redactor extends cmsFrontend {

    public function actionUpload(){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array(
                'status' => 'error',
                'msg'    => 'auth error'
            ));
        }

        $file_context = array(
            'target_controller' => $this->request->get('target_controller', ''),
            'target_subject' => $this->request->get('target_subject', ''),
            'target_id' => $this->request->get('target_id', '')
        );

		$result = cmsCore::getController('images')->
                registerUploadFile($file_context)->
                uploadWithPreset('file', 'wysiwyg_redactor');

        if (!$result['success']){

            return $this->cms_template->renderJSON(array(
                'status' => 'error',
                'msg'    => $result['error']
            ));

        }

        return $this->cms_template->renderJSON(array(
            'status'   => 'success',
            'filelink' => $result['image']['url']
        ));

    }

    public function actionImagesList() {

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array());
        }

        $target_controller = $this->request->get('target_controller', '');
        $target_subject    = $this->request->get('target_subject', '');
        $target_id         = $this->request->get('target_id', '');

        $files_model = cmsCore::getModel('files');

        if(!$target_controller){
            return $this->cms_template->renderJSON(array());
        }
        $files_model->filterEqual('target_controller', $target_controller);

        if(!$target_subject){
            return $this->cms_template->renderJSON(array());
        }
        $files_model->filterEqual('target_subject', $target_subject);

        if($target_id){
            $files_model->filterEqual('target_id', $target_id);
        }

        $files_model->filterEqual('user_id', $this->cms_user->id);

        $files_model->limit(100);

        $files = $files_model->filterFileType('image')->getFiles();

        if(!$files){
            return $this->cms_template->renderJSON(array());
        }

        return $this->cms_template->renderJSON($files);

    }

    public function actionLinksList() {

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array());
        }

        $target_controller = $this->request->get('target_controller', '');
        $target_subject    = $this->request->get('target_subject', '');
        $target_id         = $this->request->get('target_id', '');

        if(!cmsCore::isControllerExists($target_controller)){
            return $this->cms_template->renderJSON(array());
        }

        $controller = cmsCore::getController($target_controller, $this->request);

        if(!$controller->isEnabled()){
            return $this->cms_template->renderJSON(array());
        }

        $data = $controller->runHook('wysiwyg_links_list', array($target_subject, $target_id));

        if(!$data || $data === $this->request->getData()){
            return $this->cms_template->renderJSON(array());
        }

        return $this->cms_template->renderJSON($data);
    }

}
