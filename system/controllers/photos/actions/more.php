<?php
/**
 * @property \modelPhotos $model
 */
class actionPhotosMore extends cmsAction {

    public function run($target = null, $id = null) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if ($target === 'album_id') {

            if (!$id) {
                return $this->halt();
            }

            $album = $this->model->getAlbum($id);
            if (!$album) {
                return $this->halt();
            }

            $ctype = $album['ctype'];

            list($ctype, $album, $fields) = cmsEventsManager::hook('content_albums_before_item', [$ctype, $album, []]);

            return $this->runHook('content_albums_item_html', [$album]);
        }

        if ($target === 'user_id') {

            if (!$id) {
                return $this->halt();
            }

            $profile = cmsCore::getModel('users')->getUser($id);
            if (!$profile) {
                return $this->halt();
            }

            $profile['user_id'] = $profile['id']; // проверка на авторство идёт по полю user_id

            $ctype = cmsCore::getModel('content')->getContentTypeByName('albums');

            return $this->runHook('content_albums_items_html', [['user_view', $ctype, $profile, []]]);
        }

        if ($target === 'camera') {

            $camera = urldecode($this->request->get('camera', ''));
            if (!$camera) {
                return cmsCore::error404();
            }

            if (cmsUser::isAllowed('albums', 'view_all') || $this->cms_user->id == $profile['user_id']) {
                $this->model->disablePrivacyFilter();
            }

            $this->model->filterEqual('camera', $camera);

            $item = [
                'id'         => 0,
                'user_id'    => -1,
                'url_params' => ['camera' => $camera],
                'base_url'   => href_to('photos', 'camera-' . urlencode($camera))
            ];

            return $this->renderPhotosList($item, '', $this->request->get('photo_page', 1));

        }

        if (!$target) {

            if (cmsUser::isAllowed('albums', 'view_all')) {
                $this->model->disablePrivacyFilter();
            }

            $album = [
                'id'       => 0,
                'user_id'  => -1,
                'base_url' => href_to('photos')
            ];

            $album['filter_values'] = [
                'ordering'    => $this->request->get('ordering', $this->options['ordering']),
                'orderto'     => $this->request->get('orderto', $this->options['orderto']),
                'type'        => $this->request->get('type', ''),
                'orientation' => $this->request->get('orientation', ''),
                'width'       => $this->request->get('width', 0) ?: '',
                'height'      => $this->request->get('height', 0) ?: ''
            ];

            if (!in_array($album['filter_values']['ordering'], array_keys(modelPhotos::getOrderList()))) {
                return cmsCore::error404();
            }

            if (!in_array($album['filter_values']['orderto'], ['asc', 'desc'])) {
                return cmsCore::error404();
            }

            $this->model->orderByList([
                [
                    'by' => $album['filter_values']['ordering'],
                    'to' => $album['filter_values']['orderto']
                ],
                [
                    'by' => 'id',
                    'to' => $album['filter_values']['orderto']
                ]
            ]);

            if (cmsUser::isAllowed('albums', 'view_all') || $this->cms_user->id == $album['user_id']) {
                $this->model->disablePrivacyFilter();
            }

            if ($album['filter_values']['type']) {
                $this->model->filterEqual('type', $album['filter_values']['type']);
            }

            if ($album['filter_values']['orientation']) {
                $this->model->filterEqual('orientation', $album['filter_values']['orientation']);
            }

            if ($album['filter_values']['width']) {
                $this->model->filterGtEqual('width', $album['filter_values']['width']);
            }

            if ($album['filter_values']['height']) {
                $this->model->filterGtEqual('height', $album['filter_values']['height']);
            }

            return $this->renderPhotosList($album, 0, $this->request->get('photo_page', 1));
        }

        return cmsCore::error404();
    }

}
