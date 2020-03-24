<?php

class actionRssIndex extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('feeds');

        if ($this->request->isAjax()) {

            $this->model->setPerPage(admin::perpage);

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $total = $this->model->getFeedsCount();
            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages = ceil($total / $perpage);

            $feeds = $this->model->getFeeds();

            $this->cms_template->renderGridRowsJSON($grid, $feeds, $total, $pages);

            $this->halt();

        }

        return $this->cms_template->render('backend/index', array(
            'grid' => $grid
        ));

    }

}
