<?php

class formAuthReset extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('password1', array(
                        'title' => LANG_NEW_PASS,
                        'is_password' => true,
                        'rules' => array(
                            array('required'),
                            array('min_length', 6)
                        )
                    )),
                    new fieldString('password2', array(
                        'title' => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'rules' => array(
                            array('required'),
                            array('min_length', 6)
                        )
                    )),
                )
            ),


        );

    }

}
