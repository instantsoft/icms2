<?php
class geo extends cmsFrontend {

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
            if(!empty($geo['city']['id'])){
                $city_id = $geo['city']['id'];
            }
            if(!empty($geo['city']['region_id'])){
                $region_id = $geo['city']['region_id'];
            }
            if(!empty($geo['city']['country_id'])){
                $country_id = $geo['city']['country_id'];
            }
            if(!empty($geo['country']['id']) && !$country_id){
                $country_id = $geo['country']['id'];
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

    public function actionGetItems(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $type = $this->request->get('type', '');
        $parent_id = $this->request->get('parent_id', 0);

        if (!$type || !in_array($type, array('regions', 'cities'))) { cmsCore::error404(); }
        if (!$parent_id) { cmsCore::error404(); }

        switch ( $type ){

            case 'regions': $items = $this->model->getRegions( $parent_id );
                            $select_text = LANG_GEO_SELECT_REGION;
                            break;

            case 'cities':  $items = $this->model->getCities( $parent_id );
                            $select_text = LANG_GEO_SELECT_CITY;
                            break;

            default: $items = false;

        }

        if (is_array($items)){
            $items = array('0'=>$select_text) + $items;
        }

        foreach ($items as $id => $name){
            $data[] = array(
                'id' => $id,
                'name' => $name,
            );
        }

        return $this->cms_template->renderJSON(array(
           'error' => $data ? false : true,
           'items' => $data
        ));

    }

    public function getGeoByIp() {

        $cached_geo = cmsUser::sessionGet('geo_data');
        if($cached_geo){ return $cached_geo; }

        $out = simplexml_load_string(file_get_contents_from_url('http://ipgeobase.ru:7020/geo?ip='.cmsUser::getIp()));

        $data = array();

        if($out && is_object($out) && !empty($out->ip[0])){
            foreach ($out->ip[0] as $key=>$value) {
                $data[$key] = (string)$value;
            }
        }

        $geo = array(
            'city'    => array(
                'id'   => null,
                'name' => null
            ),
            'country' => array(
                'id'   => null,
                'name' => null
            ),
        );

        if(isset($data['country'])){
            $geo['country'] = $this->model->getItemByField('geo_countries', 'alpha2', $data['country']);
        }

        if(isset($data['city'])){

            if(!empty($geo['country']['id'])){
                $this->model->filterEqual('country_id', $geo['country']['id']);
            }

            $geo['city'] = $this->model->getItemByField('geo_cities', 'name', $data['city']);

        }

        cmsUser::sessionSet('geo_data', $geo);

        return $geo;

    }

}