<?php
class geo extends cmsFrontend {

    public function actionWidget($field_id, $city_id = false){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();
        $user = cmsUser::getInstance();

        $countries = $this->model->getCountries();
        $countries = array('0'=>LANG_GEO_SELECT_COUNTRY) + $countries;

        $regions = array();
        $cities = array();

        $region_id = false;
        $country_id = false;

        if ($user->is_logged && !$city_id && $user->city['id']){
            $city_id = $user->city['id'];
        }

        if ($city_id){

            $city_parents = $this->model->getCityParents($city_id);

            $region_id = $city_parents['region_id'];
            $country_id = $city_parents['country_id'];

            $regions = $this->model->getRegions($country_id);
            $regions = array('0'=>LANG_GEO_SELECT_REGION) + $regions;

            $cities = $this->model->getCities($region_id);
            $cities = array('0'=>LANG_GEO_SELECT_CITY) + $cities;

        }

        $template->render('widget', array(
            'field_id' => $field_id,
            'city_id' => $city_id,
            'country_id' => $country_id,
            'region_id' => $region_id,
            'countries' => $countries,
            'regions' => $regions,
            'cities' => $cities,
        ));

    }

    public function actionGetItems(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $type = $this->request->get('type');
        $parent_id = $this->request->get('parent_id');

        if (!in_array($type, array('regions', 'cities'))) { cmsCore::error404(); }
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

        cmsTemplate::getInstance()->renderJSON(array(
           'error' => $items ? false : true,
           'items' => $items
        ));

    }

}

