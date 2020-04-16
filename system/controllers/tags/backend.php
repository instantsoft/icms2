<?php

class backendTags extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useSeoOptions           = true;

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_TAGS_CONTROLLER,
                'url'   => href_to($this->root_url)
            ),
            array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            )
        );
    }

}
