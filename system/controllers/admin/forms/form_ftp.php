<?php
class formAdminFtp extends cmsForm {

    public function init() {

        return [

            [
                'type' => 'fieldset',
                'title' => LANG_CP_FTP_ACCOUNT,
                'childs' => [
                    new fieldHidden('addon_id', []),
                    new fieldString('host', [
                        'title' => LANG_CP_FTP_HOST,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('port', [
                        'title'   => LANG_CP_FTP_PORT,
                        'default' => 21,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('user', [
                        'title' => LANG_CP_FTP_USER,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('pass', [
                        'title' => LANG_CP_FTP_PASS,
                        'is_password' => true,
                        'is_clean_disable' => true
                    ]),
                    new fieldString('path', [
                        'title' => LANG_CP_FTP_PATH,
                        'hint' => LANG_CP_FTP_PATH_HINT,
                        'default' => '/',
                        'suffix' => '<a id="check_ftp" href="'.href_to('admin', 'check_ftp').'" class="ajaxlink">'.LANG_CP_CHECK.'</a>',
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldCheckbox('is_pasv', [
                        'title' => LANG_CP_FTP_IS_PASV,
                        'default' => true
                    ])
                ]
            ],
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldCheckbox('save_to_session', [
                        'title'   => LANG_CP_FTP_SAVE_TO_SESSION,
                        'hint'    => LANG_CP_FTP_SAVE_TO_SESSION_HINT,
                        'default' => false
                    ])
                ]
            ],
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_skip', [
                        'title' => LANG_CP_FTP_SKIP,
                        'hint' => LANG_CP_FTP_SKIP_HINT,
                        'default' => false
                    ])
                ]
            ]
        ];
    }

}
