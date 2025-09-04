<?php
class formAdminUser extends cmsForm {

    public function init($do) {

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_USER,
                'childs' => [
                    new fieldString('email', [
                        'title' => LANG_EMAIL,
                        'rules' => [
                            ['required'],
                            ['email'],
                            $do === 'add' ? ['unique', '{users}', 'email'] : false
                        ]
                    ]),
                    new fieldString('nickname', [
                        'title' => LANG_NICKNAME,
                        'options'=>[
                            'max_length'=> 100
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('password1', [
                        'title' => LANG_NEW_PASS,
                        'is_password' => true,
                        'options'=>[
                            'min_length'=> 6,
                            'max_length'=> 72
                        ],
                        'rules' => [
                            $do === 'add' ? ['required'] : false
                        ]
                    ]),
                    new fieldString('password2', [
                        'title' => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'options'=>[
                            'min_length'=> 6,
                            'max_length'=> 72
                        ],
                        'rules' => [
                            $do === 'add' ? ['required'] : false
                        ]
                    ])
                ]
            ],
            'permissions' => [
                'type' => 'fieldset',
                'title' => LANG_PERMISSIONS,
                'childs' => [
                    new fieldCheckbox('is_admin', [
                        'title' => LANG_USER_IS_ADMIN,
                        'default' => false
                    ])
                ]
            ],
            'groups' => [
                'type' => 'fieldset',
                'title' => LANG_USER_GROUP,
                'childs' => [
                    new fieldListGroups('groups', [
                        'show_all' => false,
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ],
            'locked' => [
                'type'  => 'fieldset',
                'title' => LANG_CP_USER_LOCKING,
                'childs' => [
                    new fieldCheckbox('is_locked', [
                        'title' => LANG_CP_USER_IS_LOCKED
                    ]),
                    new fieldDate('lock_until', [
                        'title' => LANG_CP_USER_LOCK_UNTIL
                    ]),
                    new fieldString('lock_reason', [
                        'title' => LANG_CP_USER_LOCK_REASON,
                        'rules' => [
                            ['max_length', 250]
                        ]
                    ])
                ]
            ]
        ];

    }

}
