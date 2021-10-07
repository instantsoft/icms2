<?php

class actionPhotosSetCover extends cmsAction{

    public function run($photo_id = 0){

		if (!$photo_id) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($photo_id);
        if (!$photo) { cmsCore::error404(); }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album) { cmsCore::error404(); }

        $is_can_set_cover = (cmsUser::isAllowed($album['ctype']['name'], 'edit', 'all') ||
                (cmsUser::isAllowed($album['ctype']['name'], 'edit', 'own') && $album['user_id'] == $this->cms_user->id));

        if (!$is_can_set_cover) { cmsCore::error404(); }

        $this->model->updateAlbumCoverImage($album['id'], array($photo['id']));

        cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

        $back_url = $this->getRequestBackUrl();

        if ($back_url){
            $this->redirect($back_url);
        } else {
            $this->redirect(href_to('photos', $photo['slug'] . '.html'));
        }

    }

}
