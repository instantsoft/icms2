<?php

class actionGeoIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'geo_countries';
        $this->grid_name  = 'countries';

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_GEO_ADD_COUNTRY,
                'href'  => $this->cms_template->href_to('country')
            ]
        ];
    }

}
