<?php

class onPhotosCommentsTargets extends cmsAction {

    public function run(){

        return array(
            'name'  => $this->name,
            'types' => array(
                $this->name.':photo' => LANG_PHOTOS_CONTROLLER
            )
        );

    }

}
