<?php

class onFrontpageSitemapSources extends cmsAction {

    public function run() {

        return [
            'name' => $this->name,
            'sources' => [
                'root' => LANG_HOME
            ]
        ];
    }

}
