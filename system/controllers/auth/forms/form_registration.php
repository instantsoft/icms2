<?php

class formAuthRegistration extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('email', array(
                        'title' => LANG_EMAIL,
                        'rules' => array(
                            array('required'),
                            array('email'),
                            array('unique', '{users}', 'email')
                        )
                    )),
                    new fieldString('nickname', array(
                        'title' => LANG_NICKNAME,
                        'rules' => array(
                            array('required'),
                        )
                    )),
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
