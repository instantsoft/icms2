<?php

class actionPhotosDownload extends cmsAction {

    use icms\controllers\content\traits\contentAccessTrait;

    public function run($photo_id = null, $preset = null) {

        if (!$photo_id || !$preset) {
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

        $ctype = $album['ctype'];

        if (!isset($photo['image'][$preset])) {
            return cmsCore::error404();
        }

        // Проверяем прохождение модерации
        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $album);

        // Проверяем доступ к альбому, всё что не прошло проверку, 404
        [$access_status, $access_value, $access_param] = $this->checkContentItemAccess($ctype, $album, $is_moderator);

        if ($access_status !== 'allowed') {
            return cmsCore::error404();
        }

        // Проверяем приватность фото
        [$redirect_url, $item_view_notice] = $this->checkParentAccess($ctype, $photo, $is_moderator);
        if (is_string($redirect_url)) {
            return cmsCore::error404();
        }

        if (!$this->validateDownloadHash($this->request->get('hash', ''), $photo['id'])) {
            return $this->redirect(href_to('photos', $photo['slug'] . '.html'));
        }

        if (!empty($this->options['download_view'][$preset]) &&
                !$this->cms_user->isInGroups($this->options['download_view'][$preset])) {
            return cmsCore::error404();
        }

        if (!empty($this->options['download_hide'][$preset]) &&
                $this->cms_user->isInGroups($this->options['download_hide'][$preset])) {
            return cmsCore::error404();
        }

        if ($this->cms_user->id != $photo['user_id']) {
            $this->model->incrementCounter($photo['id'], 'downloads_count');
        }

        session_write_close();

        $ext = strtolower(pathinfo($photo['image'][$preset], PATHINFO_EXTENSION));

        $name = $photo['title'] . ' ' . $photo['sizes'][$preset]['width'] . '×' . $photo['sizes'][$preset]['height'] . '.' . $ext;

        return $this->cms_core->response->sendDownloadFile($photo['image'][$preset], $name);
    }

}
