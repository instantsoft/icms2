<?php

class onFrontpageSitemapUrls extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($name) {

        return [
            [
                'last_modified' => date('Y-m-d'),
                'title'         => LANG_HOME,
                'url'           => href_to_home(true)
            ]
        ];
    }

}
