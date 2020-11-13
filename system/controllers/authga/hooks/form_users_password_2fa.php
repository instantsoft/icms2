<?php

class onAuthgaFormUsersPassword2fa extends cmsAction {

    public function run($data) {

        list($form, $params) = $data;

        $profile = $params[0];

        cmsCore::loadLib('google_authenticator.class');

        $ga = new googleAuthenticator();

        if ($profile['ga_secret']) {

            $form->addField('twofa', new fieldString('ga_secret_respose', array(
                    'title' => LANG_AUTHGA_GA_SECRET_RESPOSE,
                    'rules' => array(
                        array(function($controller, $data, $value)use($profile){

                            if($data['2fa'] === 'authga'){ return true; }

                            if(!$value){ return ERR_VALIDATE_REQUIRED; }

                            $ga = new googleAuthenticator();

                            if(!$ga->verifyCode($profile['ga_secret'], $value)){
                                return ERR_VALIDATE_INVALID;
                            }

                            return true;

                        })
                    ),
                    'visible_depend' => array('2fa' => array('hide' => array($this->name)))
                ))
            );

            return [$form, $params];
        }

        $ga_secret = $ga->createSecret();

        $form->addField('twofa', new cmsFormField('ga_qrcode', array(
                'title' => LANG_AUTHGA_GA_QRCODE,
                'html' => '<img src="'.$ga->getQRCodeGoogleUrl($this->cms_config->sitename, $ga_secret).'">',
                'visible_depend' => array('2fa' => array('show' => array($this->name)))
            ))
        )->disableField('ga_qrcode');

        $form->addField('twofa', new fieldString('ga_secret', array(
                'title' => LANG_AUTHGA_GA_SECRET,
                'default' => $ga_secret,
                'attributes' => ['readonly' => true],
                'visible_depend' => array('2fa' => array('show' => array($this->name)))
            ))
        );

        $form->addField('twofa', new fieldString('ga_secret_respose', array(
                'title' => LANG_AUTHGA_GA_SECRET_RESPOSE,
                'rules' => array(
                    array(function($controller, $data, $value){

                        if($data['2fa'] !== 'authga'){ return true; }

                        if(!$value || empty($data['ga_secret'])){ return ERR_VALIDATE_REQUIRED; }

                        $ga = new googleAuthenticator();

                        if(!$ga->verifyCode($data['ga_secret'], $value)){
                            return ERR_VALIDATE_INVALID;
                        }

                        return true;

                    })
                ),
                'visible_depend' => array('2fa' => array('show' => array($this->name)))
            ))
        );

        return [$form, $params];

    }

}
