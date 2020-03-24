<?php

class onUsersAdminDashboardChart extends cmsAction {

	public function run(){

        $data = [
            'id' => 'users',
            'title' => LANG_USERS,
            'sections' => [
                'reg' => [
                    'title' => LANG_REGISTRATION,
                    'table' => '{users}',
                    'key' => 'date_reg'
                ],
                // если title не указан, не будет показываться в селекте
                // через двоеточие указываются связанные данные, которые нужно отдать
                // вместе с основными данными
                // графики наложатся
                'reg:unconfirmed' => [
                    'table' => '{users}',
                    'hint' => LANG_CP_USERS_UNCONFIRMED,
                    'key' => 'date_reg',
                    'style' => [
                        'bg_color' => 'rgba(248, 108, 107, 0.1)',
                        'border_color' => 'rgba(248, 108, 107, 1)'
                    ],
                    'filters' => [
                        [
                            'condition' => 'eq',
                            'value'     => 1,
                            'field'     => 'is_locked'
                        ],
                        [
                            'condition' => 'nn',
                            'value'     => '',
                            'field'     => 'pass_token'
                        ]
                    ]
                ],
                'log' => [
                    'title' => LANG_AUTH_LOGIN,
                    'table' => '{users}',
                    'key' => 'date_log'
                ]
            ],
            'footer' => [
                'reg' => [
                    [
                        'table' => '{users}',
                        'title' => LANG_CP_USERS_ALL,
                        'progress' => 'success'
                    ],
                    [
                        'table' => '{users}',
                        'title' => LANG_CP_USERS_UNCONFIRMED,
                        'progress' => 'warning',
                        'filters' => [
                            [
                                'condition' => 'eq',
                                'value'     => 1,
                                'field'     => 'is_locked'
                            ],
                            [
                                'condition' => 'nn',
                                'value'     => '',
                                'field'     => 'pass_token'
                            ]
                        ]
                    ],
                    [
                        'table' => '{users}',
                        'title' => LANG_CP_USERS_LOCKED,
                        'progress' => 'danger',
                        'filters' => [
                            [
                                'condition' => 'eq',
                                'value'     => 1,
                                'field'     => 'is_locked'
                            ],
                            [
                                'condition' => 'ni',
                                'value'     => '',
                                'field'     => 'pass_token'
                            ]
                        ]
                    ],
                    [
                        'table' => '{users}',
                        'title' => LANG_CP_USERS_ISDELETED,
                        'progress' => 'secondary',
                        'filters' => [
                            [
                                'condition' => 'eq',
                                'value'     => 1,
                                'field'     => 'is_deleted'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $data;

    }

}
