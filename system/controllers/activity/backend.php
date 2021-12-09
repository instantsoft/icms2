<?php

class backendActivity extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;

    public function loadCallback() {

        $this->callbacks = [
            'actionoptions' => [
                function ($controller, $options) {
                    $controller->model->enableTypes($options['types']);
                }
            ]
        ];

    }

    public function actionIndex() {
        $this->redirectToAction('options');
    }

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options'),
                'options' => [
                    'icon' => 'cog'
                ]
            ],
            [
                'title' => LANG_PERMISSIONS,
                'url'   => href_to($this->root_url, 'perms', 'activity'),
                'options' => [
                    'icon' => 'key'
                ]
            ]
        ];
    }

}
