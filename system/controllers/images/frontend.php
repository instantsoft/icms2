<?php
class images extends cmsFrontend {

	private $allowed_extensions = "jpg,jpeg,png,gif,bmp,swf";
	
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
                $result['error'] = LANG_UPLOAD_ERR_MIME;
            }
        }

        if (!$result['success']){
			$uploader->remove($result['path']);
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
				'square'=>$p['is_square']
			));
			
			if (!$path) { continue; }
			
			$image = array(
				'path' => $path,
				'url' => $config->upload_host . '/' . $path
			);
			
			if ($p['is_watermark'] && $p['wm_image']){
				$this->addWatermark($image['path'], $p['wm_image']['original'], $p['wm_origin'], $p['wm_margin']);
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
			$uploader->remove($result['path']);
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
			'square'=>$preset['is_square']
		));

		$image = array(
			'path' => $path,
			'url' => $config->upload_host . '/' . $path
		);

		if ($preset['is_watermark'] && $preset['wm_image']){
			$this->addWatermark($image['path'], $preset['wm_image']['original'], $preset['wm_origin'], $preset['wm_margin']);
		}

		$result['image'] = $image;
				
		unlink($result['path']);
        unset($result['path']);

        return $result;
		
	}
	
	public function addWatermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality=100){
		
		$config = cmsConfig::getInstance();
		
		$src_file = $config->upload_path . $src_file;
		$wm_file = $config->upload_path . $wm_file;
		
		$img_size = getimagesize($src_file);
		if ($img_size === false) { return false; }
        $img_width = $img_size[0]; $img_height = $img_size[1];
        $img = imagecreatefromjpeg($src_file);
		
		$wm_size = getimagesize($wm_file);
		if ($wm_size === false) { return false; }
        $wm_width = $wm_size[0]; $wm_height = $wm_size[1];
        $wm_format = strtolower(substr($wm_size['mime'], strpos($wm_size['mime'], '/') + 1));
        $wm_func = "imagecreatefrom" . $wm_format;
		if (!function_exists($wm_func)) { return false; }
        $wm = $wm_func($wm_file);
		
		if (!$wm_margin) { $wm_margin = 0; }
		
		$x = 0; $y = 0;
		
		switch($wm_origin){
			case 'top-left': 
				$x = $wm_margin; 
				$y = $wm_margin; 
				break;
			case 'top': 
				$x = ($img_width/2) - ($wm_width/2); 
				$y = $wm_margin; 
				break;
			case 'top-right': 
				$x = ($img_width - $wm_width - $wm_margin); 
				$y = $wm_margin; 
				break;
			case 'left': 
				$x = $wm_margin; 
				$y = ($img_height/2) - ($wm_height/2); 
				break;
			case 'center':
				$x = ($img_width/2) - ($wm_width/2); 
				$y = ($img_height/2) - ($wm_height/2); 
				break;
			case 'right': 
				$x = ($img_width - $wm_width - $wm_margin); 
				$y = ($img_height/2) - ($wm_height/2); 
				break;
			case 'bottom-left':
				$x = $wm_margin;
				$y = ($img_height - $wm_height - $wm_margin);
				break;
			case 'bottom':
				$x = ($img_width/2) - ($wm_width/2); 
				$y = ($img_height - $wm_height - $wm_margin);
				break;
			case 'bottom-right':
				$x = ($img_width - $wm_width - $wm_margin); 
				$y = ($img_height - $wm_height - $wm_margin);
				break;
		}
		
		imagecopy($img, $wm, $x, $y, 0, 0, $wm_width, $wm_height);
		
		imageinterlace($img, 1);

        imagejpeg($img, $src_file, $quality);

        imagedestroy($img);
        imagedestroy($wm);
		
	}

	public function getAllowedExtensions(){
		return $this->allowed_extensions;
	}
	
}
