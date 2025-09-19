<?php

class formAuthRegistration extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'title'  => LANG_AUTH_REG_AUTH,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('email', [
                        'title' => LANG_EMAIL,
                        'rules' => [
                            ['required'],
                            ['email'],
                            ['unique', '{users}', 'email']
                        ]
                    ]),
                    new fieldString('password1', [
                        'title'       => LANG_PASSWORD,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('password2', [
                        'title'       => LANG_RETYPE_PASSWORD,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }

}
