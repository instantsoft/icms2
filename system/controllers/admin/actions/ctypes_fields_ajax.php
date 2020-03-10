<?php

class actionAdminCtypesFieldsAjax extends cmsAction {

    public function run($ctype_name){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_fields', $ctype['name']);

        $this->model_content->orderBy('ordering', 'asc');

        $fields = $this->model_content->getContentFields($ctype['name'], false, false);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $this->cms_template->renderGridRowsJSON($grid, $fields);

        $this->halt();

    }

}
