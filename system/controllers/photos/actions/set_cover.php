<?php

class actionPhotosSetCover extends cmsAction {

    public function run($photo_id = 0) {

        if (!$photo_id) {
            return cmsCore::error404();
        }

        $photo = $this->model->getPhoto($photo_id);
        if (!$photo) {
            return cmsCore::error404();
        }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album) {
            return cmsCore::error404();
        }

        $is_can_set_cover = (cmsUser::isAllowed($album['ctype']['name'], 'edit', 'all') ||
                (cmsUser::isAllowed($album['ctype']['name'], 'edit', 'own') && $album['user_id'] == $this->cms_user->id));

        if (!$is_can_set_cover) {
            return cmsCore::error404();
        }

        $this->model->updateAlbumCoverImage($album['id'], [$photo['id']]);

        cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

        $back_url = $this->getRequestBackUrl();

        if ($back_url) {
            return $this->redirect($back_url);
        }

        return $this->redirect(href_to('photos', $photo['slug'] . '.html'));
    }

}
