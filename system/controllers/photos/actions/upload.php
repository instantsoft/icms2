<?php

class actionPhotosUpload extends cmsAction{

    public function run($album_id = null){

        if (!cmsUser::isAllowed('albums', 'add')) { cmsCore::error404(); }

        if ($this->request->isAjax()){

            return $this->processUpload($album_id);

        } else {

            return $this->showUploadForm($album_id);

        }

    }

    public function showUploadForm($album_id){

        if (!cmsUser::isAllowed('albums', 'add')) { cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentTypeByName('albums');

		$albums = $content_model->
					filterEqual('user_id', $user->id)->
					filterOr()->
					filterEqual('is_public', 1)->
					orderByList(array(
						array('by' => 'is_public', 'to' => 'asc'),
						array('by' => 'date_pub', 'to' => 'desc'),
					))->getContentItems('albums');

        if (!$albums){
            $this->redirect(href_to('albums', 'add'));
        }

        if ($this->request->has('submit')){

            $album_id = $this->request->get('album_id');

            if (!isset($albums[$album_id])){ $this->redirectBack(); }
            if (!$this->request->has('photos')) { $this->redirectBack(); }

            $album = $albums[$album_id];

            $photos_titles = $this->request->get('photos');

            $this->model->assignAlbumId($album_id);

            $this->model->updateAlbumCoverImage($album['id'], $photos_titles);

            $this->model->updateAlbumPhotosCount($album_id, sizeof($photos_titles));

            $this->model->updatePhotoTitles($album_id, $photos_titles);

            $activity_thumb_images = array();
            $photos = $this->model->getPhotosByIdsList(array_keys($photos_titles));

            $photos_count = count($photos);
            if ($photos_count > 5) { $photos = array_slice($photos, 0, 4); }

            if ($photos_count){
                foreach($photos as $photo){
                    $activity_thumb_images[] = array(
                        'url' => href_to('photos', 'view', $photo['id']),
                        'src' => html_image_src($photo['image'], 'small')
                    );
                }
            }

            $activity_controller = cmsCore::getController('activity');

            $activity_controller->addEntry($this->name, "add.photos", array(
                'user_id' => $user->id,
                'subject_title' => $album['title'],
                'subject_id' => $album['id'],
                'subject_url' => href_to('albums', $album['slug'] . '.html'),
                'is_private' => isset($album['is_private']) ? $album['is_private'] : 0,
                'group_id' => isset($album['parent_id']) ? $album['parent_id'] : null,
                'images' => $activity_thumb_images,
                'images_count' => $photos_count
            ));

            $this->redirect(href_to('albums', $albums[$album_id]['slug'] . '.html'));

        }

        $photos = $this->model->getOrphanPhotos();

        if (!isset($albums[$album_id])){ $album_id = false; }

        cmsTemplate::getInstance()->render('upload', array(
            'ctype' => $ctype,
            'albums' => $albums,
            'photos' => $photos,
            'album_id' => $album_id
        ));

    }

    public function processUpload($album_id){

        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        $result = $uploader->upload('qqfile');

        if ($result['success']){
            if (!$uploader->isImage($result['path'])){
                $result['success'] = false;
                $result['error']   = LANG_UPLOAD_ERR_MIME;
            }
        }

        if (!$result['success']){
            if(!empty($result['path'])){
                $uploader->remove($result['path']);
            }
            cmsTemplate::getInstance()->renderJSON($result);
            $this->halt();
        }

		$preset = array('width' => 600, 'height'=>460, 'is_square'=>false, 'is_watermark'=>false);

		if (!empty($this->options['preset'])){
			$preset = cmsCore::getModel('images')->getPresetByName($this->options['preset']);
		}

        $result['paths'] = array(
            'big' => $uploader->resizeImage($result['path'], array('width'=>$preset['width'], 'height'=>$preset['height'], 'square'=>$preset['is_square'], 'quality'=>(($preset['is_watermark'] && !empty($preset['wm_image'])) ? 100 : $preset['quality']))),
            'normal' => $uploader->resizeImage($result['path'], array('width'=>160, 'height'=>160, 'square'=>true)),
            'small' => $uploader->resizeImage($result['path'], array('width'=>64, 'height'=>64, 'square'=>true)),
			'original' => $result['url']
        );

		if ($preset['is_watermark'] && !empty($preset['wm_image'])){
			img_add_watermark(
					$result['paths']['big'],
					$preset['wm_image']['original'],
					$preset['wm_origin'],
					$preset['wm_margin'],
					$preset['quality']
			);
		}

        $result['filename'] = basename($result['path']);

		if (empty($this->options['is_origs'])){
			@unlink($result['path']);
			unset($result['paths']['original']);
		}

        unset($result['path']);

        $result['url'] = $config->upload_host . '/' . $result['paths']['small'];

        $result['id'] = $this->model->addPhoto($album_id, $result['paths']);

        cmsTemplate::getInstance()->renderJSON($result);
        $this->halt();

    }

}