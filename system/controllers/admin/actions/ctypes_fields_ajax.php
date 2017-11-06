<?php

class actionAdminCtypesFieldsAjax extends cmsAction {

    public function run($ctype_name){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$ctype_name) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_fields');

        $content_model = cmsCore::getModel('content');

        $content_model->orderBy('ordering', 'asc');

        $fields = $content_model->getContentFields($ctype_name);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $this->cms_template->renderGridRowsJSON($grid, $fields);

        $this->halt();

    }

}
