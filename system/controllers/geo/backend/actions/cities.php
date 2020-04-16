<?php

class actionGeoCities extends cmsAction {

    public function run($region_id = null, $country_id = null){

		if(!$region_id){ cmsCore::error404(); }

		$region = $this->model->getItemById('geo_regions', $region_id);
		if(!$region){ cmsCore::error404(); }

		$grid = $this->loadDataGrid('cities');

        if($this->request->isAjax()){

            $this->model->setPerPage(admin::perpage);

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $total = $this->model->filterEqual('region_id', $region_id)->getCount('geo_cities');
            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages   = ceil($total / $perpage);

            $cities = $this->model->get('geo_cities');

            $this->cms_template->renderGridRowsJSON($grid, $cities, $total, $pages);

            $this->halt();

        }

		$country = $this->model->getItemById('geo_countries', $country_id);
		if(!$country){ cmsCore::error404(); }

        $this->cms_template->setPageH1(array($country['name'], $region['name']));

        return $this->cms_template->render('backend/cities', array(
			'grid'    => $grid,
            'country' => $country,
            'region'  => $region
        ));

    }

}
