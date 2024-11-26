<?php

class formAuthgaConfirm extends cmsForm {

    public function init($logged_user) {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('ga_confirm_code', [
                        'title' => LANG_AUTHGA_GA_SECRET_RESPOSE,
                        'rules' => [
                            ['required'],
                            [function ($controller, $data, $value) use ($logged_user) {

                                $ga = new googleAuthenticator();

                                if (!$ga->verifyCode($logged_user['ga_secret'], $value)) {
                                    return ERR_VALIDATE_INVALID;
                                }

                                return true;
                            }]
                        ]
                    ])
                ]
            ]
        ];

    }

}
