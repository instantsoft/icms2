<?php

class backendGeo extends cmsBackend {

    protected $useOptions = true;
    public $useDefaultOptionsAction = true;

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_GEO_CONTROLLER,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'globe'
                ]
            ],
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
