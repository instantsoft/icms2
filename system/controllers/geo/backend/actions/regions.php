<?php

class actionGeoRegions extends cmsAction {

    public function run($country_id = null){

		if(!$country_id){ cmsCore::error404(); }

		$country = $this->model->getItemById('geo_countries', $country_id);
		if(!$country){ cmsCore::error404(); }

		$grid = $this->loadDataGrid('regions');

        if($this->request->isAjax()){

            $this->model->setPerPage(admin::perpage);

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $total = $this->model->filterEqual('country_id', $country_id)->getCount('geo_regions');

            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages = ceil($total / $perpage);

            $regions = $this->model->get('geo_regions');

            $this->cms_template->renderGridRowsJSON($grid, $regions, $total, $pages);

            $this->halt();

        }

        $this->cms_template->setPageH1($country['name']);

        return $this->cms_template->render('backend/regions', array(
			'grid'    => $grid,
            'country' => $country
        ));

    }
}
