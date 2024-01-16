<?php

class formGeoCountry extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('name', [
                        'title'   => LANG_COUNTRY,
                        'options' => [
                            'max_length'        => 128,
                            'show_symbol_count' => true
                        ],
                        'rules'   => [['required']]
                    ]),
                    new fieldString('alpha2', [
                        'title'   => LANG_GEO_ALPHA2,
                        'options' => [
                            'max_length'        => 2,
                            'show_symbol_count' => true
                        ],
                        'rules'   => [['required']]
                    ]),
                    new fieldString('alpha3', [
                        'title'   => LANG_GEO_ALPHA3,
                        'options' => array(
                            'max_length'        => 3,
                            'show_symbol_count' => true
                        ),
                        'rules'   => [['required']]
                    ]),
                    new fieldString('iso', [
                        'title' => LANG_GEO_ISO,
                        'rules' => [['number'], ['required']]
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
