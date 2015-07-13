<?php

class formRecaptchaOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_RECAPTCHA_KEYS, 
                'childs' => array(

                    new fieldString('public_key', array(
                        'title' => LANG_RECAPTCHA_PUBLIC_KEY,                        
                    )),

                    new fieldString('private_key', array(
                        'title' => LANG_RECAPTCHA_PRIVATE_KEY,
                        'hint' => LANG_RECAPTCHA_SIGN_UP
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_RECAPTCHA_LOOK_AND_FEEL,
                'childs' => array(

                    new fieldList('theme', array(
                        'title' => LANG_RECAPTCHA_SKIN, 
                        'items' => array(
                            'red' => 'red',
                            'blackglass' => 'blackglass',
                            'white' => 'white',
                            'clean' => 'clean'
                        )
                    )), 
                    
                    new fieldList('lang', array(
                        'title' => LANG_RECAPTCHA_LANG, 
                        'items' => array(
                            'en' => 'English',
                            'nl' => 'Dutch',
                            'fr' => 'French',
                            'de' => 'German',
                            'pt' => 'Portuguese',
                            'ru' => 'Russian',
                            'es' => 'Spanish',
                            'tr' => 'Turkish'
                        )
                    ))

                )
            ),

        );

    }

}
