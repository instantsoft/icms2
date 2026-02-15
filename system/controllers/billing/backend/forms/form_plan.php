<?php

class formBillingPlan extends cmsForm {

    public function init($do, $options) {

        $currency = html_spellcount_only(10, $options['currency']);

        return [
            'basic' => [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_BILLING_PLAN_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 128]
                        ]
                    ]),
                    new fieldString('description', [
                        'title' => LANG_BILLING_PLAN_DESC,
                        'rules' => [
                            ['max_length', 1024]
                        ]
                    ]),
                    new fieldNumber('max_out', [
                        'title' => LANG_BILLING_PLANS_MAX_OUT,
                        'hint'  => sprintf(LANG_BILLING_PLANS_MAX_OUT_HINT, $currency),
                        'options' => [
                            'is_abs' => true,
                            'save_zero' => false
                        ]
                    ]),
                    new fieldCheckbox('is_real_price', [
                        'title' => sprintf(LANG_BILLING_PLAN_IS_REAL_PRICE, $options['currency_real']),
                        'hint'  => sprintf(LANG_BILLING_PLAN_IS_REAL_PRICE_HINT, $currency, $options['currency_title'], $currency)
                    ]),
                    new fieldCheckbox('is_subscribe_after_reg', [
                        'title' => LANG_BILLING_PLAN_IS_SUBSCRIBE_AFTER_REG,
                        'rules' => [
                            [function ($controller, $data, $value) {

                                if (!$value) {
                                    return true;
                                }

                                if (!empty($data['id'])) {
                                    $controller->model->filterNotEqual('id', $data['id']);
                                }

                                $plans = $controller->model->getPlans();

                                foreach ($plans as $p) {
                                    if ($p['is_subscribe_after_reg']) {
                                        return ERR_VALIDATE_UNIQUE;
                                    }
                                }

                                return true;
                            }]
                        ]
                    ]),
                    new fieldCheckbox('is_enabled', [
                        'title' => LANG_BILLING_PLAN_IS_ENABLED
                    ])
                ]
            ],
            'groups' => [
                'title'  => LANG_BILLING_PLAN_GROUPS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldListGroups('groups', [
                        'hint'        => LANG_BILLING_PLAN_GROUPS_HINT,
                        'show_all'    => false,
                        'show_guests' => false
                    ])
                ]
            ],
            'features' => [
                'title'  => LANG_BILLING_PLAN_FEATURES,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldFieldsgroup('features', [
                        'add_title' => LANG_BILLING_PLAN_FEATURES_ADD,
                        'is_sortable' => false,
                        'childs' => [
                            new fieldList('type', [
                                'title' => LANG_BILLING_PLAN_FEATURES_TYPE,
                                'generator' => function ($item) use ($options) {
                                    $items = [];
                                    foreach ($options['plan_features']??[] as $f) {
                                        $items[$f['name']] = $f['title'] . ' (' . string_lang('LANG_BILLING_PLAN_FEATURES_TYPE_' . $f['type']) .')';
                                    }
                                    return $items;
                                },
                                'rules' => [
                                    ['required']
                                ]
                            ]),
                            new fieldString('value', [
                                'title' => LANG_BILLING_PLAN_FEATURES_TYPE_VALUE,
                                'rules' => [
                                    ['max_length', 1024]
                                ]
                            ])
                        ]
                    ])
                ]
            ],
            'prices' => [
                'title'    => sprintf(LANG_BILLING_PLAN_PRICES, $options['currency_title']),
                'type'     => 'fieldset',
                'childs'   => [
                    new fieldFieldsgroup('prices', [
                        'add_title' => LANG_BILLING_PLAN_PRICES_PRICE_ADD,
                        'hint' => sprintf(LANG_BILLING_PLAN_PRICE_HINT, $currency) . ' ' . sprintf(LANG_BILLING_PLAN_CASHBACK_HINT, $currency),
                        'rules' => [
                            [function ($controller, $data, $value) {

                                if (!empty($data['prices'])) {
                                    return true;
                                }

                                if (!empty($data['id'])) {
                                    $controller->model->filterNotEqual('id', $data['id']);
                                }

                                $plans = $controller->model->getPlans();

                                foreach ($plans as $p) {
                                    if (!$p['prices']) {
                                        return ERR_VALIDATE_UNIQUE;
                                    }
                                }

                                return true;
                            }]
                        ],
                        'childs' => [
                            new fieldNumber('length', [
                                'title' => LANG_BILLING_PLAN_PRICES_VAL,
                                'options' => [
                                    'is_abs'  => true,
                                    'is_ceil' => true
                                ],
                                'rules' => [
                                    ['required']
                                ]
                            ]),
                            new fieldList('int', [
                                'title' => LANG_BILLING_PLAN_PRICES_INT,
                                'items' => [
                                    'MINUTE' => LANG_MINUTE1,
                                    'HOUR'   => LANG_HOUR1,
                                    'DAY'    => LANG_DAY1,
                                    'WEEK'   => LANG_WEEK1,
                                    'MONTH'  => LANG_MONTH1,
                                    'YEAR'   => LANG_YEAR1
                                ]
                            ]),
                            new fieldNumber('amount', [
                                'title' => LANG_BILLING_PLAN_PRICES_PRICE,
                                'options' => [
                                    'is_abs'  => true
                                ],
                                'rules' => [
                                    ['required']
                                ]
                            ]),
                            new fieldNumber('cashback', [
                                'title' => LANG_BILLING_PLAN_PRICES_CASHBACK,
                                'options' => [
                                    'is_abs'  => true,
                                    'is_ceil' => true
                                ]
                            ])
                        ]
                    ])
                ]
            ]
        ];
    }

}
