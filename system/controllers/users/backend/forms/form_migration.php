<?php

class formUsersMigration extends cmsForm {

    public function init($do) {

        $groups = array_collection_to_list(cmsCore::getModel('users')->getGroups(), 'id', 'title');

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_USERS_MIG_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 256]
                        ]
                    ]),
                    new fieldCheckbox('is_active', [
                        'title' => LANG_USERS_MIG_IS_ACTIVE,
                    ]),
                    new fieldList('is_keep_group', [
                        'title' => LANG_USERS_MIG_ACTION,
                        'items' => [
                            0 => LANG_USERS_MIG_ACTION_CHANGE,
                            1 => LANG_USERS_MIG_ACTION_ADD
                        ]
                    ]),
                    new fieldList('group_from_id', [
                        'title' => LANG_USERS_MIG_FROM,
                        'items' => $groups
                    ]),
                    new fieldList('group_to_id', [
                        'title' => LANG_USERS_MIG_TO,
                        'items' => $groups
                    ]),
                    new fieldCheckbox('is_passed', [
                        'title' => LANG_USERS_MIG_COND_DATE,
                    ]),
                    new fieldList('passed_from', [
                        'title' => LANG_USERS_MIG_PASSED_FROM,
                        'items' => [
                            0 => LANG_USERS_MIG_PASSED_REG,
                            1 => LANG_USERS_MIG_PASSED_MIG
                        ],
                        'visible_depend' => ['is_passed' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('passed_days', [
                        'title' => LANG_USERS_MIG_PASSED,
                        'visible_depend' => ['is_passed' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_rating', [
                        'title' => LANG_USERS_MIG_COND_RATING,
                    ]),
                    new fieldNumber('rating', [
                        'title' => LANG_USERS_MIG_RATING,
                        'visible_depend' => ['is_rating' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_karma', [
                        'title' => LANG_USERS_MIG_COND_KARMA,
                    ]),
                    new fieldNumber('karma', [
                        'title' => LANG_USERS_MIG_KARMA,
                        'visible_depend' => ['is_karma' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_notify', [
                        'title' => LANG_USERS_MIG_NOTIFY,
                    ]),
                    new fieldHtml('notify_text', [
                        'title' => LANG_USERS_MIG_NOTIFY_TEXT,
                        'visible_depend' => ['is_notify' => ['show' => ['1']]]
                    ])
                ]
            ]
        ];
    }

}
