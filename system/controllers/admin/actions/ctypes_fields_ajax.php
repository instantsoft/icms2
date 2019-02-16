<?php

class actionAdminCtypesFieldsAjax extends cmsAction {

    public function run($ctype_name){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$ctype_name) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_fields');

        $this->model_content->orderBy('ordering', 'asc');

        $fields = $this->model_content->getContentFields($ctype_name, false, false);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $this->cms_template->renderGridRowsJSON($grid, $fields);

        $this->halt();

    }

}
