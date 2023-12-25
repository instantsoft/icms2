<?php
class geo extends cmsFrontend {

    protected $useOptions = true;

    public function actionWidget($field_id, $city_id = false){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $countries = $this->model->getCountries();
        $countries = array('0'=>LANG_GEO_SELECT_COUNTRY) + $countries;

        $regions = array();
        $cities = array();

        $region_id = false;
        $country_id = false;

        if (!$city_id){

            $geo = $this->getGeoByIp();

            if($geo){

                if(!empty($geo['city']['id'])){
                    $city_id = $geo['city']['id'];
                }
                if(!empty($geo['city']['region_id'])){
                    $region_id = $geo['city']['region_id'];
                }
                if(!empty($geo['city']['country_id'])){
                    $country_id = $geo['city']['country_id'];
                }
                if(!empty($geo['region']['id']) && !$region_id){
                    $region_id = $geo['region']['id'];
                }
                if(!empty($geo['country']['id']) && !$country_id){
                    $country_id = $geo['country']['id'];
                }

            }

            if(!$country_id && !empty($this->options['default_country_id'])){
                $country_id = $this->options['default_country_id'];
            }

        }

        if ($city_id){

            if(!$region_id || !$country_id){

                $city_parents = $this->model->getCityParents($city_id);

                $region_id = $region_id ? $region_id : $city_parents['region_id'];
                $country_id = $country_id ? $country_id : $city_parents['country_id'];

            }

            $regions = $this->model->getRegions($country_id);
            $regions = array('0'=>LANG_GEO_SELECT_REGION) + $regions;

            $cities = $this->model->getCities($region_id);
            $cities = array('0'=>LANG_GEO_SELECT_CITY) + $cities;

        }

        $this->cms_template->render('widget', array(
            'field_id'   => $field_id,
            'city_id'    => $city_id,
            'country_id' => $country_id,
            'region_id'  => $region_id,
            'countries'  => $countries,
            'regions'    => $regions,
            'cities'     => $cities
        ));

    }

    public function actionGetItems() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $type      = $this->request->get('type', '');
        $parent_id = $this->request->get('parent_id', 0);

        if (!$type || !in_array($type, ['regions', 'cities'])) {
            return cmsCore::error404();
        }

        if (!$parent_id) {
            return cmsCore::error404();
        }

        $items = []; $data = [];

        switch ($type) {

            case 'regions':
                $items = $this->model->getRegions($parent_id);
                $select_text = LANG_GEO_SELECT_REGION;
                break;

            case 'cities':
                $items = $this->model->getCities($parent_id);
                $select_text = LANG_GEO_SELECT_CITY;
                break;
        }

        if ($items) {
            $items = ['' => $select_text] + $items;
        }

        foreach ($items as $id => $name) {
            $data[] = [
                'id'   => $id,
                'name' => $name,
            ];
        }

        return $this->cms_template->renderJSON([
            'error' => $data ? false : true,
            'items' => $data
        ]);
    }

    public function getGeoByIp() {

        if(empty($this->options['auto_detect'])){ return false; }

        $geo = $this->getAutoDetectGeoByIp();

        if(!empty($this->options['default_country_id']) && empty($geo['country']['id']) && empty($geo['city']['country_id'])){
            $geo['country']['id'] = $this->options['default_country_id'];
        }

        return $geo;

    }

    public function getAutoDetectGeoByIp($ip = '') {

        $geo = array(
            'city'    => array(
                'id'   => null,
                'name' => null
            ),
            'region'   => array(
                'id'   => null,
                'name' => null
            ),
            'country' => array(
                'id'   => null,
                'name' => null
            )
        );

        if(empty($this->options['auto_detect_provider'])){ return $geo; }

        if(!$ip){ $ip = cmsUser::getIp(); }

        $cache_key = 'geo_data:'.md5($ip);

        $cached_geo = cmsUser::sessionGet($cache_key);
        if($cached_geo){ return $cached_geo; }

        $geo_class_name = 'icms' . string_to_camel('_', $this->options['auto_detect_provider']);

        if(!cmsCore::includeFile('system/controllers/geo/iplookups/'.$this->options['auto_detect_provider'].'.php')){
            return $geo;
        }

        $data = call_user_func(array($geo_class_name, 'detect'), $ip);

        if(isset($data['country'])){
            $geo['country'] = $this->model->getItemByField('geo_countries', 'alpha2', $data['country']);
        }

        if(isset($data['city'])){

            if(!empty($geo['country']['id'])){
                $this->model->filterEqual('country_id', $geo['country']['id']);
            }

            $geo['city'] = $this->model->getItemByField('geo_cities', 'name', $data['city']);

        }

        cmsUser::sessionSet($cache_key, $geo);

        return $geo;

    }

}
