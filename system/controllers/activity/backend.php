<?php

class backendActivity extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;

    public function loadCallback() {

        $this->callbacks = array(
            'actionoptions'=>array(
                function($controller, $options){
                    $controller->model->enableTypes($options['types']);
                }
            )
        );

    }

    public function actionIndex(){
        $this->redirectToAction('options');
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_OPTIONS,
                'url' => href_to($this->root_url, 'options')
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'url' => href_to($this->root_url, 'perms', 'activity')
            ),
        );
    }

}
