<?php

class onPhotosContentAlbumsAfterDelete extends cmsAction {

    public function run($album){

        $this->model->deletePhotos($album['id']);

        cmsCore::getController('activity')->deleteEntry('photos', 'add.photos', $album['id']);

        return true;

    }

}
