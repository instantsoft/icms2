<?php

class actionImagesPresetsDelete extends cmsAction {

    public function run($id){

        $images_model = cmsCore::getModel('images');

        $preset = $images_model->getPreset($id);
        if (!$preset) { cmsCore::error404(); }

        $images_model->deletePreset($preset['id']);

        $this->deleteDefaultImages($preset);

        $this->redirectToAction('presets');

    }

}
