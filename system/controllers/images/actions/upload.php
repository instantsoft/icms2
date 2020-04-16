<?php

class actionImagesUpload extends cmsAction {

    public function run($name){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'error'   => 'auth error'
            ));
        }

        // Разрешаем загрузку по ссылке
        // устанавливаем разрешенные типы изображений
        $this->cms_uploader->enableRemoteUpload()->setAllowedMime($this->allowed_mime);

        cmsEventsManager::hook('images_before_upload', array($name, $this->cms_uploader), null, $this->request);

        // Непосредственно загружаем
        $result = $this->cms_uploader->upload($name);

        // Начинаем работу с изображением
        if ($result['success']){

            try {
                $image = new cmsImages($result['path']);
            } catch (Exception $exc) {
                $result['success'] = false;
                $result['error']   = LANG_UPLOAD_ERR_MIME;
            }

        }

        // Не получилось, удаляем исходник, показываем ошибку
        if (!$result['success']){
            if(!empty($result['path'])){
                files_delete_file($result['path'], 2);
            }
            return $this->cms_template->renderJSON($result);
        }

        // Переданные пресеты
		$sizes = $this->request->get('sizes', '');
        // Желаемое имя файла
		$file_name = $this->request->get('file_name', '');

		if (!empty($sizes) && preg_match('/([a-z0-9_,]+)$/i', $sizes)){
			$sizes = explode(',', $sizes);
		} else {
            $sizes = array_keys((array)$this->model->getPresetsList());
            $sizes[] = 'original';
        }

        // Результирующий массив изображений после конвертации
        $result['paths'] = [];

        // Дополняем оригиналом, если нужно
		if (in_array('original', $sizes, true)){
			$result['paths']['original'] = array(
				'path' => $result['url'],
                'url'  => $this->cms_config->upload_host . '/' . $result['url']
            );
		}

        // Получаем пресеты
		$presets = $this->model->orderByList(array(
            ['by' => 'is_square', 'to' => 'asc'],
            ['by' => 'width', 'to' => 'desc']
        ))->getPresets();

        list($result, $presets, $sizes) = cmsEventsManager::hook('images_after_upload', array($result, $presets, $sizes), null, $this->request);

        // Создаём изображения по пресетам
		foreach($presets as $p){

			if (!in_array($p['name'], $sizes, true)){
				continue;
			}

            $resized_path = $image->resizeByPreset($p, $file_name);

            if (!$resized_path) { continue; }

            $result['paths'][$p['name']] = [
				'path' => $resized_path,
                'url'  => $this->cms_config->upload_host . '/' . $resized_path
            ];

		}

        list($result, $presets, $sizes) = cmsEventsManager::hook('images_after_resize', array($result, $presets, $sizes), null, $this->request);

		if (!in_array('original', $sizes, true)){
			files_delete_file($result['path'], 2);
		}

        if ($this->request->isInternal()){
            return $result;
        }

        unset($result['path']);

        return $this->cms_template->renderJSON($result);

    }

}
