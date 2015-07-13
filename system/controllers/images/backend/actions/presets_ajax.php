<?php

class actionImagesPresetsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('presets');

        $images_model = cmsCore::getModel('images');

        $presets = $images_model->getPresets();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $presets);

        $this->halt();

    }

}
