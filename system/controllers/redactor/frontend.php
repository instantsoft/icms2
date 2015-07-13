<?php
class redactor extends cmsFrontend {

    public function actionUpload(){

		$images_controller = cmsCore::getController('images');
		
		$result = $images_controller->uploadWithPreset('file', 'wysiwyg_redactor');
				
        if (!$result['success']){

            cmsTemplate::getInstance()->renderJSON(array(
                'status' => 'error',
                'msg' => $result['error']
            ));

            $this->halt();

        }

        cmsTemplate::getInstance()->renderJSON(array(
            'status' => 'success',
            'filelink' => $result['image']['url']
        ));

        $this->halt();

    }

}
