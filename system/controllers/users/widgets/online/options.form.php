<?php

class formWidgetUsersOnlineOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:is_avatars', [
                        'title' => LANG_WD_USERS_ONLINE_AVATARS
                    ]),
                    new fieldListGroups('options:groups', [
                        'title' => LANG_WD_USERS_ONLINE_GROUPS
                    ]),
                    new fieldListGroups('options:groups_hide', [
                        'title' => LANG_WD_USERS_ONLINE_NO_GROUPS
                    ])
                ]
            ]
        ];
    }

}
