<?php

class actionPhotosUpload extends cmsAction{

    public function run($album_id = null){

        if (!cmsUser::isAllowed('albums', 'add')) { cmsCore::error404(); }

        $request_album_id = $this->request->get('album_id', 0);

        if($request_album_id){
            $album_id = $request_album_id;
        } else {
            $album_id = (int)$album_id;
        }

        if ($this->request->isAjax()){

            return $this->processUpload($album_id);

        } else {

            return $this->showUploadForm($album_id);

        }

    }

    public function showUploadForm($album_id){

        if (!cmsUser::isAllowed('albums', 'add')) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentTypeByName('albums');

        if($album_id){
            $album = $content_model->getContentItem('albums', $album_id);
            if($album){
                if ($this->cms_user->id == $album['id']){
                    $content_model->disablePrivacyFilter();
                }
            }
        }

        if ($this->cms_user->is_admin){
			$content_model->disablePrivacyFilter();
        }

		$albums = $content_model->
					filterEqual('user_id', $this->cms_user->id)->
					filterOr()->
					filterEqual('is_public', 1)->
					orderByList(array(
						array('by' => 'is_public', 'to' => 'asc'),
						array('by' => 'date_pub', 'to' => 'desc'),
					))->getContentItems('albums');

        if (!$albums){

            $group_id = $this->request->get('group_id', 0);

            $this->redirect(href_to('albums', 'add').($group_id ? '?group_id='.$group_id : ''));
        }

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        if ($this->request->has('submit')){

            if (!isset($albums[$album_id])){ $this->redirectBack(); }
            if (!$this->request->has('photos')) { $this->redirectBack(); }
            if (!$this->request->has('content')) { $this->redirectBack(); }

            $album = $albums[$album_id];

            // данные
            $photo_titles      = $this->request->get('photos', array());
            $photo_contents    = $this->request->get('content', array());
            $photo_is_privates = $this->request->get('is_private', array());
            $photo_types = array();
            if(!empty($this->options['types'])){
                $photo_types = $this->request->get('type', array());
            }

            if (!$photo_titles) { $this->redirectBack(); }

            // по ключам названий определяем id фотографий
            $_photo_ids = array_keys($photo_titles);
            // ключи могут быть только числовые
            $photo_ids = array_filter($_photo_ids, function ($v){
                return is_numeric($v);
            });

            if (!$photo_ids) { $this->redirectBack(); }

            // формируем массив для каждой фотографии
            $photo_list = array();
            $last_order = $this->model->filterEqual('album_id', $album['id'])->getNextOrdering('photos');

            foreach ($photo_ids as $photo_id) {

                // эти данные должны существовать, пусть даже и пустые
                // если их нет, значит запрос подделанный
                if(!isset($photo_titles[$photo_id]) ||
                        !isset($photo_contents[$photo_id])){

                    $this->model->deletePhoto($photo_id); continue;

                }

                $_photo = array(
                    'date_pub'   => null,
                    'album_id'   => $album['id'],
                    'title'      => strip_tags($photo_titles[$photo_id] ? $photo_titles[$photo_id] : sprintf(LANG_PHOTOS_PHOTO_UNTITLED, $photo_id)),
                    'content_source' => ($photo_contents[$photo_id] ? $photo_contents[$photo_id] : null),
                    'content'        => ($photo_contents[$photo_id] ? cmsEventsManager::hook('html_filter', [
                        'text'         => $photo_contents[$photo_id],
                        'is_auto_br'   => (!$editor_params['editor'] || $editor_params['editor'] == 'markitup'),
                        'build_smiles' => $editor_params['editor'] == 'markitup'
                    ]) : null),
                    'is_private' => (isset($photo_is_privates[$photo_id]) ? (int)$photo_is_privates[$photo_id] : 0),
                    'type'       => (isset($photo_types[$photo_id]) ? (int)$photo_types[$photo_id] : null),
                    'ordering'   => $last_order
                );

                $photo_list[$photo_id] = $_photo;

                $last_order++;

            }

            $photos = $this->model->assignPhotoList($photo_list);

            list($photos, $album, $ctype) = cmsEventsManager::hook('content_photos_after_add', array($photos, $album, $ctype));

            $activity_thumb_images = array();

            $photos_count = count($photos);
            if ($photos_count > 5) { $photos = array_slice($photos, 0, 4); }

            if ($photos_count){
                foreach($photos as $photo){

                    $_presets = array_keys($photo['image']);
                    $small_preset = end($_presets);

                    $activity_thumb_images[] = array(
                        'url'   => href_to_rel('photos', $photo['slug'].'.html'),
                        'src'   => html_image_src($photo['image'], $small_preset),
                        'title' => $photo['title']
                    );
                }
            }

            cmsCore::getController('activity')->addEntry($this->name, 'add.photos', array(
                'user_id'       => $this->cms_user->id,
                'subject_title' => $album['title'],
                'subject_id'    => $album['id'],
                'subject_url'   => href_to_rel('albums', $album['slug'] . '.html'),
                'is_private'    => isset($album['is_private']) ? $album['is_private'] : 0,
                'group_id'      => isset($album['parent_id']) ? $album['parent_id'] : null,
                'images'        => $activity_thumb_images,
                'images_count'  => $photos_count
            ));

            $this->redirect(href_to('albums', $albums[$album_id]['slug'] . '.html'));

        }

        $photos = $this->model->getOrphanPhotos($this->cms_user->id);

        if (!isset($albums[$album_id])){ $album_id = false; }

        $_albums_select = array(); $num = 0;
        foreach ($albums as $album) {
            if (!empty($album['parent_title'])){
                if ($album['is_public']) { $album['title'] = '[' . LANG_PHOTOS_PUBLIC_ALBUM . '] ' . $album['title']; }
                $_albums_select[$album['parent_title']][] = $album;
            } elseif($album['is_public']) {
                $_albums_select[LANG_PHOTOS_PUBLIC_ALBUMS][] = $album;
            } else {
                $_albums_select[LANG_PHOTOS_USER_ALBUMS][] = $album;
            }
        }
        $albums_select = array(''=>'');
        foreach ($_albums_select as $album_type=>$_albums) {
            $albums_select['opt'.$num] = array($album_type);
            foreach ($_albums as $album) {
                $albums_select[$album['id']] = $album['title'];
            }
            $num++;
        }

        $this->cms_template->render('upload', array(
            'title'         => LANG_PHOTOS_UPLOAD,
            'editor_params' => $editor_params,
            'is_edit'       => false,
            'ctype'         => $ctype,
            'albums'        => $albums,
            'album'         => (isset($albums[$album_id]) ? $albums[$album_id] : array()),
            'albums_select' => $albums_select,
            'photos'        => $photos,
            'preset_big'    => $this->options['preset'],
            'types'         => (!empty($this->options['types']) ? (array('' => LANG_PHOTOS_NO_TYPE) + $this->options['types']) : array()),
            'album_id'      => $album_id
        ));

    }

    public function processUpload($album_id){

        $album = $this->model->getAlbum($album_id);

        if (!$album){
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'error'   => sprintf(LANG_PHOTOS_SELECT_ALBUM, $album['ctype']['labels']['one'])
            ));
        }

        if (!$album['is_public'] && ($album['user_id'] != $this->cms_user->id) && !$this->cms_user->is_admin){
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'error'   => 'access error'
            ));
        }

        // получаем пресеты, которые нужно создать
        $presets = cmsCore::getModel('images')->orderByList(array(
            array('by' => 'is_square', 'to' => 'asc'),
            array('by' => 'width', 'to' => 'desc')
        ))->filterIsNull('is_internal')->getPresets();

        if(!$presets || empty($this->options['sizes'])){
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'error'   => 'no presets'
            ));
        }

        $result = $this->cms_uploader->setAllowedMime([
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ])->upload('qqfile');

        if ($result['success']){

            try {
                $image = new cmsImages($result['path']);
            } catch (Exception $exc) {
                $result['success'] = false;
                $result['error']   = LANG_UPLOAD_ERR_MIME;
            }

        }

        if (!$result['success']){
            if(!empty($result['path'])){
                files_delete_file($result['path'], 2);
            }
            return $this->cms_template->renderJSON($result);
        }

        $result['paths']['original'] = $result['url'];

		foreach($presets as $p){

			if (!in_array($p['name'], $this->options['sizes'], true)){
				continue;
			}

            $resized_path = $image->resizeByPreset($p);

            if (!$resized_path) { continue; }

			$result['paths'][$p['name']] = $resized_path;

		}

        $result['filename'] = basename($result['path']);

        // основную exif информацию берём из оригинала
        $image_data = img_get_params($result['path']);

        // если оригинал удаляется, то размеры берём из пресета просмотра на странице
        $big_image_data = img_get_params($this->cms_config->upload_path.$result['paths'][$this->options['preset']]);
        // ориентацию берем из большого фото, т.к. оригиналы автоматически не поворачиваются
        $image_data['orientation'] = $big_image_data['orientation'];

        if (empty($this->options['is_origs'])){

			@unlink($result['path']);
			unset($result['paths']['original']);

            $image_data['width'] = $big_image_data['width'];
            $image_data['height'] = $big_image_data['height'];

		}

        unset($result['path']);

        // маленкая картинка
        $last_image = end($result['paths']);
        $result['url'] = $this->cms_config->upload_host.'/'.$last_image;
        // большая картинка
        $first_image = reset($result['paths']);
        $result['big_url'] = $this->cms_config->upload_host.'/'.$first_image;

        $sizes = array();
        foreach ($result['paths'] as $name => $relpath) {

            $s = getimagesize($this->cms_config->upload_path.$relpath);
            if ($s === false) { continue; }

            $sizes[$name] = array(
                'width'  => $s[0],
                'height' => $s[1]
            );

        }

        $date_photo = (isset($image_data['exif']['date']) ? $image_data['exif']['date'] : false);
        $camera     = (isset($image_data['exif']['camera']) ? $image_data['exif']['camera'] : null);
        unset($image_data['exif']['date'], $image_data['exif']['camera'], $image_data['exif']['orientation']);

        $result['id'] = $this->model->addPhoto(array(
            'album_id'    => $album['id'],
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
        ));

        return $this->cms_template->renderJSON($result);

    }

}
