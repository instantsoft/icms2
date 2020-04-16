<?php
class images extends cmsFrontend {

<<<<<<< HEAD
	private $allowed_extensions = 'jpg,jpeg,png,gif,bmp';
=======
    public $request_params = array(
        'target_controller' => array(
            'default' => '',
            'rules'   => array(
                array('sysname'),
                array('max_length', 32)
            )
        ),
        'target_subject' => array(
            'default' => '',
            'rules'   => array(
                array('regexp', "/^([a-z0-9\-_\/\.]*)$/"),
                array('max_length', 32)
            )
        ),
        'target_id' => array(
            'default' => 0,
            'rules'   => array(
                array('digits')
            )
        )
    );

	private $allowed_extensions = 'jpg,jpeg,png,gif,bmp,webp';

    public $allowed_mime = array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    );

	private $file_context = null;
>>>>>>> origin/master

//============================================================================//
//============================================================================//

    public function getSingleUploadWidget($name, $paths = false, $sizes = false, $allow_import_link = false){

        $is_image_exists = !empty($paths);

        $dom_id = str_replace(array('[',']'), array('_l_', '_r_'), $name);

        $upload_url = href_to('images', 'upload', $dom_id);
        if (is_array($sizes)) {
            $upload_url .= '?sizes=' . implode(',', $sizes);
        }

        return $this->cms_template->renderInternal($this, 'upload_single', array(
			'name'              => $name,
            'paths'             => $paths,
            'sizes'             => $sizes,
            'upload_url'        => $upload_url,
            'dom_id'            => $dom_id,
            'is_image_exists'   => $is_image_exists,
            'allow_import_link' => $allow_import_link
        ));

    }

    public function getMultiUploadWidget($name, $images = false, $sizes = false, $allow_import_link = false, $max_photos = 0){

        $dom_id = str_replace(array('[',']'), array('_l_', '_r_'), $name);

        $upload_url = href_to('images', 'upload', $dom_id);
        if (is_array($sizes)) {
            $upload_url .= '?sizes=' . implode(',', $sizes);
        }

        return $this->cms_template->renderInternal($this, 'upload_multi', array(
            'name'              => $name,
            'images'            => $images,
            'sizes'             => $sizes,
            'upload_url'        => $upload_url,
            'dom_id'            => $dom_id,
            'max_photos'        => (int)$max_photos,
            'allow_import_link' => $allow_import_link
        ));
<<<<<<< HEAD

    }

//============================================================================//
//============================================================================//

    public function actionUpload($name){

        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        $result = $uploader->upload($name, $this->allowed_extensions);

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

		$sizes = $this->request->get('sizes');

		if (!empty($sizes) && preg_match('/([a-zA-Z0-9_,]+)/i', $sizes)){
			$sizes = explode(',', $sizes);
		}

		$is_store_original = !is_array($sizes) || in_array('original', $sizes);

        $result['paths'] = array();

		if ($is_store_original){
			$result['paths']['original'] = array(
				'path' => $result['url'],
				'url' => $config->upload_host . '/' . $result['url']
			);
		}

		$presets = $this->model->getPresets();

		foreach($presets as $p){

			if (is_array($sizes) && !in_array($p['name'], $sizes)){
				continue;
			}

			$path = $uploader->resizeImage($result['path'], array(
				'width'=>$p['width'],
				'height'=>$p['height'],
				'square'=>$p['is_square'],
				'quality'=>(($p['is_watermark'] && $p['wm_image']) ? 100 : $p['quality']) // потом уже при наложении ватермарка будет правильное качество
			));

			if (!$path) { continue; }

			$image = array(
				'path' => $path,
				'url' => $config->upload_host . '/' . $path
			);

			if ($p['is_watermark'] && $p['wm_image']){
				img_add_watermark($image['path'], $p['wm_image']['original'], $p['wm_origin'], $p['wm_margin'], $p['quality']);
			}

			$result['paths'][$p['name']] = $image;

		}

		if (!$is_store_original){
			unlink($result['path']);
		}

        unset($result['path']);

        cmsTemplate::getInstance()->renderJSON($result);
        $this->halt();
=======
>>>>>>> origin/master

    }

//============================================================================//
//============================================================================//

	public function uploadWithPreset($name, $preset_name){

<<<<<<< HEAD
        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        $result = $uploader->upload($name, $this->allowed_extensions);

        if ($result['success']){
            if (!$uploader->isImage($result['path'])){
                $result['success'] = false;
                $result['error'] = LANG_UPLOAD_ERR_MIME;
            }
        }

        if (!$result['success']){
            if(!empty($result['path'])){
                $uploader->remove($result['path']);
            }
            return $result;
        }

=======
>>>>>>> origin/master
		$preset = $this->model->getPresetByName($preset_name);

		if (!$preset){
			return array(
				'success' => false,
                'error'   => ''
            );
		}

        $this->cms_uploader->enableRemoteUpload()->setAllowedMime($this->allowed_mime);

        cmsEventsManager::hook('images_before_upload_by_preset', array($name, $this->cms_uploader, $preset), null, $this->request);

        $result = $this->cms_uploader->upload($name);

        if ($result['success']){

<<<<<<< HEAD
		@unlink($result['path']);
=======
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
            return $result;
        }

        list($result, $preset) = cmsEventsManager::hook('images_after_upload_by_preset', array($result, $preset), null, $this->request);

        $resized_path = $image->resizeByPreset($preset);

        if (!$resized_path) {
			return array(
				'success' => false,
                'error'   => ''
            );
        }

        $result['image'] = [
			'path' => $resized_path,
            'url'  => $this->cms_config->upload_host . '/' . $resized_path
        ];

		$result['location'] = $result['image']['url'];

        list($result, $preset) = cmsEventsManager::hook('images_after_resize_by_preset', array($result, $preset), null, $this->request);

		files_delete_file($result['path'], 2);
>>>>>>> origin/master
        unset($result['path']);

        $file_context = array(
            'target_controller' => $this->request->get('target_controller'),
            'target_subject' => $this->request->get('target_subject'),
            'target_id' => $this->request->get('target_id')
        );

        if($file_context['target_controller']){
            $this->registerUploadFile($file_context);
        }

        $this->registerFile($result['image']);

        unset($result['error']);

        return $result;

	}

	public function getAllowedExtensions(){
		return $this->allowed_extensions;
	}

	public function setAllowedExtensions($exts){
        if(is_array($exts)){
            $this->allowed_extensions = implode(',', $exts);
        } else {
            $this->allowed_extensions = $exts;
        }
		return $this;
	}

<<<<<<< HEAD
}
=======
	public function registerUploadFile($file_context){
        $this->file_context = $file_context; return $this;
	}

	private function registerFile($image){

        if($this->file_context === null){ return false; }

        $file_id = cmsCore::getModel('files')->registerFile(array_merge($this->file_context, array(
            'path'    => $image['path'],
            'type'    => 'image',
            'name'    => pathinfo($image['path'], PATHINFO_BASENAME),
            'user_id' => cmsUser::get('id')
        )));

        $this->file_context = null;

        return $file_id;

	}

    /**
     * Этот метод устаревший, используйте класс cmsImages
     */
	public function addWatermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality=90){
        // функция img_add_watermark также устаревшая
		return img_add_watermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality);
	}

}
>>>>>>> origin/master
