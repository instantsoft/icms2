<?php

class actionGeoWidget extends cmsAction {

    public function run($field_id, $city_id = false) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $countries = ['0' => LANG_GEO_SELECT_COUNTRY] + $this->model->getCountries();

        $regions = [];
        $cities  = [];

        $region_id  = false;
        $country_id = false;

        if (!$city_id) {

            $geo = $this->getGeoByIp();

            if ($geo) {

                if (!empty($geo['city']['id'])) {
                    $city_id = $geo['city']['id'];
                }
                if (!empty($geo['city']['region_id'])) {
                    $region_id = $geo['city']['region_id'];
                }
                if (!empty($geo['city']['country_id'])) {
                    $country_id = $geo['city']['country_id'];
                }
                if (!empty($geo['region']['id']) && !$region_id) {
                    $region_id = $geo['region']['id'];
                }
                if (!empty($geo['country']['id']) && !$country_id) {
                    $country_id = $geo['country']['id'];
                }
            }

            if (!$country_id && !empty($this->options['default_country_id'])) {
                $country_id = $this->options['default_country_id'];
            }
        }

        if ($city_id) {

            if (!$region_id || !$country_id) {

                $city_parents = $this->model->getCityParents($city_id);

                $region_id  = $region_id ? $region_id : $city_parents['region_id'];
                $country_id = $country_id ? $country_id : $city_parents['country_id'];
            }

            $regions = ['0' => LANG_GEO_SELECT_REGION] + $this->model->getRegions($country_id);

            $cities = ['0' => LANG_GEO_SELECT_CITY] + $this->model->getCities($region_id);
        }

        return $this->cms_template->render('widget', [
            'field_id'   => $field_id,
            'city_id'    => $city_id,
            'country_id' => $country_id,
            'region_id'  => $region_id,
            'countries'  => $countries,
            'regions'    => $regions,
            'cities'     => $cities
        ]);
    }

}
