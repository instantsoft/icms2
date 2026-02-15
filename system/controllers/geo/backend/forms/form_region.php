<?php

class formGeoRegion extends cmsForm {

    public function init(){

        return [
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldList('country_id', [
                        'title' => LANG_COUNTRY,
                        'rules' => [['required']],
                        'generator' => function() {
                            return cmsCore::getModel('geo')->getCountries();
                        }
                    ]),
                    new fieldString('name', [
                        'title' => LANG_REGION,
                        'options'=> [
                            'max_length' => 128,
                            'show_symbol_count' =>true
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
