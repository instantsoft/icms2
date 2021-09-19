<?php

class formGeoOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => '',
                'childs' => array(
                    new fieldCheckbox('auto_detect', array(
                        'title' => LANG_PARSER_CITY_AUTO_DETECT,
                        'default' => 1
                    )),
                    new fieldList('auto_detect_provider', array(
                        'title' => LANG_GEO_AUTO_DETECT_PROVIDER,
                        'default' => 'geoiplookup',
                        'generator' => function ($item){

                            $items = array();
                            $files = cmsCore::getFilesList('system/controllers/geo/iplookups', '*.php', true, true);

                            foreach ($files as $name) {

                                $class = 'icms' . string_to_camel('_', $name);

                                $items[$name] = $class::$title;

                            }

                            return $items;

                        },
                        'visible_depend' => array('auto_detect' => array('show' => array('1')))
                    )),
                    new fieldCity('default_country_id', array(
                        'title' => LANG_GEO_DEFAULT_COUNTRY_ID,
                        'default' => 0,
                        'options' => array(
                            'location_type' => 'countries'
                        )
                    ))
                )
            )

        );

    }

}
