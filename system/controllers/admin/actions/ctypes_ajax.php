<?php

class actionAdminCtypesAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctypes');

        $this->model_content->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('admin.grid_filter.ctypes', $filter_str);

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model_content->applyGridFilter($grid, $filter);
        }

        $total = $this->model_content->getContentTypesCountFiltered();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $ctypes = $this->model_content->getContentTypesFiltered();

        $this->cms_template->renderGridRowsJSON($grid, $ctypes, $total, $pages);

        $this->halt();

    }

}
