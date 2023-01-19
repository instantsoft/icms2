<?php
class formAdminUsersGroup extends cmsForm {

    public function init($do) {

        return [
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            ['max_length', 32],
                            $do == 'add' ? ['unique', '{users}_groups', 'name'] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => '{users}_groups'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 32]
                        ]
                    ])
                ]
            ],
            [
                'type' => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_public', [
                        'title' => LANG_CP_USER_GROUP_IS_PUBLIC
                    ]),
                    new fieldCheckbox('is_filter', [
                        'title' => LANG_CP_USER_GROUP_IS_FILTER
                    ])
                ]
            ]
        ];
    }
}
