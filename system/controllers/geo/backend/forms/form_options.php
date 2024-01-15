<?php

class formGeoOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => '',
                'childs' => [
                    new fieldCheckbox('auto_detect', [
                        'title'   => LANG_PARSER_CITY_AUTO_DETECT,
                        'default' => 1
                    ]),
                    new fieldList('auto_detect_provider', [
                        'title'     => LANG_GEO_AUTO_DETECT_PROVIDER,
                        'default'   => 'geoiplookup',
                        'generator' => function ($item) {

                            $items = [];
                            $files = cmsCore::getFilesList('system/controllers/geo/iplookups', '*.php', true, true);

                            foreach ($files as $name) {

                                $class = 'icms' . string_to_camel('_', $name);

                                $items[$name] = $class::$title;
                            }

                            return $items;
                        },
                        'visible_depend' => ['auto_detect' => ['show' => ['1']]]
                    ]),
                    new fieldCity('default_country_id', [
                        'title'   => LANG_GEO_DEFAULT_COUNTRY_ID,
                        'default' => 0,
                        'options' => [
                            'location_type' => 'countries'
                        ]
                    ])
                ]
            ]
        ];
    }

}
