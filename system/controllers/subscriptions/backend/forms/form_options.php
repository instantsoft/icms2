<?php

class formSubscriptionsOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_BASIC,
                'childs' => [
                    new fieldCheckbox('need_auth', [
                        'title'   => LANG_SBSCR_NEED_AUTH,
                        'default' => 0
                    ]),
                    new fieldCheckbox('guest_email_confirmation', [
                        'title'          => LANG_SBSCR_GUEST_EMAIL_CONFIRMATION,
                        'default'        => 1,
                        'visible_depend' => ['need_auth' => ['hide' => ['1']]]
                    ]),
                    new fieldNumber('verify_exp', [
                        'title'          => LANG_SBSCR_VERIFY_EXP,
                        'units'          => LANG_HOURS,
                        'default'        => 24,
                        'visible_depend' => ['need_auth' => ['hide' => ['1']]]
                    ]),
                    new fieldString('admin_email', [
                        'title' => LANG_SBSCR_ADMIN_EMAIL
                    ]),
                    new fieldCheckbox('show_btn_title', [
                        'title'   => LANG_SBSCR_SHOW_BTN_TITLE,
                        'hint'    => LANG_SBSCR_SHOW_BTN_TITLE_HINT,
                        'default' => 1
                    ]),
                    new fieldNumber('limit', [
                        'title'   => LANG_SBSCR_LIMIT,
                        'default' => 20,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ]
        ];

    }

}
