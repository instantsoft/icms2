<?php

class actionGeoRegions extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'geo_regions';
        $this->grid_name  = 'regions';

        $country_id = $params[0] ?? 0;

        $country = $this->model->getItemById('geo_countries', $country_id);
        if (!$country) {
            return cmsCore::error404();
        }

        $this->list_callback = function ($model) use ($country_id) {

            $model->filterEqual('country_id', $country_id);

            return $model;
        };

        $this->cms_template->setPageH1($country['name']);

        $this->cms_template->addBreadcrumb($country['name']);

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_GEO_ADD_REGION,
                'href'  => $this->cms_template->href_to('region', $country['id'])
            ]
        ];
    }

}
