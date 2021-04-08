<?php

class actionAdminCtypesPropsAjax extends cmsAction {

    public function run($ctype_name, $category_id = false) {

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$ctype_name) { cmsCore::error404(); }

        $ctype = $this->model_backend_content->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_props', $this->cms_template->href_to('ctypes', ['props_reorder', $ctype['name']]));

        $this->model_backend_content->orderBy('ordering', 'asc');

        $fields = $this->model_backend_content->getContentPropsBinds($ctype['name'], $category_id);

        $this->cms_template->renderGridRowsJSON($grid, $fields);

        $this->halt();
    }

}
