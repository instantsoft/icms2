<?php

class actionUsersMigrationsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('migrations');

        $users_model = cmsCore::getModel('users');

        $users_model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        if ($filter_str){
            parse_str($filter_str, $filter);
            $users_model->applyGridFilter($grid, $filter);
        }

        $total = $users_model->getMigrationRulesCount();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $rules = $users_model->getMigrationRules();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $rules, $total, $pages);

        $this->halt();

    }

}
