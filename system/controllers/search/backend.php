<?php

class backendSearch extends cmsBackend {

    public $useDefaultOptionsAction = true;
    public $useSeoOptions           = true;

    public function getMetaListFields() {
        return [
            'query'        => LANG_SEARCH_QUERY,
            'target'       => LANG_SEARCH_TARGET,
            'target_title' => LANG_SEARCH_TARGET_TITLE
        ];
    }

}
