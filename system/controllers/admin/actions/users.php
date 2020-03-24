<?php

class actionAdminUsers extends cmsAction {

    public function run($do = false){

        // если нужно, передаем управление другому экшену
        if ($do && !is_numeric($do)){
            $this->runExternalAction('users_'.$do, array_slice($this->params, 1));
            return;
        }

        $groups = $this->model_users->getGroups();
        $groups = array_pad($groups, (sizeof($groups)+1)*-1, array('id' => 0, 'title' => LANG_ALL));

        $grid = $this->loadDataGrid('users', false, 'admin.grid_filter.users');

        if ($this->request->isAjax()) {

            $this->model_users->setPerPage(admin::perpage);

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            $filter_str = cmsUser::getUPSActual('admin.grid_filter.users', $filter_str);

            if ($filter_str){

                $this->model_users->setTablePrefix('');

                parse_str($filter_str, $filter);

                $this->model_users->applyGridFilter($grid, $filter);

                if (!empty($filter['advanced_filter'])){

                    parse_str($filter['advanced_filter'], $dataset_filters);

                    $this->model_users->applyDatasetFilters($dataset_filters);

                }

            }

            // тут id группы
            if ($do){
                $this->model_users->filterGroup($do);
            }

            $this->model_users->disableDeleteFilter();

            $total = $this->model_users->getUsersCount();
            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages = ceil($total / $perpage);

            $users = $this->model_users->getUsers();

            $this->cms_template->renderGridRowsJSON($grid, $users, $total, $pages);

            $this->halt();

        }

        return $this->cms_template->render('users', array(
            'groups' => $groups,
            'grid'   => $grid
        ));

    }

}
