<?php

class onPhotosContentAlbumsAfterAdd extends cmsAction {

    public function run($album){

        if (!isset($album['id'])) { return false; }

        if (!$album['is_approved']) { return false; }

        $this->redirectToAction('upload', array($album['id']));

        return true;

    }

}
