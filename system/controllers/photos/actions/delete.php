<?php

class actionPhotosDelete extends cmsAction {

    public function run($photo_id = null) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$photo_id) {

            $photo_id = $this->request->get('id', 0);

            if (!$photo_id) {
                return cmsCore::error404();
            }
        }

        $photo = $this->model->getPhoto($photo_id);
        if (!$photo) {
            return cmsCore::error404();
        }

        $success = true;

        // проверяем наличие доступа
        if (!cmsUser::isAllowed('albums', 'edit')) {
            $success = false;
        }
        if (!cmsUser::isAllowed('albums', 'edit', 'all') && $photo['user_id'] != $this->cms_user->id) {
            $success = false;
        }

        if (!$success) {
            return $this->cms_template->renderJSON([
                'success' => false
            ]);
        }

        $album = cmsCore::getModel('content')->getContentItem('albums', $photo['album_id']);

        list($album, $photo) = cmsEventsManager::hook('photos_before_delete', [$album, $photo]);

        $this->model->deletePhoto($photo);

        list($album, $photo) = cmsEventsManager::hook('photos_after_delete', [$album, $photo]);

        return $this->cms_template->renderJSON([
            'success'   => true,
            'album_url' => href_to('albums', $album['slug'] . '.html')
        ]);
    }

}
