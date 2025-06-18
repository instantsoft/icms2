<?php

class formBillingOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return [
            [
                'title'  => LANG_BILLING_CP_TAB_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('currency_title', [
                        'title'   => LANG_BILLING_CP_CURRENCY_TITLE,
                        'hint'    => LANG_BILLING_CP_CURRENCY_TITLE_HINT,
                        'default' => LANG_BILLING_CP_CURRENCY_TITLE_DEF,
                        'multilanguage' => true,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldString('currency', [
                        'title'   => LANG_BILLING_CP_CURRENCY,
                        'hint'    => LANG_BILLING_CP_CURRENCY_HINT,
                        'default' => LANG_BILLING_CP_CURRENCY_DEF,
                        'multilanguage' => true,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldString('currency_real', [
                        'title'   => LANG_BILLING_CP_CURRENCY_REAL,
                        'hint'    => LANG_BILLING_CP_CURRENCY_REAL_HINT,
                        'default' => LANG_BILLING_CP_CURRENCY_REAL_DEF,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldString('cur_real_symb', [
                        'title'   => LANG_BILLING_CP_CURRENCY_REAL_SYMB,
                        'default' => LANG_BILLING_CP_CURRENCY_REAL_SYMB_DEF,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldNumber('min_pack', [
                        'title'   => LANG_BILLING_CP_MIN_PACK,
                        'hint'    => LANG_BILLING_CP_MIN_PACK_HINT,
                        'default' => 0,
                        'options' => [
                            'is_abs'  => true
                        ]
                    ]),
                    new fieldNumber('reg_bonus', [
                        'title'   => LANG_BILLING_CP_REG_BONUS,
                        'hint'    => LANG_BILLING_CP_REG_BONUS_HINT,
                        'default' => 0,
                        'options' => [
                            'is_abs'  => true
                        ]
                    ]),
                    new fieldHtml('pay_field_html', [
                        'title' => LANG_BILLING_CP_PAY_FIELD_HTML,
                        'patterns_hint' => [
                            'patterns' =>  [
                                'url'   => LANG_BILLING_CP_PAY_URL,
                                'price' => LANG_BILLING_CP_PRICE_SPELL,
                                'title' => LANG_BILLING_CP_FIELD_BTN_TITLE
                            ],
                            'text_panel'   => '',
                            'always_show'  => true,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ],
                        'options' => ['editor' => 'ace', 'editor_options' => ['minLines' => 3]]
                    ]),
                    new fieldString('btn_titles:guest', [
                        'title' => LANG_BILLING_CP_FIELD_BTN_TITLES_GUEST,
                        'multilanguage' => true,
                        'patterns_hint' => [
                            'patterns'     => ['price' => LANG_BILLING_CP_PRICE_SPELL],
                            'text_panel'   => '',
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ]),
                    new fieldString('btn_titles:user', [
                        'title' => LANG_BILLING_CP_FIELD_BTN_TITLES_USER,
                        'multilanguage' => true,
                        'patterns_hint' => [
                            'patterns'     => ['price' => LANG_BILLING_CP_PRICE_SPELL],
                            'text_panel'   => '',
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ]),
                    new fieldNumber('limit_log', [
                        'title'   => LANG_BILLING_CP_LIMIT_LOG,
                        'default' => 15,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ],
            [
                'title'  => LANG_BILLING_CP_TAB_IN,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('in_mode', [
                        'title'   => LANG_BILLING_CP_IN_MODE,
                        'default' => 'enabled',
                        'items'   => [
                            'disabled' => LANG_BILLING_CP_IN_MODE_OFF,
                            'enabled'  => LANG_BILLING_CP_IN_MODE_ON,
                            'plans'    => LANG_BILLING_CP_IN_MODE_PLANS_ONLY,
                        ]
                    ]),
                    new fieldFieldsgroup('prices', [
                        'title'     => LANG_BILLING_CP_TAB_DISCOUNTS,
                        'add_title' => LANG_BILLING_CP_DSC_ADD,
                        'default'   => [
                            ['amount' => 1, 'price' => 1]
                        ],
                        'childs'    => [
                            new fieldNumber('amount', [
                                'title' => LANG_BILLING_CP_DSC_VOLUME,
                                'options' => [
                                    'save_zero' => false,
                                    'is_abs'    => true
                                ],
                                'rules' => [
                                    ['required'],
                                    ['min', 0.0001]
                                ]
                            ]),
                            new fieldNumber('price', [
                                'title' => LANG_BILLING_CP_DSC_PRICE,
                                'options' => [
                                    'save_zero'   => false,
                                    'placeholder' => LANG_BILLING_IN_REAL_PRICE,
                                    'is_abs'      => true
                                ],
                                'rules' => [
                                    ['required'],
                                    ['min', 0.0001]
                                ]
                            ])
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ],
            [
                'title'  => LANG_BILLING_CP_TAB_PLANS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_plans', [
                        'title'   => LANG_BILLING_CP_PLANS_ENABLED,
                        'default' => true
                    ]),
                    new fieldNumber('plan_remind_days', [
                        'title'   => LANG_BILLING_CP_PLANS_REMIND_DAYS,
                        'units'   => LANG_DAY10,
                        'default' => 3,
                        'visible_depend' => ['is_plans' => ['show' => ['1']]],
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ],
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ],
            [
                'title'  => LANG_BILLING_CP_TAB_TRANSFERS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_transfers', [
                        'title'   => LANG_BILLING_CP_TRANSFERS_ENABLED,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_transfers_mail', [
                        'title'   => LANG_BILLING_CP_TRANSFERS_MAIL,
                        'hint'    => LANG_BILLING_CP_TRANSFERS_MAIL_HINT,
                        'default' => true,
                        'visible_depend' => ['is_transfers' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_transfers_notify', [
                        'title'   => LANG_BILLING_CP_TRANSFERS_NOTIFY,
                        'default' => true,
                        'visible_depend' => ['is_transfers' => ['show' => ['1']]]
                    ])
                ]
            ],
            [
                'title'  => LANG_BILLING_CP_TAB_EXCHANGE,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldListGroups('rtp_groups', [
                        'title'    => LANG_BILLING_CP_EXCHANGE_GROUPS,
                        'show_all' => true
                    ]),
                    new fieldCheckbox('is_rtp', [
                        'title' => LANG_BILLING_CP_EXCHANGE_RTP
                    ]),
                    new fieldNumber('rtp_rate', [
                        'title'   => LANG_BILLING_CP_EXCHANGE_RTP_RATE,
                        'hint'    => LANG_BILLING_CP_EXCHANGE_RTP_RATE_HINT,
                        'options' => [
                            'is_abs' => true
                        ],
                        'default' => 0.5,
                        'rules' => [
                            ['required'],
                            ['min', 0.0001]
                        ],
                        'visible_depend' => ['is_rtp' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_ptr', [
                        'title'   => LANG_BILLING_CP_EXCHANGE_PTR,
                        'default' => true
                    ]),
                    new fieldNumber('ptr_rate', [
                        'title'   => LANG_BILLING_CP_EXCHANGE_PTR_RATE,
                        'hint'    => LANG_BILLING_CP_EXCHANGE_PTR_RATE_HINT,
                        'options' => [
                            'is_abs' => true
                        ],
                        'default' => 1,
                        'rules' => [
                            ['required'],
                            ['min', 0.0001]
                        ],
                        'visible_depend' => ['is_ptr' => ['show' => ['1']]]
                    ])
                ]
            ],
            [
                'title'  => LANG_BILLING_CP_TAB_OUT,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_out', [
                        'title' => LANG_BILLING_CP_OUT_ENABLED
                    ]),
                    new fieldListGroups('out_groups', [
                        'title'    => LANG_BILLING_CP_OUT_GROUPS,
                        'show_all' => true
                    ]),
                    new fieldCheckbox('is_out_mail', [
                        'title'   => LANG_BILLING_CP_OUT_MAIL,
                        'hint'    => LANG_BILLING_CP_OUT_MAIL_HINT,
                        'default' => true
                    ]),
                    new fieldNumber('out_period_days', [
                        'title' => LANG_BILLING_CP_OUT_PERIOD,
                        'hint'  => LANG_BILLING_CP_OUT_PERIOD_HINT,
                        'units' => LANG_DAY10,
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldNumber('out_min', [
                        'title'   => LANG_BILLING_CP_OUT_MIN,
                        'hint'    => LANG_BILLING_CP_OUT_MIN_HINT,
                        'default' => 1,
                        'options' => [
                            'is_abs' => true
                        ],
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldNumber('out_rate', [
                        'title'   => LANG_BILLING_CP_OUT_RATE,
                        'hint'    => LANG_BILLING_CP_OUT_RATE_HINT,
                        'options' => [
                            'is_abs' => true
                        ],
                        'default' => 0.5
                    ]),
                    new fieldText('out_systems', [
                        'title'   => LANG_BILLING_CP_OUT_SYSTEMS,
                        'hint'    => LANG_BILLING_CP_OUT_SYSTEMS_HINT,
                        'default' => "Webmoney WMZ\nЮMoney\nQIWI\nPayPal"
                    ]),
                    new fieldString('out_email', [
                        'title' => LANG_BILLING_CP_OUT_NOTIFY_EMAIL,
                        'hint'  => LANG_BILLING_CP_OUT_NOTIFY_EMAIL_HINT,
                        'rules' => [
                            ['email']
                        ]
                    ]),
                    new fieldNumber('limit_out', [
                        'title'   => LANG_BILLING_CP_LIMIT_OUT,
                        'default' => 15,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ],
            [
                'title'  => LANG_BILLING_CP_TAB_REFS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_refs', [
                        'title'   => LANG_BILLING_CP_REFS_ENABLED,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_refs_as_invite', [
                        'title'   => LANG_BILLING_CP_REFS_AS_INVITE,
                        'hint'    => LANG_BILLING_CP_REFS_AS_INVITE_HINT,
                        'default' => false
                    ]),
                    new fieldNumber('ref_days', [
                        'title'   => LANG_BILLING_CP_REF_COOKIE_DAYS,
                        'hint'    => LANG_BILLING_CP_REF_COOKIE_DAYS_HINT,
                        'units'   => LANG_DAY10,
                        'default' => 100,
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldString('ref_url', [
                        'title' => LANG_BILLING_CP_REF_REDIRECT_URL,
                        'hint'  => LANG_BILLING_CP_REF_REDIRECT_URL_HINT
                    ]),
                    new fieldString('ref_terms', [
                        'title' => LANG_BILLING_CP_REF_TERMS_URL,
                        'hint'  => LANG_BILLING_CP_REF_TERMS_URL_HINT
                    ]),
                    new fieldNumber('ref_bonus', [
                        'title' => LANG_BILLING_CP_REF_BONUS,
                        'hint'  => LANG_BILLING_CP_REF_BONUS_HINT,
                        'options' => [
                            'is_abs' => true
                        ]
                    ]),
                    new fieldList('ref_mode', [
                        'title' => LANG_BILLING_CP_REF_MODE,
                        'items' => [
                            'dep' => LANG_BILLING_CP_REF_MODE_DEP,
                            'all' => LANG_BILLING_CP_REF_MODE_ALL,
                            'sub' => LANG_BILLING_CP_REF_MODE_SUB
                        ],
                        'default' => 'all'
                    ]),
                    new fieldList('ref_type', [
                        'title'   => LANG_BILLING_CP_REF_TYPE,
                        'items'   => [
                            'linear'  => LANG_BILLING_CP_REF_TYPE_LINEAR,
                            'collect' => LANG_BILLING_CP_REF_TYPE_COLLECT,
                        ],
                        'default' => 'linear'
                    ]),
                    new fieldNumber('ref_scale', [
                        'title'   => LANG_BILLING_CP_REF_SCALE,
                        'default' => 2,
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ],
                        'visible_depend' => ['ref_type' => ['show' => ['collect']]]
                    ]),
                    new fieldFieldsgroup('ref_levels', [
                        'title'     => LANG_BILLING_CP_REF_INCOME,
                        'add_title' => LANG_BILLING_CP_REF_LEVEL_ADD,
                        'is_counter_list' => true,
                        'default'   => [['percent' => 10]],
                        'childs'    => [
                            new fieldNumber('percent', [
                                'title' => LANG_BILLING_CP_REF_PERCENT,
                                'options' => [
                                    'save_zero' => false,
                                    'is_abs'    => true
                                ],
                                'rules' => [
                                    ['required'],
                                    ['min', 0.0001],
                                    ['max', 100]
                                ]
                            ])
                        ]
                    ]),
                    new fieldNumber('limit_refs', [
                        'title'   => LANG_BILLING_CP_LIMIT_REFS,
                        'default' => 15,
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
