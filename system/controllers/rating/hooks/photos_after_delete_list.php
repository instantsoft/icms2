<?php

class onRatingPhotosAfterDeleteList extends cmsAction {

    public function run($data){

        list($photos, $album_id) = $data;

        foreach ($photos as $photo) {

            $this->model->deleteVotes('photos', 'photo', $photo['id']);
        }

        return [$photos, $album_id];
    }

}
