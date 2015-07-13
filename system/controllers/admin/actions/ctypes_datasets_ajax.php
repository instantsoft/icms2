<?php

class actionAdminCtypesDatasetsAjax extends cmsAction {

    public function run($ctype_id){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$ctype_id) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_datasets');

        $content_model = cmsCore::getModel('content');

        $content_model->orderBy('ordering', 'asc');

        $datasets = $content_model->getContentDatasets($ctype_id);

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $datasets);

        $this->halt();

    }

}
