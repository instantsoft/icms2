<?php

class formMessagesPmailing extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_PM_PMAILING_GROUPS,
                'childs' => [
                    new fieldListGroups('groups', [
                        'show_all' => true
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_FILTERS,
                'childs' => [
                    new fieldList('filters', [
                        'add_title'    => LANG_FILTER_ADD,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'single_select' => 1,
                        'select_title' => LANG_FILTER_FIELD,
                        'multiple_keys' => [
                            'field' => 'field', 'condition' => 'field_select', 'value' => 'field_value'
                        ],
                        'generator' => function () {

                            $items = [];

                            $items['is_admin'] = [
                                'title' => 'is_admin',
                                'data'  => [
                                    'ns' => 'int'
                                ]
                            ];
                            $items['date_reg'] = [
                                'title' => 'date_reg',
                                'data'  => [
                                    'ns' => 'date'
                                ]
                            ];
                            $items['date_log'] = [
                                'title' => 'date_log',
                                'data'  => [
                                    'ns' => 'date'
                                ]
                            ];

                            return $items;
                        },
                        'value_items' => [
                            'int'  => [
                                'eq' => '=',
                                'gt' => '&gt;',
                                'lt' => '&lt;',
                                'ge' => '&ge;',
                                'le' => '&le;',
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ],
                            'str'  => [
                                'eq' => '=',
                                'lk' => LANG_FILTER_LIKE,
                                'ln' => LANG_FILTER_NOT_LIKE,
                                'lb' => LANG_FILTER_LIKE_BEGIN,
                                'lf' => LANG_FILTER_LIKE_END,
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ],
                            'date'  => [
                                'eq' => '=',
                                'gt' => '&gt;',
                                'lt' => '&lt;',
                                'ge' => '&ge;',
                                'le' => '&le;',
                                'dy' => LANG_FILTER_DATE_YOUNGER,
                                'do' => LANG_FILTER_DATE_OLDER,
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ]
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_PM_PMAILING_MSG,
                'childs' => [
                    new fieldHtml('message_text', [
                        'title'   => LANG_MESSAGE,
                        'hint'    => LANG_PM_PMAILING_TYPOGRAPH,
                        'options' => ['editor' => 'ace'],
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldList('typograph_id', [
                        'title'     => LANG_PARSER_TYPOGRAPH,
                        'generator' => function ($item) {
                            $items   = [];
                            $presets = (new cmsModel())->get('typograph_presets') ?: [];
                            foreach ($presets as $preset) {
                                $items[$preset['id']] = $preset['title'];
                            }
                            return $items;
                        },
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('type', [
                        'title' => LANG_PM_PMAILING_TYPE,
                        'items' => [
                            'notify'  => LANG_PM_PMAILING_TYPE_NOTIFY,
                            'message' => LANG_PM_PMAILING_TYPE_MESSAGE,
                            'email'   => LANG_PM_PMAILING_TYPE_EMAIL
                        ]
                    ]),
                    new fieldString('sender_user_email', [
                        'title'        => LANG_PM_SENDER_USER_ID,
                        'hint'         => LANG_PM_SENDER_USER_ID_HINT,
                        'autocomplete' => ['url' => href_to('admin', 'users', 'autocomplete')],
                        'rules'        => [
                            ['email']
                        ],
                        'visible_depend' => ['type' => ['show' => ['message']]]
                    ]),
                    new fieldCheckbox('is_br', [
                        'title' => LANG_PM_PMAILING_IS_BR
                    ])
                ]
            ]
        ];
    }

}
