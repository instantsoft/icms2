<?php

class backendImages extends cmsBackend{
	
	public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;

    public function actionIndex(){
        $this->redirectToAction('presets');
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_IMAGES_PRESETS,
                'url' => href_to($this->root_url, 'presets')
            ),
            array(
                'title' => LANG_OPTIONS,
                'url' => href_to($this->root_url, 'options')
            ),
        );
    }

}