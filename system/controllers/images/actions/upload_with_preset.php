<?php

class actionImagesUploadWithPreset extends cmsAction {

    public function __construct($controller, $params = []){

        parent::__construct($controller, $params);

        $this->request->setContext(cmsRequest::CTX_AJAX);
    }

    public function run($name, $preset_name){

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

        return $this->cms_template->renderJSON($this->uploadWithPreset($name, $preset_name));
    }

}
