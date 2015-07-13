<?php

class actionAdminUsersAjax extends cmsAction {

    public function run($group_id=false){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('users');

        $users_model = cmsCore::getModel('users');

        $users_model->setPerPage(admin::perpage);

        $filter     = array();
        $filter_str = $this->request->get('filter');

        if ($filter_str){

            $content_model = cmsCore::getModel('content')->setTablePrefix('');

            parse_str($filter_str, $filter);

            $users_model->applyGridFilter($grid, $filter);

            if (!empty($filter['advanced_filter'])){

                parse_str($filter['advanced_filter'], $dataset_filters);

                $users_model->applyDatasetFilters($dataset_filters);

            }

        }


        if ($group_id){
            $users_model->filterGroup($group_id);
        }

        $total = $users_model->getUsersCount();
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
        $pages = ceil($total / $perpage);

        $users = $users_model->getUsers();

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $users, $total, $pages);

        $this->halt();

    }

}
