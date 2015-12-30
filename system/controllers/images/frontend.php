<?php
class images extends cmsFrontend {

	private $allowed_extensions = 'jpg,jpeg,png,gif,bmp';

//============================================================================//
//============================================================================//

    public function getSingleUploadWidget($name, $paths = false, $sizes = false){

        return cmsTemplate::getInstance()->renderInternal($this, 'upload_single', array(
			'name' => $name,
			'paths' => $paths,
			'sizes' => $sizes
        ));

    }

    public function getMultiUploadWidget($name, $images = false, $sizes = false){

        return cmsTemplate::getInstance()->renderInternal($this, 'upload_multi', array(
			'name' => $name,
			'images' => $images,
			'sizes' => $sizes
        ));

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

    }

//============================================================================//
//============================================================================//

	public function uploadWithPreset($name, $preset_name){

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

		$preset = $this->model->getPresetByName($preset_name);

		if (!$preset){
			return array(
				'success' => false,
				'error' => '',
			);
		}

		$path = $uploader->resizeImage($result['path'], array(
			'width'=>$preset['width'],
			'height'=>$preset['height'],
			'square'=>$preset['is_square'],
			'quality'=>(($preset['is_watermark'] && $preset['wm_image']) ? 100 : $preset['quality'])
		));

		$image = array(
			'path' => $path,
			'url' => $config->upload_host . '/' . $path
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