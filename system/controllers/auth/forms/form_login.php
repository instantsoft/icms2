<?php

class formAuthLogin extends cmsForm {

    public $show_unsave_notice = false;

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_AUTHORIZATION,
                'childs' => array(
                    new fieldString('login_email', array(
                        'title' => LANG_EMAIL,
                        'type'  => 'email',
                        'rules' => array(
                            array('required'),
                            array('email')
                        )
                    )),
                    new fieldString('login_password', array(
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
                    new fieldCheckbox('remember', array(
                        'title' => '<span class="auth_remember">'.LANG_REMEMBER_ME.'</span> <a class="auth_restore_link" href="'.href_to('auth', 'restore').'">'.LANG_FORGOT_PASS.'</a>'
                    ))
                )
            )

        );

    }

}
