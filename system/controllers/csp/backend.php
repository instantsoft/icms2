<?php

class backendCsp extends cmsBackend {

    protected $useOptions = true;
    public $useDefaultOptionsAction = true;

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
                'title' => LANG_CSP_REPORTS,
                'url'   => href_to($this->root_url, 'reports'),
                'options' => [
                    'icon' => 'list'
                ]
            ]
        ];
    }

}
