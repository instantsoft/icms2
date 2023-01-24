<?php

class actionAdminControllersAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        cmsCore::loadAllControllersLanguages();

        $grid = $this->loadDataGrid('controllers');

        $this->model->setPerPage(admin::perpage);

        $filter     = [];
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('admin.grid_filter.controllers', $filter_str);

        if ($filter_str) {
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($grid, $filter);
        }

        $total = $this->model->getInstalledControllersCount();
        $pages = ceil($total / admin::perpage);

        $controllers = $this->model->getInstalledControllers();

        $this->cms_template->renderGridRowsJSON($grid, $controllers, $total, $pages);

        return $this->halt();
    }

}
