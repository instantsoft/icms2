<?php

class actionImagesUploadWithPreset extends cmsAction {

    public function run($name, $preset_name){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'error'   => 'auth error'
            ));
        }

        return $this->cms_template->renderJSON($this->uploadWithPreset($name, $preset_name));

    }

}
