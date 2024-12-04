<?php

class formRecaptchaOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_RECAPTCHA_KEYS,
                'childs' => [
                    new fieldString('public_key', [
                        'title' => LANG_RECAPTCHA_PUBLIC_KEY,
                    ]),
                    new fieldString('private_key', [
                        'title' => LANG_RECAPTCHA_PRIVATE_KEY,
                        'hint'  => LANG_RECAPTCHA_SIGN_UP
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_RECAPTCHA_LOOK_AND_FEEL,
                'childs' => [
                    new fieldList('theme', [
                        'title' => LANG_RECAPTCHA_SKIN,
                        'items' => [
                            'light' => LANG_RECAPTCHA_SKIN_LIGHT,
                            'dark'  => LANG_RECAPTCHA_SKIN_DARK
                        ]
                    ]),
                    new fieldList('size', [
                        'title' => LANG_RECAPTCHA_SIZE,
                        'items' => [
                            'normal'  => LANG_RECAPTCHA_SIZE_NORMAL,
                            'compact' => LANG_RECAPTCHA_SIZE_COMPACT
                        ]
                    ]),
                    new fieldList('lang', [
                        'title' => LANG_RECAPTCHA_LANG,
                        'items' => [
                            ''   => LANG_AUTO,
                            'en' => 'English',
                            'nl' => 'Dutch',
                            'fr' => 'French',
                            'de' => 'German',
                            'pt' => 'Portuguese',
                            'ru' => 'Русский',
                            'uk' => 'Українська',
                            'es' => 'Spanish',
                            'tr' => 'Turkish'
                        ]
                    ])
                ]
            ]
        ];
    }

}
