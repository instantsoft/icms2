<?php

class formAuthgaConfirm extends cmsForm {

    public function init($logged_user) {

        cmsCore::loadLib('google_authenticator.class');

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('ga_confirm_code', array(
                        'title' => LANG_AUTHGA_GA_SECRET_RESPOSE,
                        'rules' => array(
                            array('required'),
                            array(function($controller, $data, $value) use($logged_user){

                                $ga = new googleAuthenticator();

                                if(!$ga->verifyCode($logged_user['ga_secret'], $value)){
                                    return ERR_VALIDATE_INVALID;
                                }

                                return true;

                            })
                        )
                    ))
                )
            )

        );

    }

}
