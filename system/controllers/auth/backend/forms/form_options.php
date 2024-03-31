<?php

class formAuthOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $auth_redirect_items = [
            'none'        => LANG_REG_CFG_AUTH_REDIRECT_NONE,
            'index'       => LANG_REG_CFG_AUTH_REDIRECT_INDEX,
            'profile'     => LANG_REG_CFG_AUTH_REDIRECT_PROFILE,
            'profileedit' => LANG_REG_CFG_AUTH_REDIRECT_PROFILEEDIT
        ];

        $model = new cmsModel();

        $show_notify_old_auth_options = $model->filterIsNull('password_hash')->getCount('{users}');

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_REGISTRATION,
                'childs' => [
                    new fieldCheckbox('is_reg_enabled', [
                        'title' => LANG_REG_CFG_IS_ENABLED
                    ]),
                    new fieldString('reg_reason', [
                        'title' => LANG_REG_CFG_DISABLED_NOTICE,
                        'visible_depend' => ['is_reg_enabled' => ['show' => ['0']]]
                    ]),
                    new fieldCheckbox('is_reg_invites', [
                        'title' => LANG_REG_CFG_IS_INVITES
                    ]),
                    new fieldCheckbox('reg_captcha', [
                        'title' => LANG_REG_CFG_REG_CAPTCHA
                    ]),
                    new fieldCheckbox('reg_auto_auth', [
                        'title'   => LANG_REG_CFG_REG_AUTO_AUTH,
                        'default' => 1
                    ]),
                    new fieldListGroups('def_groups', [
                        'title' => LANG_REG_CFG_DEF_GROUP_ID,
                        'show_all' => false,
                        'default' => [3],
                        'rules' => [
                            [function($controller, $data, $value) {
                                if($value){
                                    return true;
                                }
                                $public_groups = cmsCore::getModel('users')->getPublicGroups();
                                if(!$public_groups){
                                    return ERR_VALIDATE_REQUIRED;
                                }
                                return true;
                            }]
                        ]
                    ]),
                    new fieldCheckbox('verify_email', [
                        'title' => LANG_REG_CFG_VERIFY_EMAIL,
                        'hint' => LANG_REG_CFG_VERIFY_EMAIL_HINT
                    ]),
                    new fieldNumber('verify_exp', [
                        'title'   => LANG_REG_CFG_VERIFY_EXPIRATION,
                        'default' => 48,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldCheckbox('send_greetmsg', [
                        'title' => LANG_REG_CFG_SEND_GREETMSG
                    ]),
                    new fieldHtml('greetmsg', [
                        'title' => LANG_REG_CFG_GREETMSG,
                        'visible_depend' => ['send_greetmsg' => ['show' => ['1']]]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_AUTHORIZATION,
                'childs' => [
                    new fieldCheckbox('notify_old_auth', [
                        'title' => LANG_REG_CFG_NOTIFY_OLD_AUTH,
                        'hint' => LANG_REG_CFG_NOTIFY_OLD_AUTH_HINT,
                        'is_visible' => $show_notify_old_auth_options
                    ]),
                    new fieldCheckbox('auth_captcha', [
                        'title' => LANG_REG_CFG_AUTH_CAPTCHA,
                    ]),
                    new fieldList('first_auth_redirect', [
                        'title'   => LANG_REG_CFG_FIRST_AUTH_REDIRECT,
                        'default' => 'profileedit',
                        'items'   => $auth_redirect_items
                    ]),
                    new fieldList('auth_redirect', [
                        'title'   => LANG_REG_CFG_AUTH_REDIRECT,
                        'default' => 'none',
                        'items'   => $auth_redirect_items
                    ]),
                    new fieldList('2fa', [
                        'title' => LANG_REG_CFG_AUTH_2FA,
                        'is_chosen_multiple' => true,
                        'generator' => function(){

                            $providers = cmsEventsManager::hookAll('auth_twofactor_list');

                            $items = [];

                            if (is_array($providers)){
                                foreach($providers as $provider){
                                    foreach($provider['types'] as $name => $title){
                                        $items[$name] = $title;
                                    }
                                }
                            }

                            return $items;
                        }
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_AUTH_RESTRICTIONS,
                'childs' => [
                    new fieldCheckbox('is_site_only_auth_users', [
                        'title' => LANG_CP_SETTINGS_SITE_ONLY_TO_USERS
                    ]),
                    new fieldList('guests_allow_controllers', [
                        'title'     => LANG_REG_CFG_GUESTS_ALLOW_CONTROLLERS,
                        'default'   => ['auth', 'geo'],
                        'is_chosen_multiple' => true,
                        'generator' => function ($item){
                            $admin_model = cmsCore::getModel('admin');
                            $controllers = $admin_model->getInstalledControllers();
                            $items = ['' => ''];
                            foreach($controllers as $controller){
                                $items[$controller['name']] = $controller['title'];
                            }
                            return $items;
                        },
                        'visible_depend' => ['is_site_only_auth_users' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('disable_restore', [
                        'title' => LANG_AUTH_DISABLE_RESTORE
                    ]),
                    new fieldText('restricted_emails', [
                        'title' => LANG_AUTH_RESTRICTED_EMAILS,
                        'hint' => LANG_AUTH_RESTRICTED_EMAILS_HINT
                    ]),
                    new fieldText('restricted_names', [
                        'title' => LANG_AUTH_RESTRICTED_NAMES,
                        'hint' => LANG_AUTH_RESTRICTED_NAMES_HINT
                    ]),
                    new fieldText('restricted_ips', [
                        'title' => LANG_AUTH_RESTRICTED_IPS,
                        'hint' => LANG_AUTH_RESTRICTED_IPS_HINT
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_AUTH_INVITES,
                'childs' => [
                    new fieldCheckbox('is_invites', [
                        'title' => LANG_AUTH_INVITES_AUTO,
                        'hint' => LANG_AUTH_INVITES_AUTO_HINT
                    ]),
                    new fieldCheckbox('is_invites_strict', [
                        'title' => LANG_AUTH_INVITES_STRICT,
                        'hint' => LANG_AUTH_INVITES_STRICT_HINT
                    ]),
                    new fieldNumber('invites_period', [
                        'title' => LANG_AUTH_INVITES_PERIOD,
                        'units' => LANG_DAY10,
                        'default' => 7,
                        'rules' => [
                            ['min', 1]
                        ]
                    ]),
                    new fieldNumber('invites_qty', [
                        'title' => LANG_AUTH_INVITES_QTY,
                        'rules' => [
                            ['min', 1]
                        ]
                    ]),
                    new fieldNumber('invites_min_karma', [
                        'title' => LANG_AUTH_INVITES_KARMA
                    ]),
                    new fieldNumber('invites_min_rating', [
                        'title' => LANG_AUTH_INVITES_RATING,
                    ]),
                    new fieldNumber('invites_min_days', [
                        'title' => LANG_AUTH_INVITES_DATE,
                        'units' => LANG_DAY10
                    ])
                ]
            ]
        ];
    }

}
