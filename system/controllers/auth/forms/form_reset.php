<?php

class formAuthReset extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('password1', array(
                        'title'       => LANG_NEW_PASS,
                        'is_password' => true,
                        'options'     => array(
                            'min_length' => 6,
                            'max_length'=> 72
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldString('password2', array(
                        'title'       => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'options'     => array(
                            'min_length' => 6,
                            'max_length'=> 72
                        ),
                        'rules' => array(
                            array('required')
                        )
                    ))
                )
            )

        );

    }

}
