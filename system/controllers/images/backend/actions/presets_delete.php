<?php

class actionImagesPresetsDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $images_model = cmsCore::getModel('images');

        $images_model->deletePreset($id);

        $this->redirectToAction('presets');

    }

}
