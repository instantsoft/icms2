<?php
class redactor extends cmsFrontend {

    public function actionUpload(){

        if (!cmsUser::isLogged()) {
            return $this->cms_template->renderJSON(array(
                'status' => 'error',
                'msg'    => 'auth error'
            ));
        }

		$result = cmsCore::getController('images')->uploadWithPreset('file', 'wysiwyg_redactor');

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

}
