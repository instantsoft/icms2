<?php

class formUsersPassword extends cmsForm {

    public function init($profile) {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_PASSWORD,
                'childs' => array(
                    new fieldString('password', array(
                        'title' => LANG_OLD_PASS,
                        'is_password' => true,
                        'options'=>array(
                            'min_length'=> 6,
                            'max_length'=> 72
                        ),
                        'rules' => array(
                            array('required'),
                            array(function($controller, $data, $value)use($profile){

                                $user = cmsCore::getModel('users')->getUserByAuth($profile['email'], $value);

                                if (!$user){
                                    return LANG_OLD_PASS_INCORRECT;
                                }

                                return true;

                            })
                        )
                    )),
                    new fieldString('password1', array(
                        'title' => LANG_NEW_PASS,
                        'is_password' => true,
                        'options'=>array(
                            'min_length'=> 6,
                            'max_length'=> 72
                        )
                    )),
                    new fieldString('password2', array(
                        'title' => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'options'=>array(
                            'min_length'=> 6,
                            'max_length'=> 72
                        )
                    ))
                )
            )

        );

    }

}
