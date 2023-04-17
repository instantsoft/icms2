<?php

class backendAuth extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useSeoOptions = true;

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
                'title' => LANG_AUTH_SEND_INVITES,
                'url'   => href_to($this->root_url, 'send_invites'),
                'options' => [
                    'icon' => 'mail-bulk'
                ]
            ]
        ];
    }

}
