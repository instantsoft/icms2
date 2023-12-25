<?php
/**
 * @property \modelPhotos $model
 * @property \modelContent $model_content
 */
class actionPhotosUpload extends cmsAction {

    private $allow_add_public_albums = false;
    private $ctype, $album = [];

    public function run($album_id = null) {

        $request_album_id = $this->request->get('album_id', 0);

        if ($request_album_id) {
            $album_id = $request_album_id;
        } else {
            $album_id = (int) $album_id;
        }

        $this->ctype = $this->model_content->getContentTypeByName('albums');
        if (!$this->ctype) {
            return cmsCore::error404();
        }

        if (!empty($this->options['allow_add_public_albums']) &&
                $this->cms_user->isInGroups($this->options['allow_add_public_albums'])) {
            $this->allow_add_public_albums = true;
        }

        // Если альбом не знаем, проверяем правило доступа на его создание
        if (!$album_id) {

            if (!cmsUser::isAllowed('albums', 'add')) {
                return cmsCore::error404();
            }

        } else {

            $this->album = $this->model_content->getContentItem('albums', $album_id);

            if ($this->album) {

                if (!$this->isAllowedUpload($this->album)) {
                    return cmsCore::error404();
                }

                if ($this->cms_user->id == $this->album['id']) {
                    $this->model_content->disablePrivacyFilter();
                }

            } else {
                return cmsCore::error404();
            }
        }

        if ($this->request->isAjax()) {
            return $this->processUpload();
        }

        return $this->showUploadForm();
    }

    private function isAllowedUpload($album) {
        return (!empty($album['is_public']) && $this->allow_add_public_albums) ||
                (empty($album['id']) && cmsUser::isAllowed($this->ctype['name'], 'add')) ||
                ($this->cms_user->id && !empty($album['user_id']) && $album['user_id'] == $this->cms_user->id) ||
                $this->cms_user->is_admin;

    }

    public function showUploadForm() {

        if ($this->cms_user->is_admin) {
            $this->model_content->disablePrivacyFilter();
        }

        $this->model_content->filterEqual('user_id', $this->cms_user->id);

        if ($this->allow_add_public_albums) {
            $this->model_content->filterOr()->filterEqual('is_public', 1);
        }

        $albums = $this->model_content->orderByList([
                    ['by' => 'is_public', 'to' => 'asc'],
                    ['by' => 'date_pub', 'to' => 'desc']
                ])->getContentItems('albums');

        if (!$albums) {

            $group_id = $this->request->get('group_id', 0);

            return $this->redirect(href_to('albums', 'add') . ($group_id ? '?group_id=' . $group_id : ''));
        }

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);
        $editor_params['options']['id'] = false;

        if ($this->request->has('submit')) {

            if (!$this->request->has('photos')) {
                return $this->redirectBack();
            }
            if (!$this->request->has('content')) {
                return $this->redirectBack();
            }

            // данные
            $photo_titles      = $this->request->get('photos', []);
            $photo_contents    = $this->request->get('content', []);
            $photo_is_privates = $this->request->get('is_private', []);
            $photo_types       = [];
            if (!empty($this->options['types'])) {
                $photo_types = $this->request->get('type', []);
            }

            if (!$photo_titles) {
                return $this->redirectBack();
            }

            // по ключам названий определяем id фотографий
            $_photo_ids = array_keys($photo_titles);
            // ключи могут быть только числовые
            $photo_ids  = array_filter($_photo_ids, function ($v) {
                return is_numeric($v);
            });

            if (!$photo_ids) {
                return $this->redirectBack();
            }

            // формируем массив для каждой фотографии
            $photo_list = [];
            $last_order = $this->model->filterEqual('album_id', $this->album['id'])->getNextOrdering('photos');

            foreach ($photo_ids as $photo_id) {

                // эти данные должны существовать, пусть даже и пустые
                // если их нет, значит запрос подделанный
                if (!isset($photo_titles[$photo_id]) || is_array($photo_titles[$photo_id]) ||
                        !isset($photo_contents[$photo_id]) || is_array($photo_contents[$photo_id])) {
                    $this->model->filterEqual('user_id', $this->cms_user->id)->deletePhoto($photo_id);
                    continue;
                }

                $_photo = [
                    'date_pub'       => null,
                    'album_id'       => $this->album['id'],
                    'title'          => strip_tags($photo_titles[$photo_id] ? $photo_titles[$photo_id] : sprintf(LANG_PHOTOS_PHOTO_UNTITLED, $photo_id)),
                    'content_source' => ($photo_contents[$photo_id] ? cmsEventsManager::hook('html_filter', [
                        'text'         => $photo_contents[$photo_id],
                        'is_process_callback' => false,
                        'typograph_id' => $this->options['typograph_id'],
                        'is_auto_br'   => false
                    ]) : null),
                    'content'        => ($photo_contents[$photo_id] ? cmsEventsManager::hook('html_filter', [
                        'text'         => $photo_contents[$photo_id],
                        'typograph_id' => $this->options['typograph_id'],
                        'is_auto_br'   => !$editor_params['editor'] ? true : null
                    ]) : null),
                    'is_private'     => (isset($photo_is_privates[$photo_id]) ? (int) $photo_is_privates[$photo_id] : 0),
                    'type'           => (isset($photo_types[$photo_id]) ? (int) $photo_types[$photo_id] : null),
                    'ordering'       => $last_order
                ];

                $photo_list[$photo_id] = $_photo;

                $last_order++;
            }

            $photos = $this->model->assignPhotoList($photo_list);

            list($photos, $this->album, $this->ctype) = cmsEventsManager::hook('content_photos_after_add', [$photos, $this->album, $this->ctype]);

            return $this->redirect(href_to('albums', $this->album['slug'] . '.html'));
        }

        $photos = $this->model->getOrphanPhotos($this->cms_user->id);

        $_albums_select = [];
        $num = 0;

        foreach ($albums as $album) {
            if (!empty($album['parent_title'])) {
                if ($album['is_public']) {
                    $album['title'] = '[' . LANG_PHOTOS_PUBLIC_ALBUM . '] ' . $album['title'];
                }
                $_albums_select[$album['parent_title']][] = $album;
            } elseif ($album['is_public']) {
                $_albums_select[LANG_PHOTOS_PUBLIC_ALBUMS][] = $album;
            } else {
                $_albums_select[LANG_PHOTOS_USER_ALBUMS][] = $album;
            }
        }

        $albums_select = ['' => ''];
        foreach ($_albums_select as $album_type => $_albums) {
            $albums_select['opt' . $num] = array($album_type);
            foreach ($_albums as $album) {
                $albums_select[$album['id']] = $album['title'];
            }
            $num++;
        }

        return $this->cms_template->render('upload', [
            'title'         => LANG_PHOTOS_UPLOAD,
            'allow_add'     => cmsUser::isAllowed($this->ctype['name'], 'add'),
            'editor_params' => $editor_params,
            'is_edit'       => false,
            'ctype'         => $this->ctype,
            'albums'        => $albums,
            'album'         => $this->album,
            'albums_select' => $albums_select,
            'photos'        => $photos,
            'preset_big'    => $this->options['preset'],
            'types'         => (!empty($this->options['types']) ? (['' => LANG_PHOTOS_NO_TYPE] + $this->options['types']) : []),
            'album_id'      => (!empty($this->album['id']) ? $this->album['id'] : false)
        ]);
    }

    public function processUpload() {

        if (!$this->album) {

            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => sprintf(LANG_PHOTOS_SELECT_ALBUM, $this->ctype['labels']['one'])
            ]);
        }

        // получаем пресеты, которые нужно создать
        $presets = cmsCore::getModel('images')->orderByList([
                    ['by' => 'is_square', 'to' => 'asc'],
                    ['by' => 'width', 'to' => 'desc']
                ])->filterIsNull('is_internal')->getPresets();

        if (!$presets || empty($this->options['sizes'])) {

            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => 'no presets'
            ]);
        }

        $result = $this->cms_uploader->setAllowedMime([
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ])->upload('qqfile');

        if ($result['success']) {

            try {
                $image = new cmsImages($result['path']);
            } catch (Exception $exc) {
                $result['success'] = false;
                $result['error']   = LANG_UPLOAD_ERR_MIME;
            }
        }

        if (!$result['success']) {
            if (!empty($result['path'])) {
                files_delete_file($result['path'], 2);
            }
            return $this->cms_template->renderJSON($result);
        }

        $result['paths']['original'] = $result['url'];

        foreach ($presets as $p) {

            if (!in_array($p['name'], $this->options['sizes'], true)) {
                continue;
            }

            $resized_path = $image->resizeByPreset($p);

            if (!$resized_path) {
                continue;
            }

            $result['paths'][$p['name']] = $resized_path;
        }

        $result['filename'] = basename($result['path']);

        // основную exif информацию берём из оригинала
        $image_data = img_get_params($result['path']);

        // если оригинал удаляется, то размеры берём из пресета просмотра на странице
        $big_image_data = img_get_params($this->cms_config->upload_path . $result['paths'][$this->options['preset']]);
        // ориентацию берем из большого фото, т.к. оригиналы автоматически не поворачиваются
        $image_data['orientation'] = $big_image_data['orientation'];

        if (empty($this->options['is_origs'])) {

            @unlink($result['path']);
            unset($result['paths']['original']);

            $image_data['width']  = $big_image_data['width'];
            $image_data['height'] = $big_image_data['height'];
        }

        unset($result['path']);

        // маленкая картинка
        $last_image        = end($result['paths']);
        $result['url']     = $this->cms_config->upload_host . '/' . $last_image;
        // большая картинка
        $first_image       = reset($result['paths']);
        $result['big_url'] = $this->cms_config->upload_host . '/' . $first_image;

        $sizes = [];
        foreach ($result['paths'] as $name => $relpath) {

            $s = getimagesize($this->cms_config->upload_path . $relpath);
            if ($s === false) {
                continue;
            }

            $sizes[$name] = [
                'width'  => $s[0],
                'height' => $s[1]
            ];
        }

        $date_photo = (isset($image_data['exif']['date']) ? $image_data['exif']['date'] : false);
        $camera     = (isset($image_data['exif']['camera']) ? $image_data['exif']['camera'] : null);
        unset($image_data['exif']['date'], $image_data['exif']['camera'], $image_data['exif']['orientation']);

        $result['id'] = $this->model->addPhoto([
            'album_id'    => $this->album['id'],
            'user_id'     => $this->cms_user->id,
            'image'       => $result['paths'],
            'date_photo'  => $date_photo,
            'camera'      => $camera,
            'width'       => $image_data['width'],
            'height'      => $image_data['height'],
            'sizes'       => $sizes,
            'is_private'  => 2,
            'orientation' => $image_data['orientation'],
            'exif'        => (!empty($image_data['exif']) ? $image_data['exif'] : null),
        ]);

        return $this->cms_template->renderJSON($result);
    }

}
