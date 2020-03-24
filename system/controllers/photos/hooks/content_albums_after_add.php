<?php

class onPhotosContentAlbumsAfterAdd extends cmsAction {

    public function run($album){

        if (!isset($album['id'])) { return $album; }

        if (!$album['is_approved']) { return $album; }

        $this->redirectToAction('upload', array($album['id']));

        return $album;

    }

}
