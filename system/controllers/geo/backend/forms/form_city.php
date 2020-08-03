<?php

class formGeoCity extends cmsForm {

    public function init($country_id){

        $model = cmsCore::getModel('geo');

        return array(
            array(
                'type'   => 'fieldset',
                'childs' => array(

                    new fieldList('country_id', array(
                        'title' => LANG_COUNTRY,
                        'rules' => array(array('required')),
                        'items' => $model->getCountries()
                    )),

                    new fieldList('region_id', array(
                        'title'  => LANG_REGION,
                        'rules'  => array(array('required')),
                        'parent' => array(
                            'list' => 'country_id',
                            'url'  => href_to('admin', 'get_table_list', ['geo_regions', 'id', 'name'])
                        ),
                        'generator' => function($item, $request) use($model) {
                            $list     = ['0' => ''];
                            $country_id = is_array($item) ? array_value_recursive('country_id', $item) : false;
                            if (!$country_id && $request) {
                                $country_id = $request->get('country_id', 0);
                            }
                            if (!$country_id) {
                                return $list;
                            }
                            return $model->getRegions($country_id);
                        }
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
