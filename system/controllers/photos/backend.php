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

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_IMAGES_CONTROLLER,
            'url'   => href_to('admin', 'controllers', array('edit', 'images')),
            'options' => array(
                'icon'  => 'icon-settings'
            )
        ]);

        return [];
    }

}
