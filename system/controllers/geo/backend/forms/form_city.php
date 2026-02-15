<?php

class formGeoCity extends cmsForm {

    public function init($country_id){

        $model = cmsCore::getModel('geo');

        return [
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('country_id', [
                        'title' => LANG_COUNTRY,
                        'rules' => [['required']],
                        'items' => $model->getCountries()
                    ]),
                    new fieldList('region_id', [
                        'title'  => LANG_REGION,
                        'rules'  => [['required']],
                        'parent' => [
                            'list' => 'country_id',
                            'url'  => href_to('admin', 'get_table_list', ['geo_regions', 'id', 'name'])
                        ],
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
                    ]),
                    new fieldString('name', [
                        'title' => LANG_CITY,
                        'options'=>[
                            'max_length' => 128,
                            'show_symbol_count' => true
                        ],
                        'rules' => [['required']]
                    ]),
                    new fieldString('ordering', [
                        'title' => LANG_GEO_POSITION,
                        'rules' => [['number'], ['required']]
                    ])
                ]
            ]
        ];
    }
}
