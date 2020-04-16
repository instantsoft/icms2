<?php

class actionUsersMigrationsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('migrations');

        $this->model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($grid, $filter);
        }

        $total = $this->model->getMigrationRulesCount();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $rules = $this->model->getMigrationRules();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $rules, $total, $pages);

        $this->halt();

    }

}
