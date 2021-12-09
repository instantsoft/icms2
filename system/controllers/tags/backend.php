<?php

class backendTags extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useSeoOptions = true;

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_TAGS_CONTROLLER,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'tags'
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
