<?php

class actionPhotosDownload extends cmsAction {

    private $file_types = [
        'webp' => 'image/webp',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp'
    ];

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

        $image_path = $this->cms_config->upload_path . $photo['image'][$preset];
        if (!is_readable($image_path)) {
            return cmsCore::error404();
        }

        $ext = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

        $name = htmlspecialchars($photo['title']) . ' ' . $photo['sizes'][$preset]['width'] . '×' . $photo['sizes'][$preset]['height'] . '.' . $ext;
        header('Content-Disposition: attachment; filename="' . $name . '"' . "\n");
        header('Content-type: ' . $this->file_types[$ext]);

        readfile($image_path);

        return $this->halt();
    }

}
