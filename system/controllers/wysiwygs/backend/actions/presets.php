<?php

class actionWysiwygsPresets extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('presets');

        if ($this->request->isAjax()) {

            $presets = $this->model->getPresets();

            return $this->cms_template->renderGridRowsJSON($grid, $presets);

        }

        return $this->cms_template->render('backend/presets', array(
            'grid' => $grid
        ));

    }

}
