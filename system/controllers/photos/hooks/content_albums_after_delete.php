<?php

class onPhotosContentAlbumsAfterDelete extends cmsAction {

    public function run($album) {

        $this->model->deletePhotos($album['id']);

        return $album;
    }

}
