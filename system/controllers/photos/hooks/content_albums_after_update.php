<?php

class onPhotosContentAlbumsAfterUpdate extends cmsAction {

    public function run($album){

        $core = cmsCore::getInstance();

        if (!$core->request->has('photos')) { return false; }

        $photos = $core->request->get('photos');

        $this->model->updateAlbumCoverImage($album['id'], $photos);

        $this->model->updateAlbumPhotosCount($album['id'], sizeof($photos));

        $this->model->updatePhotoTitles($album['id'], $photos);

        return true;

    }

}
