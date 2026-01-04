<?php

class onPhotosContentAlbumsAfterAdd extends cmsAction {

    public function run($album){

        if (!isset($album['id'])) { return $album; }

        if (!$album['is_approved']) { return $album; }

        $this->request->set('back', href_to($this->name, 'upload', [$album['id']]));

        return $album;
    }

}
