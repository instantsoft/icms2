<?php
/**
 * @property \modelImages $model
 * @property \modelFiles $model_files
 */
class actionImagesUpload extends cmsAction {

    public function run($name) {

        if (!$this->cms_user->is_logged) {

            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => 'auth error'
            ]);
        }

        $imgs_limit = (int) cmsUser::getPermissionValue('images', 'rate_limit');

        $wait = $this->model->filesCheckUploadRateLimit($this->cms_user->id, $imgs_limit);

        if ($wait > 0) {

            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => sprintf(LANG_IMAGES_LIMIT_ERROR, $wait)
            ]);
        }

        // Переданные пресеты
        $sizes = $this->request->get('sizes', '');
        // Желаемое имя файла
        $file_name = str_replace('.', '', $this->request->get('file_name', ''));

        // Проверяем размеры
        if ($sizes !== '') {

            if (!preg_match('/([a-z0-9_,]+)$/i', $sizes) || mb_strlen($sizes) < 3) {
                return $this->cms_template->renderJSON([
                    'success' => false,
                    'error'   => 'Sizes error'
                ]);
            }

            $sizes = explode(',', $sizes);

        } else {
            $sizes = [];
        }

        // Получаем пресеты по переданным размерам
        if ($sizes) {
            $this->model->filterIn('name', $sizes);
        }

        $presets = $this->model->orderByList([
            ['by' => 'is_square', 'to' => 'asc'],
            ['by' => 'width', 'to' => 'desc']
        ])->filterIsNull('is_internal')->getPresets();

        if (!$presets) {
            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => 'No presets'
            ]);
        }
        // Должно совпадать кол-во
        if ($sizes && count($sizes) !== count($presets)) {
            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => 'Error Sizes Presets'
            ]);
        }

        // Разрешаем загрузку по ссылке
        // устанавливаем разрешенные типы изображений
        $this->cms_uploader->enableRemoteUpload()->setAllowedMime($this->allowed_mime);

        cmsEventsManager::hook('images_before_upload', [$name, $this->cms_uploader], null, $this->request);

        // Непосредственно загружаем
        $result = $this->cms_uploader->upload($name);

        // Начинаем работу с изображением
        if ($result['success']) {

            try {
                $image = new cmsImages($result['path']);
            } catch (Exception $exc) {
                $result['success'] = false;
                $result['error']   = $exc->getMessage();
            }
        }

        // Не получилось, удаляем исходник, показываем ошибку
        if (!$result['success']) {

            if (!empty($result['path'])) {
                files_delete_file($result['path'], 2);
            }

            return $this->cms_template->renderJSON($result);
        }

        // Результирующий массив изображений после конвертации
        $result['paths'] = [];

        list($result, $presets, $sizes) = cmsEventsManager::hook('images_after_upload', [$result, $presets, $sizes], null, $this->request);

        $file_context = [
            'target_controller' => $this->request->get('target_controller', ''),
            'target_subject'    => $this->request->get('target_subject', ''),
            'target_id'         => $this->request->get('target_id', 0) ?: null
        ];

        $last_error = null;

        // Создаём изображения по пресетам
        // Все пресеты должны отработать
        foreach ($presets as $p) {

            $resized_path = $image->resizeByPreset($p, $file_name);
            if (!$resized_path) {

                $last_error = $image->getLastError();

                continue;
            }

            $result['paths'][$p['name']] = [
                'path' => $resized_path,
                'url'  => $this->cms_config->upload_host . '/' . $resized_path
            ];

            if ($file_context['target_controller']) {
                $this->registerUploadFile($file_context);
            }

            $this->registerFile(['path' => $resized_path]);
        }

        // Какой-то пресет не отработал, удаляем все, показываем ошибку
        if (count($result['paths']) !== count($presets)) {

            files_delete_file($result['path'], 2);

            foreach ($result['paths'] as $paths) {
                $this->model_files->deleteFile($paths['path']);
            }

            error_log($last_error ?? 'Conversion to one or more presets failed');

            return $this->cms_template->renderJSON([
                'success' => false,
                'error'   => 'Conversion to one or more presets failed'
            ]);
        }

        list($result, $presets, $sizes) = cmsEventsManager::hook('images_after_resize', [$result, $presets, $sizes], null, $this->request);

        // Удаляем исходник, для оригинального файла есть отдельный пресет
        files_delete_file($result['path'], 2);
        unset($result['path']);

        if ($this->request->isInternal()) {
            return $result;
        }

        return $this->cms_template->renderJSON($result);
    }
}
