<?php

class actionPhotosDownload extends cmsAction {

    public function run($photo_id = null, $preset = null) {

        if (!$photo_id || !$preset) {
            return cmsCore::error404();
        }

        $photo = $this->model->getPhoto($photo_id);
        if (!$photo) {
            return cmsCore::error404();
        }

        if (!isset($photo['image'][$preset])) {
            return cmsCore::error404();
        }

        $hash = $this->request->get('hash', '');
        if ($hash !== $this->getDownloadHash()) {
            return cmsCore::error404();
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
