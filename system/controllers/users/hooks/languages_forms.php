<?php

class onUsersLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_USERS,
            'forms' => [
                'field' => [
                    'title' => LANG_CP_CTYPE_FIELDS
                ],
                'tab' => [
                    'title' => LANG_USERS_CFG_TABS
                ]
            ]
        ];
    }

}
