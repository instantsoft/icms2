<?php

class formBillingBalance extends cmsForm {

    public function init() {

        return [
            'target' => [
                'title'  => LANG_BILLING_CP_BAL_WHO,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('mode', [
                        'title' => LANG_BILLING_CP_BAL_MODE,
                        'items' => [
                            'user'  => LANG_BILLING_CP_BAL_MODE_USER,
                            'group' => LANG_BILLING_CP_BAL_MODE_GROUP,
                        ]
                    ]),
                    new fieldString('user_email', [
                        'title' => LANG_BILLING_CP_BAL_USER,
                        'hint'  => LANG_BILLING_CP_BAL_USER_HINT,
                        'autocomplete' => ['url' => href_to('admin', 'users', 'autocomplete')],
                        'rules' => [
                            ['email']
                        ],
                        'visible_depend' => ['mode' => ['show' => ['user']]]
                    ]),
                    new fieldList('group_id', [
                        'title'     => LANG_BILLING_CP_BAL_GROUP,
                        'generator' => function ($item) {

                            $groups = cmsCore::getModel('users')->getGroups(false);

                            $items = [0 => LANG_ALL];

                            foreach ($groups as $group) {
                                $items[$group['id']] = $group['title'];
                            }

                            return $items;
                        },
                        'visible_depend' => ['mode' => ['show' => ['group']]]
                    ])
                ]
            ],
            'amount' => [
                'title'  => LANG_BILLING_CP_BAL_MUCH,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('amount', [
                        'title' => LANG_BILLING_CP_BAL_AMOUNT,
                        'hint'  => LANG_BILLING_CP_BAL_HINT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('description', [
                        'title' => LANG_BILLING_CP_BAL_DESCRIPTION,
                        'hint'  => LANG_BILLING_CP_BAL_DESCRIPTION_HINT,
                        'rules' => [
                            ['max_length', 255]
                        ]
                    ])
                ]
            ]
        ];
    }

}
