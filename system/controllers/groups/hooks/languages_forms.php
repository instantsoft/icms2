<?php

class onGroupsLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_GROUPS,
            'forms' => [
                'field' => [
                    'title' => LANG_CP_CTYPE_FIELDS
                ]
            ]
        ];
    }

}
