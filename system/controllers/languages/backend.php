<?php

class backendLanguages extends cmsBackend {

    public $useDefaultOptionsAction = true;

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
            ]
        ];
    }

}
