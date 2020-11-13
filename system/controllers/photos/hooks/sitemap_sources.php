<?php

class onPhotosSitemapSources extends cmsAction {

    public function run(){

        return array(
            'name' => $this->name,
            'sources' => array(
                'photo' => LANG_PHOTOS_CONTROLLER
            )
        );

    }

}
