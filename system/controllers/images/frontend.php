<?php

class images extends cmsFrontend {

    public $request_params = [
        'target_controller' => [
            'default' => '',
            'rules'   => [
                ['sysname'],
                ['max_length', 32]
            ]
        ],
        'target_subject' => [
            'default' => '',
            'rules'   => [
                ['regexp', "/^([a-z0-9\-_\/\.]*)$/"],
                ['max_length', 32]
            ]
        ],
        'target_id' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ]
    ];

    private $allowed_extensions = 'jpg,jpeg,png,gif,bmp,webp';

    public $allowed_mime = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    private $file_context = null;

    public function getSingleUploadWidget($name, $paths = false, $params = []) {

        $is_image_exists = !empty($paths);

        $dom_id = !empty($params['id']) ? $params['id'] : 'img-' . uniqid();

        $upload_url = href_to('images', 'upload', $dom_id);

        $upload_params = $this->getContextParams();

        if (!empty($params['sizes'])) {
            $upload_params['sizes'] = implode(',', $params['sizes']);
        }

        if ($upload_params) {
            $upload_url .= '?' . http_build_query($upload_params);
        }

        $preview_url = '';
        if ($is_image_exists) {
            $preview_url = $this->cms_config->upload_host . '/' . ($paths['small'] ?? reset($paths));
        }

        return $this->cms_template->renderInternal($this, 'upload_single', [
            'allowed_mime'          => $this->allowed_mime,
            'name'                  => $name,
            'paths'                 => $paths,
            'upload_url'            => $upload_url,
            'delete_url'            => href_to('images', 'delete'),
            'dom_id'                => $dom_id,
            'preview_url'           => $preview_url,
            'is_image_exists'       => $is_image_exists,
            'allow_image_cropper'   => ($params['allow_image_cropper'] ?? false),
            'image_cropper_rounded' => ($params['image_cropper_rounded'] ?? false),
            'image_cropper_ratio'   => ($params['image_cropper_ratio'] ?? 1),
            'allow_import_link'     => ($params['allow_import_link'] ?? false)
        ]);
    }

    public function getMultiUploadWidget($name, $images = false, $params = []) {

        $dom_id = !empty($params['id']) ? $params['id'] : 'img-' . uniqid();

        $upload_url = href_to('images', 'upload', $dom_id);

        $upload_params = $this->getContextParams();

        if (!empty($params['sizes'])) {
            $upload_params['sizes'] = implode(',', $params['sizes']);
        }

        if ($upload_params) {
            $upload_url .= '?' . http_build_query($upload_params);
        }

        return $this->cms_template->renderInternal($this, 'upload_multi', [
            'allowed_mime'      => $this->allowed_mime,
            'name'              => $name,
            'images'            => $images,
            'upload_url'        => $upload_url,
            'delete_url'        => href_to('images', 'delete'),
            'dom_id'            => $dom_id,
            'max_photos'        => !empty($params['max_photos']) ? intval($params['max_photos']) : 0,
            'allow_import_link' => !empty($params['allow_import_link']) ? true : false
        ]);
    }

//============================================================================//
//============================================================================//

    public function uploadWithPreset($name, $preset_name) {

        $preset = $this->model->getPresetByName($preset_name);

        if (!$preset) {
            return [
                'success' => false,
                'error'   => ''
            ];
        }

        $this->cms_uploader->enableRemoteUpload()->setAllowedMime($this->allowed_mime);

        cmsEventsManager::hook('images_before_upload_by_preset', [$name, $this->cms_uploader, $preset], null, $this->request);

        $result = $this->cms_uploader->upload($name);

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
            return $result;
        }

        list($result, $preset) = cmsEventsManager::hook('images_after_upload_by_preset', [$result, $preset], null, $this->request);

        $resized_path = $image->resizeByPreset($preset);

        if (!$resized_path) {
            return [
                'success' => false,
                'error'   => ''
            ];
        }

        $result['image'] = [
            'path' => $resized_path,
            'url'  => $this->cms_config->upload_host . '/' . $resized_path
        ];

        $result['location'] = $result['image']['url'];

        list($result, $preset) = cmsEventsManager::hook('images_after_resize_by_preset', [$result, $preset], null, $this->request);

        files_delete_file($result['path'], 2);
        unset($result['path']);

        $file_context = [
            'target_controller' => $this->request->get('target_controller', ''),
            'target_subject'    => $this->request->get('target_subject', ''),
            'target_id'         => $this->request->get('target_id', 0)
        ];

        if ($file_context['target_controller']) {
            $this->registerUploadFile($file_context);
        }

        $this->registerFile($result['image']);

        unset($result['error']);

        return $result;
    }

    public function getAllowedExtensions() {
        return $this->allowed_extensions;
    }

    public function setAllowedExtensions($exts) {
        if (is_array($exts)) {
            $this->allowed_extensions = implode(',', $exts);
        } else {
            $this->allowed_extensions = $exts;
        }
        return $this;
    }

    public function registerUploadFile($file_context) {
        $this->file_context = $file_context;
        return $this;
    }

    public function registerFile($image) {

        if ($this->file_context === null) {
            return false;
        }

        $file_id = $this->model_files->registerFile(array_merge($this->file_context, [
            'path'    => $image['path'],
            'type'    => 'image',
            'name'    => pathinfo($image['path'], PATHINFO_BASENAME),
            'user_id' => $this->cms_user->id
        ]));

        $this->file_context = null;

        return $file_id;
    }

    private function getContextParams() {

        $internal_context = [
            'target_controller' => $this->request->get('target_controller', ''),
            'target_subject'    => $this->request->get('target_subject', ''),
            'target_id'         => $this->request->get('target_id', 0)
        ];

        if ($internal_context['target_controller']) {
            return $internal_context;
        }

        $context = $this->cms_core->getUriData();
        $upload_params = [];

        if ($context['controller']) {
            $upload_params['target_controller'] = $context['controller'];
        }

        if ($context['action']) {
            $upload_params['target_subject'] = mb_substr($context['action'], 0, 32);
        }

        if (strpos($this->cms_core->uri, '/add/') === false && !empty($context['params'][1]) && is_numeric($context['params'][1])) {
            $upload_params['target_id'] = $context['params'][1];
        }

        return $upload_params;
    }

    /**
     * Этот метод устаревший, используйте класс cmsImages
     */
    public function addWatermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality = 90) {
        // функция img_add_watermark также устаревшая
        return img_add_watermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality);
    }

}
