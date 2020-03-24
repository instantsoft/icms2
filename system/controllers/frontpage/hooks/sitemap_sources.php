<?php

class onFrontpageSitemapSources extends cmsAction {

    public function run(){

        return array(
            'name' => $this->name,
            'sources' => array(
                'root' => LANG_HOME
            )
        );

    }

}
