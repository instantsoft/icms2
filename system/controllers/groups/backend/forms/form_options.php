<?php

class formGroupsOptions extends cmsForm {

    public function init() {

        return [

            'list' => [
                'type' => 'fieldset',
                'title' => LANG_GROUPS_LIST,
                'childs' => [

                    new fieldCheckbox('is_filter', [
                        'title' => LANG_CP_LISTVIEW_FILTER,
                    ])

                ]
            ],

            'view' => [
                'type' => 'fieldset',
                'title' => LANG_GROUPS_VIEW,
                'childs' => [

                    new fieldString('change_owner_email', [
                        'title' => LANG_GROUPS_OPT_CHANGE_OWNER_EMAIL,
                        'rules' => [
                            ['email']
                        ]
                    ])

                ]
            ],

            'limit' => [
                'type' => 'fieldset',
                'title' => LANG_LIST_LIMIT,
                'childs' => [

                    new fieldNumber('limit', [
                        'default' => 15,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ]
        ];
    }
}
