<?php

class backendPhotos extends cmsBackend {

    public $useDefaultOptionsAction = true;

    public $maintained_ctype = 'albums';
    public $useSeoOptions    = true;

    public function getOptionsToolbar() {

        cmsCore::loadControllerLanguage('images');

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title'   => LANG_IMAGES_CONTROLLER,
            'url'     => href_to('admin', 'controllers', ['edit', 'images']),
            'options' => [
                'icon' => 'image'
            ]
        ]);

        return [];
    }

}
