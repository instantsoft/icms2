<?php
class images extends cmsFrontend {

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

    }

//============================================================================//
//============================================================================//

	public function uploadWithPreset($name, $preset_name){

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
