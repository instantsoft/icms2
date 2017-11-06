<?php

class actionImagesPresetsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('presets');

        $presets = $this->model->orderByList(array(
            array('by' => 'is_internal', 'to' => 'asc'),
            array('by' => 'width', 'to' => 'asc'),
            array('by' => 'quality', 'to' => 'desc'),
        ))->getPresets();

        return $this->cms_template->renderGridRowsJSON($grid, $presets);

    }

}
