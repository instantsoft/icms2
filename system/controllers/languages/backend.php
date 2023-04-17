<?php

class backendLanguages extends cmsBackend {

    public $useDefaultOptionsAction = true;

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'cog'
                ]
            ]
        ];
    }

}
