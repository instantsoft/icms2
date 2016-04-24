<?php

class actionAdminControllersAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        cmsCore::loadAllControllersLanguages();

        $grid = $this->loadDataGrid('controllers');

        $this->model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('admin.grid_filter.controllers', $filter_str);

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($grid, $filter);
        }

        $total = $this->model->getInstalledControllersCount();
        $pages = ceil($total / admin::perpage);

        $controllers = $this->model->getInstalledControllers();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $controllers, $total, $pages);

        $this->halt();

    }

}