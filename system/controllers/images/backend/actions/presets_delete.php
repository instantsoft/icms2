<?php

class actionImagesPresetsDelete extends cmsAction {

    public function run($id){

        $preset = $this->model->getPreset($id);
        if (!$preset) { cmsCore::error404(); }

        $this->model->deletePreset($preset['id']);

        $this->deleteDefaultImages($preset);

        $this->redirectToAction('presets');

    }

}