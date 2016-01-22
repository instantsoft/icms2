<?php

class actionImagesPresetsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('presets');

        $presets = $this->model->getPresets();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $presets);

        $this->halt();

    }

}
