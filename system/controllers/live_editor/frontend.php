<?php

class live_editor extends cmsFrontend {

    private $images_controller;

    public function __construct($request) {

        parent::__construct($request);

        $this->images_controller = cmsCore::getController('images');

    }

	public function actionUpload(){

		if (!cmsUser::isLogged()) { cmsCore::error404(); }

		if ($this->request->has('submit')){
			$this->uploadImage();
		}

		return $this->cms_template->renderPlain('upload', array(
			'allowed_extensions' => $this->images_controller->getAllowedExtensions()
		));

	}

	public function uploadImage(){

		$csrf_token = $this->request->get('csrf_token', '');

		if (!cmsForm::validateCSRFToken($csrf_token)){

            return $this->cms_template->renderPlain('upload', array(
                'allowed_extensions' => $this->allowed_extensions,
                'error' => LANG_FORM_ERRORS
            ));

		}

		$result = $this->images_controller->uploadWithPreset('image', 'wysiwyg_live');

        if (!$result['success']){

            return $this->cms_template->renderPlain('upload', array(
                'allowed_extensions' => $this->images_controller->getAllowedExtensions(),
                'error' => $result['error']
            ));

        }

        return $this->cms_template->renderPlain('image', array(
            'url' => $result['image']['url']
        ));

	}

}
