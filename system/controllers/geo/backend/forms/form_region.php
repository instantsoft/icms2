<?php

class formGeoRegion extends cmsForm {

    public function init(){

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldList('country_id', array(
                        'title' => LANG_COUNTRY,
                        'rules' => array(array('required')),
                        'generator' => function() {
                            return cmsCore::getModel('geo')->getCountries();
                        }
                    )),

                    new fieldString('name', array(
                        'title' => LANG_REGION,
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
