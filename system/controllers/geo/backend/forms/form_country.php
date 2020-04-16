<?php

class formGeoCountry extends cmsForm {

	public function init(){

		return array(
			array(
				'type' => 'fieldset',
				'childs' => array(
					new fieldString('name', array(
                        'title' => LANG_COUNTRY,
                        'options'=>array(
                            'max_length'=> 128,
                            'show_symbol_count'=>true
                        ),
                        'rules' => array(array('required'))
                    )),
					new fieldString('alpha2', array(
                        'title' => LANG_GEO_ALPHA2,
                        'options'=>array(
                            'max_length'=> 2,
                            'show_symbol_count'=>true
                        ),
                        'rules' => array(array('required'))
                    )),
					new fieldString('alpha3', array(
                        'title' => LANG_GEO_ALPHA3,
                        'options'=>array(
                            'max_length'=> 3,
                            'show_symbol_count'=>true
                        ),
                        'rules' => array(array('required'))
                    )),
					new fieldString('iso', array(
                        'title' => LANG_GEO_ISO,
                        'rules' => array(array('number'),array('required'))
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
