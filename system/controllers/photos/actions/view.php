<?php
/**
 * @property \modelPhotos $model
 * @property \moderation $controller_moderation
 */
class actionPhotosView extends cmsAction {

    use icms\controllers\content\traits\contentAccessTrait;

    private $related_title = '';

    protected $extended_langs = ['content'];

    public function run($id = false){

        // редиректим со старых адресов
        if ($id) {

            $photo = $this->model->getPhoto($id);

            if (!$photo || !$photo['slug']) {
                return cmsCore::error404();
            }

            return $this->redirect(href_to('photos', $photo['slug'] . '.html'), 301);
        }

        $slug = $this->request->get('slug', '');
        if (!$slug) {
            return cmsCore::error404();
        }

        $photo = $this->model->getPhoto($slug);
        if (!$photo) {
            return cmsCore::error404();
        }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album) {
            return cmsCore::error404();
        }

        $ctype = $album['ctype'];
        unset($album['ctype']);

        [$photo, $album, $ctype] = cmsEventsManager::hook('photos_before_item', [$photo, $album, $ctype]);

        // Проверяем прохождение модерации
        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $album);

        // Проверяем доступ к контенту
        [$access_status, $access_value, $access_param] = $this->checkContentItemAccess($ctype, $album, $is_moderator);

        switch ($access_status) {
            case 'forbidden':
                return cmsCore::errorForbidden($access_value);
            case 'not_found':
                return cmsCore::error404();
            case 'redirect':
                if ($access_param) {
                    cmsUser::addSessionMessage($access_param, 'error');
                }
                return $this->redirect($access_value);
        }

        if ($access_value) {
            foreach ($access_value as $notice) {
                cmsUser::addSessionMessage($notice, 'info');
            }
        }

        // Проверяем приватность фото
        [$redirect_url, $item_view_notice] = $this->checkParentAccess($ctype, $photo, $is_moderator);

        if ($item_view_notice) {
            cmsUser::addSessionMessage($item_view_notice, 'error');
        }

        if (is_string($redirect_url)) {
            // Игнорируем урл редиректа, перенаправляем на альбом
            return $this->redirect(href_to($ctype['name'], $album['slug'] . '.html'));
        }

        if ($this->cms_user->id != $photo['user_id']) {
            $this->model->incrementCounter($photo['id']);
        }

        $is_can_set_cover = (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $this->cms_user->id));

        $is_can_edit = (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $this->cms_user->id) ||
                ($photo['user_id'] == $this->cms_user->id));

        $is_can_delete = (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $album['user_id'] == $this->cms_user->id) ||
                ($photo['user_id'] == $this->cms_user->id));

        $downloads           = $this->getDownloadImages($photo);
        $available_downloads = array_filter($downloads, function ($item) {
            return !empty($item['link']);
        });
        $full_size_img_preset = '';
        if ($available_downloads) {

            $download_photo_sizes = [];

            foreach ($available_downloads as $preset => $data) {
                $download_photo_sizes[$preset] = $photo['sizes'][$preset];
            }

            $full_size_img_preset = $this->getMaxSizePresetName($download_photo_sizes);
        }

        $ordering = $this->request->get('ordering', $this->options['ordering']);
        $orderto  = $this->request->get('orderto', $this->options['orderto']);

        if (!in_array($ordering, array_keys(modelPhotos::getOrderList()))) {
            return cmsCore::error404();
        }

        if (!in_array($orderto, ['asc', 'desc'])) {
            return cmsCore::error404();
        }

        $photos_url_params = [];
        if ($ordering != $this->options['ordering']) {
            $photos_url_params['ordering'] = $ordering;
        }
        if ($orderto != $this->options['orderto']) {
            $photos_url_params['orderto'] = $orderto;
        }
        if ($photos_url_params) {
            $photos_url_params = http_build_query($photos_url_params);
        }

        // Навигация
        list($prev_photo, $next_photo) = $this->getNavPhotos($photo, $ordering, $orderto);

        $tpl    = 'view';
        $preset = $this->getBigPreset($photo['sizes']);

        if ($this->request->isAjax()) {

            $tpl = 'view_photo_container';
            if ($full_size_img_preset) {
                $preset = $full_size_img_preset;
            }
        }

        cmsModel::cacheResult('current_photo_item', [$album, $photo]);

        return $this->cms_template->render($tpl, [
            'photos_url_params' => $photos_url_params,
            'row_height'        => $this->getRowHeight((!empty($this->options['preset_related']) ? 'preset_related' : 'preset_small')),
            'next_photo'        => $next_photo,
            'prev_photo'        => $prev_photo,
            'hide_info_block'   => !empty($this->options['hide_photo_item_info']),
            'downloads'         => !empty($this->options['allow_download']) ? $downloads : [],
            'is_can_set_cover'  => $is_can_set_cover,
            'is_can_edit'       => $is_can_edit,
            'is_can_delete'     => $is_can_delete,
            'user'              => $this->cms_user,
            'preset'            => $preset,
            'preset_small'      => (!empty($this->options['preset_related']) ? $this->options['preset_related'] : $this->options['preset_small']),
            'photo'             => $photo,
            'photos'            => $this->getRelatedPhoto($photo),
            'related_title'     => $this->related_title,
            'album'             => $album,
            'ctype'             => $ctype,
            'photo_details'     => $this->buildPhotoDetails($photo, $album, $ctype),
            'hooks_html'        => cmsEventsManager::hookAll('photos_item_html', $photo),
            'full_size_img'     => ($full_size_img_preset ? $available_downloads[$full_size_img_preset]['image'] : '')
        ]);
    }

    private function getNavPhotos($photo, $ordering, $orderto) {

        switch ($orderto) {
            case 'asc':

                $prev_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                        getNextPhoto($photo, $ordering, 'desc');
                $next_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                        getPrevPhoto($photo, $ordering, $orderto);
                break;

            case 'desc':

                $next_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                        getNextPhoto($photo, $ordering, $orderto);
                $prev_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                        getPrevPhoto($photo, $ordering, 'asc');
                break;
            default:
                $prev_photo = [];
                $next_photo = [];
                break;
        }

        return [$prev_photo, $next_photo];
    }

    private function getDownloadImages($photo) {

        $downloads = [];

        $presets = cmsCore::getModel('images')->getPresetsList();

        $selected = $isset = false;

        foreach ($photo['image'] as $preset => $path) {

            $show = true;

            if (!empty($this->options['download_view'][$preset]) &&
                    !$this->cms_user->isInGroups($this->options['download_view'][$preset])) {
                $show = false;
            }
            if (!empty($this->options['download_hide'][$preset]) &&
                    $this->cms_user->isInGroups($this->options['download_hide'][$preset])) {
                $show = false;
            }

            if (!$isset && $show) {
                $selected = $isset = true;
            } else {
                $selected = false;
            }

            $downloads[$preset] = [
                'link'   => ($show ? href_to('photos', 'download', [$photo['id'], $preset]) . '?hash=' . $this->getDownloadHash($photo['id']) : ''),
                'size'   => $photo['sizes'][$preset]['width'] . '×' . $photo['sizes'][$preset]['height'],
                'name'   => $presets[$preset],
                'preset' => $preset,
                'image'  => ($show ? html_image_src($photo['image'], $preset, true) : ''),
                'select' => $selected
            ];
        }

        return $downloads;
    }

    private function getBigPreset($sizes) {

        if (isset($sizes[$this->options['preset']])) {
            return $this->options['preset'];
        }

        unset($sizes['original']);

        return $this->getMaxSizePresetName($sizes);
    }

    private function getMaxSizePresetName($sizes, $_size = 'width') {

        $__sizes = $presets = [];

        foreach ($sizes as $preset => $size) {
            $__sizes[] = $size[$_size];
            $presets[] = $preset;
        }

        return $presets[array_search(max($__sizes), $__sizes)];
    }

    private function buildPhotoDetails($photo, $album, $ctype) {

        $first_img = current($photo['image']);

        $details = [
            [
                'name'  => LANG_PHOTOS_FORMAT,
                'value' => strtoupper(pathinfo($first_img, PATHINFO_EXTENSION))
            ],
            [
                'name'  => LANG_PHOTOS_SIZE,
                'value' => $photo['width'] . '×' . $photo['height']
            ]
        ];

        if ($photo['date_photo']) {
            $details[] = [
                'name'  => LANG_PHOTOS_DATE,
                'value' => html_date_time($photo['date_photo'])
            ];
        }
        if (!empty($this->options['types'][$photo['type']])) {
            $details[] = [
                'name'  => LANG_PHOTOS_O_TYPE1,
                'value' => $this->options['types'][$photo['type']]
            ];
        }
        $details[] = [
            'name'  => LANG_PHOTOS_ALBUM,
            'value' => $album['title'],
            'link'  => href_to($ctype['name'], $album['slug']) . '.html'
        ];
        $details[] = [
            'name'  => LANG_HITS,
            'value' => html_spellcount($photo['hits_count'], LANG_HITS_SPELL)
        ];
        $details[] = [
            'name'  => LANG_PHOTOS_DOWNLOADS,
            'value' => $photo['downloads_count']
        ];

        list($details, $photo, $album, $ctype) = cmsEventsManager::hook('build_photo_details', [$details, $photo, $album, $ctype]);

        return $details;
    }

    private function getRelatedPhoto($photo) {

        if (empty($this->options['related_limit'])) {
            return [];
        }

        $this->related_title = LANG_PHOTOS_RELATED;

        $this->model->filterNotEqual('id', $photo['id']);

        $this->model->filterRelated(['title', 'content'], $photo['title']);

        if (cmsUser::isAllowed('albums', 'view_all')) {
            $this->model->disablePrivacyFilter();
        }

        $this->model->limit($this->options['related_limit']);

        $photos = $this->getPhotosList();

        if (!$photos) {

            $this->model->limit($this->options['related_limit']);

            $this->model->filterEqual('album_id', $photo['album_id']);

            $this->model->filterNotEqual('id', $photo['id']);

            $photos = $this->getPhotosList();

            $this->related_title = LANG_PHOTOS_OTHER_IN_ALBUM;
        }

        return $photos;
    }
}
