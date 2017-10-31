<?php

class backendMessages extends cmsBackend {

    public $useDefaultOptionsAction = true;

    public function actionIndex(){
        $this->redirectToAction('options');
    }

    public function getBackendMenu() {
        return array(
            array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ),
            array(
                'title' => LANG_PM_PMAILING,
                'url'   => href_to($this->root_url, 'pmailing')
            ),
        );
    }

}
