<?php

class live_editor extends cmsFrontend {
	
	public function actionUpload(){
		
		if (!cmsUser::isLogged()) { cmsCore::error404(); }
		
		if ($this->request->has('submit')){
			$this->uploadImage();
		}
		
		$template = cmsTemplate::getInstance();
		$images_controller = cmsCore::getController('images');
		
		$html = $template->render('upload', array(
			'allowed_extensions' => $images_controller->getAllowedExtensions()
		));
		
		echo $html; $this->halt();
		
	}
	
	public function uploadImage(){
		
		$template = cmsTemplate::getInstance();

		$csrf_token = $this->request->get('csrf_token');
		
		if (!cmsForm::validateCSRFToken($csrf_token)){

            $html = $template->render('upload', array(
                'allowed_extensions' => $this->allowed_extensions,				
                'error' => LANG_FORM_ERRORS
            ));

            echo $html; $this->halt();
			
		}

		$images_controller = cmsCore::getController('images');
		
		$result = $images_controller->uploadWithPreset('image', 'wysiwyg_live');
		
        if (!$result['success']){

            $html = $template->render('upload', array(
                'allowed_extensions' => $images_controller->getAllowedExtensions(),				
                'error' => $result['error']
            ));

            echo $html; $this->halt();

        }

        $html = $template->render('image', array(
            'url' => $result['image']['url']
        ));

        echo $html; $this->halt();
		
	}
	
}
