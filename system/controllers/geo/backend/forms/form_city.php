<?php

class formGeoCity extends cmsForm {

    public function init($country_id){

        $model = cmsCore::getModel('geo');

        $regions   = array();
        $countries = $model->getCountries();

        if($country_id){
            $regions = $model->getRegions($country_id);
        }

        return array(
            array(
                'type'   => 'fieldset',
                'childs' => array(

                    new fieldList('country_id', array(
                        'title' => LANG_COUNTRY,
                        'rules' => array(array('required')),
                        'items' => $countries
                    )),

                    new fieldList('region_id', array(
                        'title'  => LANG_REGION,
                        'rules'  => array(array('required')),
                        'parent' => array(
                            'list' => 'country_id',
                            'url'  => href_to('admin/controllers/edit/geo', 'get_regions_ajax')
                        ),
                        'items'  => $regions
                    )),

                    new fieldString('name', array(
                        'title' => LANG_CITY,
                        'options'=>array(
                            'max_length'=> 128,
                            'show_symbol_count'=>true
                        ),
                        'rules' => array(array('required'))
                    )),

                    new fieldString('ordering', array(
                        'title' => LANG_GEO_POSITION,
                        'rules' => array(array('number'),array('required'))
                    ))
                )
            )
        );
    }
}
