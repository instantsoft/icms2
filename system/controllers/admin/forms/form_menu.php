<?php

class formAdminMenu extends cmsForm {

    public function init($do) {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            $do === 'add' ? ['unique', 'menu', 'name'] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'menu'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 64]
                        ]
                    ])
                ]
            ]
        ];
    }
}
