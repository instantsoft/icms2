<?php

class formBillingPayout extends cmsForm {

    public function init() {

        $content_model = cmsCore::getModel('content')->setTablePrefix('');

        $fields      = $content_model->getContentFields('{users}');
        $fields_list = ['' => ''] + array_collection_to_list($fields, 'name', 'title');

        return [
            'basic' => [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_BILLING_CP_PO_TITLE,
                        'hint'  => LANG_BILLING_CP_PO_TITLE_HINT,
                        'rules' => [
                            ['required'],
                            ['max_length', 128]
                        ]
                    ]),
                    new fieldCheckbox('is_enabled', [
                        'title' => LANG_BILLING_CP_PO_ENABLED
                    ]),
                    new fieldNumber('period', [
                        'title' => LANG_BILLING_CP_PO_PERIOD,
                        'units' => LANG_DAY10,
                        'rules' => [
                            ['required']
                        ],
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldListGroups('groups', [
                        'title'    => LANG_BILLING_CP_PO_GROUPS,
                        'show_all' => true
                    ]),
                    new fieldNumber('user_id', [
                        'title' => LANG_BILLING_CP_PO_USER_ID,
                        'hint'  => LANG_BILLING_CP_PO_USER_ID_HINT,
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldString('amount', [
                        'title' => LANG_BILLING_CP_PO_AMOUNT,
                        'hint'  => LANG_BILLING_CP_PO_AMOUNT_HINT
                    ]),
                    new fieldCheckbox('is_topup_balance', [
                        'title' => LANG_BILLING_CP_IS_TOPUP_BALANCE,
                        'hint'  => LANG_BILLING_CP_IS_TOPUP_BALANCE_HINT
                    ]),
                    new fieldList('field_amount', [
                        'title' => LANG_BILLING_CP_PO_AMOUNT_FIELD,
                        'hint'  => LANG_BILLING_CP_PO_AMOUNT_FIELD_HINT,
                        'items' => $fields_list
                    ])
                ]
            ],
            'restrictions' => [
                'title'  => LANG_BILLING_CP_PO_REST,
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldCheckbox('is_passed', [
                        'title' => LANG_BILLING_CP_PO_IS_PASSED
                    ]),
                    new fieldNumber('passed_days', [
                        'title'   => LANG_BILLING_CP_PO_PASSED,
                        'units'   => LANG_DAY10,
                        'default' => 0,
                        'visible_depend' => ['is_passed' => ['show' => ['1']]],
                        'options' => [
                            'is_abs' => true,
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldCheckbox('is_karma', [
                        'title' => LANG_BILLING_CP_PO_IS_KARMA
                    ]),
                    new fieldNumber('karma', [
                        'title'   => LANG_BILLING_CP_PO_KARMA,
                        'units'   => LANG_UNIT10,
                        'default' => 0,
                        'visible_depend' => ['is_karma' => ['show' => ['1']]],
                        'options' => [
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldCheckbox('is_rating', [
                        'title' => LANG_BILLING_CP_PO_IS_RATING
                    ]),
                    new fieldNumber('rating', [
                        'title'   => LANG_BILLING_CP_PO_RATING,
                        'units'   => LANG_UNIT10,
                        'default' => 0,
                        'visible_depend' => ['is_rating' => ['show' => ['1']]],
                        'options' => [
                            'is_ceil' => true
                        ]
                    ]),
                    new fieldCheckbox('is_field', [
                        'title' => LANG_BILLING_CP_PO_IS_FIELD,
                    ]),
                    new fieldList('field', [
                        'title' => LANG_BILLING_CP_PO_FIELD,
                        'items' => $fields_list,
                        'visible_depend' => ['is_field' => ['show' => ['1']]]
                    ]),
                    new fieldString('field_value', [
                        'title' => LANG_BILLING_CP_PO_FIELD_VALUE,
                        'visible_depend' => ['is_field' => ['show' => ['1']]]
                    ])
                )
            ]
        ];
    }

}
