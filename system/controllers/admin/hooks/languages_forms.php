<?php

class onAdminLanguagesForms extends cmsAction {

    public function run(){

        return [
            'title' => LANG_ADMIN_CONTROLLER,
            'forms' => [
                'settings' => [
                    'title' => LANG_CP_SECTION_SETTINGS
                ],
                'menu_item' => [
                    'title' => LANG_CP_SECTION_MENU
                ],
                'users_group' => [
                    'title' => LANG_USERS.' / '.LANG_USER_GROUP
                ],
                'ctypes_field' => [
                    'title' => LANG_CONTENT_TYPE.' / '.LANG_CP_CTYPE_FIELDS
                ],
                'ctypes_dataset' => [
                    'title' => LANG_CONTENT_TYPE.' / '.LANG_CP_CTYPE_DATASETS
                ],
                'ctypes_basic' => [
                    'title' => LANG_CONTENT_TYPE
                ],
                'ctypes_labels' => [
                    'title' => LANG_CONTENT_TYPE.' / '.LANG_CP_CTYPE_LABELS
                ]
            ]
        ];
    }

}
