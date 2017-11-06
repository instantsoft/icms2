<?php

class actionAdminSettingsSchedulerAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('scheduler');

        $this->model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('admin.grid_filter.set_scheduler', $filter_str);

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($grid, $filter);
        }

        $total = $this->model->getSchedulerTasksCount();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $ctypes = $this->model->getSchedulerTasks();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $ctypes, $total, $pages);

        $this->halt();

    }

}
