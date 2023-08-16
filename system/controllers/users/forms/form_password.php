<?php

class formUsersPassword extends cmsForm {

    public function init($profile) {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_PASSWORD,
                'childs' => [
                    new fieldString('password', [
                        'title'       => LANG_OLD_PASS,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ],
                        'rules' => [
                            ['required'],
                            [function ($controller, $data, $value)use ($profile) {

                                $user = cmsCore::getModel('users')->getUserByAuth($profile['email'], $value);

                                if (!$user) {
                                    return LANG_OLD_PASS_INCORRECT;
                                }

                                return true;
                            }]
                        ]
                    ]),
                    new fieldString('password1', [
                        'title'       => LANG_NEW_PASS,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ],
                        'rules' => [
                            [function ($controller, $data, $value)use ($profile) {

                                if (!$value) {
                                    return true;
                                }

                                $user = cmsCore::getModel('users')->getUserByAuth($profile['email'], $value);

                                if ($user) {
                                    return ERR_NEW_PASS_AS_OLD;
                                }

                                return true;
                            }]
                        ]
                    ]),
                    new fieldString('password2', [
                        'title'       => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'options'     => [
                            'min_length' => 6,
                            'max_length' => 72
                        ]
                    ])
                ]
            ]
        ];

    }
}
