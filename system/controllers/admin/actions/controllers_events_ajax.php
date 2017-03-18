<?php

class actionAdminControllersEventsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('controllers_events');

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('admin.grid_filter.events', $filter_str);

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($grid, $filter);
        }

        $events = $this->model->getEvents();

        $this->cms_template->renderGridRowsJSON($grid, $events);

        $this->halt();

    }

}
