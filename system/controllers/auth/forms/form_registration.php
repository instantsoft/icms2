<?php

class formAuthRegistration extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'title' => LANG_AUTH_REG_AUTH,
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
                    new fieldString('password1', array(
                        'title' => LANG_PASSWORD,
                        'is_password' => true,
                        'options'=>array(
                            'min_length'=> 6,
                            'max_length'=> 72
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldString('password2', array(
                        'title' => LANG_RETYPE_PASSWORD,
                        'is_password' => true,
                        'options'=>array(
                            'min_length'=> 6,
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
