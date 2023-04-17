<?php

class backendActivity extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        $this->addEventListener('controller_save_options', function ($controller, $options) {

            $controller->model->enableTypes($options['types']);
        });
    }

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'cog'
                ]
            ],
            [
                'title' => LANG_ACTIVITY_TYPES,
                'url'   => href_to($this->root_url, 'types'),
                'options' => [
                    'icon' => 'list-alt'
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
