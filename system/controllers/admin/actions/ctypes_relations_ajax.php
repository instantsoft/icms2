<?php

class actionAdminCtypesRelationsAjax extends cmsAction {

    public function run($ctype_id){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$ctype_id) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_relations');

        $content_model = cmsCore::getModel('content');

        $datasets = $content_model->getContentRelations($ctype_id);

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $datasets);

        $this->halt();

    }

}
