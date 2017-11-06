<?php

class backendPhotos extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $maintained_ctype = 'albums';
    public $useSeoOptions = true;

    public function actionIndex(){
        $this->redirectToAction('options');
    }

    public function getOptionsToolbar(){
        cmsCore::loadControllerLanguage('images');
        return array(
            array(
                'class'  => 'settings',
                'title'  => LANG_IMAGES_CONTROLLER,
                'href'   => href_to('admin', 'controllers', array('edit', 'images'))
            )
        );
    }

}
