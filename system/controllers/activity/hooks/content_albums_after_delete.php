<?php

class onActivityContentAlbumsAfterDelete extends cmsAction {

    public function run($album) {

        $this->deleteEntry('photos', 'add.photos', $album['id']);

        return $album;
    }

}
