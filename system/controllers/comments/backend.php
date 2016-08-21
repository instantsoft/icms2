<?php

class backendComments extends cmsBackend{

    public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;
    public $useSeoOptions = true;

    public function actionIndex(){
        $this->redirectToAction('comments_list');
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_COMMENTS_LIST,
                'url' => href_to($this->root_url, 'comments_list')
            ),
            array(
                'title' => LANG_OPTIONS,
                'url' => href_to($this->root_url, 'options')
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'url' => href_to($this->root_url, 'perms', 'comments')
            )
        );
    }

}