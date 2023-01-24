<?php

class formUsersTab extends cmsForm {

    public function init($do) {

        return [
            'basic' => [
                'type' => 'fieldset',
                'childs' => [

                    new fieldString('title', [
                        'title' => LANG_CP_TAB_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => '{users}_tabs'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),

                    new fieldCheckbox('is_active', [
                        'title' => LANG_CP_TAB_IS_ACTIVE
                    ]),

                    new fieldCheckbox('show_only_owner', [
                        'title' => LANG_CP_TAB_SHOW_ONLY_OWNER
                    ]),

                    new fieldListGroups('groups_view', [
                        'title' => LANG_SHOW_TO_GROUPS,
                        'show_all' => true,
                        'show_guests' => true
                    ]),

                    new fieldListGroups('groups_hide', [
                        'title' => LANG_HIDE_FOR_GROUPS,
                        'show_all' => false,
                        'show_guests' => true
                    ])
                ]
            ]
        ];
    }
}
