<?php

class formAuthLogin extends cmsForm {

    public $show_unsave_notice = false;

    public function init() {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_AUTHORIZATION,
                'childs' => [
                    new fieldString('login_email', [
                        'title' => LANG_EMAIL,
                        'type'  => 'email',
                        'rules' => [
                            ['required'],
                            ['email']
                        ]
                    ]),
                    new fieldString('login_password', [
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
                    new fieldCheckbox('remember', [
                        'title' => '<span class="auth_remember">' . LANG_REMEMBER_ME . '</span>'.
                        (empty($this->controller->options['disable_restore']) ? ' <a class="auth_restore_link" href="' . href_to('auth', 'restore') . '">' . LANG_FORGOT_PASS . '</a>' : '')
                    ])
                ]
            ]
        ];
    }

}
