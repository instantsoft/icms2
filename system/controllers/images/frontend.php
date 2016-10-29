<?php
class images extends cmsFrontend {

	private $allowed_extensions = 'jpg,jpeg,png,gif,bmp';

//============================================================================//
//============================================================================//

    public function getSingleUploadWidget($name, $paths = false, $sizes = false, $allow_import_link = false){

        return $this->cms_template->renderInternal($this, 'upload_single', array(
			'name'              => $name,
            'paths'             => $paths,
            'sizes'             => $sizes,
            'allow_import_link' => $allow_import_link
        ));

    }

    public function getMultiUploadWidget($name, $images = false, $sizes = false, $allow_import_link = false, $max_photos = 0){

        return $this->cms_template->renderInternal($this, 'upload_multi', array(
            'name'              => $name,
            'images'            => $images,
            'sizes'             => $sizes,
            'max_photos'        => (int)$max_photos,
            'allow_import_link' => $allow_import_link
        ));

    }

//============================================================================//
//============================================================================//

    public function actionUpload($name){

        if (!cmsUser::isLogged()) {

            return $this->cms_template->renderJSON(array(
                'success' => false,
                'error'   => 'auth error'
            ));

        }

        $result = $this->cms_uploader->enableRemoteUpload()->upload($name, $this->allowed_extensions);

        if ($result['success']){
            if (!$this->cms_uploader->isImage($result['path'])){
                $result['success'] = false;
                $result['error']   = LANG_UPLOAD_ERR_MIME;
            }
        }

        if (!$result['success']){
            if(!empty($result['path'])){
                $this->cms_uploader->remove($result['path']);
            }
            return $this->cms_template->renderJSON($result);
        }

		$sizes = $this->request->get('sizes', '');

		if (!empty($sizes) && preg_match('/([a-z0-9_,]+)$/i', $sizes)){
			$sizes = explode(',', $sizes);
		} else {
            $sizes = array_keys((array)$this->model->getPresetsList());
            $sizes[] = 'original';
        }

        $result['paths'] = array();

		if (in_array('original', $sizes, true)){
			$result['paths']['original'] = array(
				'path' => $result['url'],
                'url'  => $this->cms_config->upload_host . '/' . $result['url']
            );
		}

		$presets = $this->model->orderByList(array(
            array('by' => 'is_square', 'to' => 'asc'),
            array('by' => 'width', 'to' => 'desc')
        ))->getPresets();

		foreach($presets as $p){

			if (!in_array($p['name'], $sizes, true)){
				continue;
			}

			$path = $this->cms_uploader->resizeImage($result['path'], array(
				'width'     => $p['width'],
                'height'    => $p['height'],
                'is_square' => $p['is_square'],
                'quality'   => (($p['is_watermark'] && $p['wm_image']) ? 100 : $p['quality']) // потом уже при наложении ватермарка будет правильное качество
            ));

			if (!$path) { continue; }

			$image = array(
				'path' => $path,
                'url'  => $this->cms_config->upload_host . '/' . $path
            );

			if ($p['is_watermark'] && $p['wm_image']){
				img_add_watermark($image['path'], $p['wm_image']['original'], $p['wm_origin'], $p['wm_margin'], $p['quality']);
			}

			$result['paths'][$p['name']] = $image;

		}

		if (!in_array('original', $sizes, true)){
			unlink($result['path']);
		}

        if ($this->request->isInternal()){
            return $result;
        }

        unset($result['path']);

        return $this->cms_template->renderJSON($result);

    }

//============================================================================//
//============================================================================//

	public function uploadWithPreset($name, $preset_name){

        $result = $this->cms_uploader->enableRemoteUpload()->upload($name, $this->allowed_extensions);

        if ($result['success']){
            if (!$this->cms_uploader->isImage($result['path'])){
                $result['success'] = false;
                $result['error']   = LANG_UPLOAD_ERR_MIME;
            }
        }

        if (!$result['success']){
            if(!empty($result['path'])){
                $this->cms_uploader->remove($result['path']);
            }
            return $result;
        }

		$preset = $this->model->getPresetByName($preset_name);

		if (!$preset){
			return array(
				'success' => false,
                'error'   => ''
            );
		}

		$path = $this->cms_uploader->resizeImage($result['path'], array(
			'width'     => $preset['width'],
            'height'    => $preset['height'],
            'is_square' => $preset['is_square'],
            'quality'   => (($preset['is_watermark'] && $preset['wm_image']) ? 100 : $preset['quality'])
        ));

		$image = array(
			'path' => $path,
            'url'  => $this->cms_config->upload_host . '/' . $path
        );

		if ($preset['is_watermark'] && $preset['wm_image']){
			img_add_watermark($image['path'], $preset['wm_image']['original'], $preset['wm_origin'], $preset['wm_margin'], $preset['quality']);
		}

		$result['image'] = $image;

		@unlink($result['path']);
        unset($result['path']);

        return $result;

	}

    /**
     * Этот метод устаревший, используйте функцию img_add_watermark
     */
	public function addWatermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality=90){
		return img_add_watermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality);
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

}
