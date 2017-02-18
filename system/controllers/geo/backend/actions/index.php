<?php

class actionGeoIndex extends cmsAction {

    public function run(){

		$grid = $this->loadDataGrid('countries');

        if($this->request->isAjax()){

            $this->model->setPerPage(admin::perpage);

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $total   = $this->model->getCount('geo_countries');
            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages   = ceil($total / $perpage);

            $countries = $this->model->get('geo_countries');

            $this->cms_template->renderGridRowsJSON($grid, $countries, $total, $pages);

            $this->halt();

        }

        return $this->cms_template->render('backend/countries', array('grid' => $grid));

    }
}
