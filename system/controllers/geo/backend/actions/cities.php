<?php

class actionGeoCities extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'geo_cities';
        $this->grid_name  = 'cities';

        $region_id  = $params[0] ?? 0;
        $country_id = $params[1] ?? 0;

        $country = $this->model->getItemById('geo_countries', $country_id);
        if (!$country) {
            return cmsCore::error404();
        }

		$region = $this->model->getItemById('geo_regions', $region_id);
        if (!$region) {
            return cmsCore::error404();
        }

        $this->list_callback = function ($model) use ($region_id) {

            $model->filterEqual('region_id', $region_id);

            return $model;
        };

        $this->cms_template->setPageH1([$country['name'], $region['name']]);

        $this->cms_template->addBreadcrumb($country['name'], $this->cms_template->href_to('regions', $country['id']));
        $this->cms_template->addBreadcrumb($region['name']);

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_GEO_ADD_CITY,
                'href'  => $this->cms_template->href_to('city', [0, $region['id']])
            ]
        ];
    }

}
