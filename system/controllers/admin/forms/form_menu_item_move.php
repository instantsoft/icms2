<?php

class formAdminMenuItemMOve extends cmsForm {

    public function init() {

        return [
            [
                'type'  => 'fieldset',
                'childs' => [
                    new fieldHidden('items', [
                        'rules' => [
                            ['required'],
                            ['regexp', '/^([0-9,]+)$/u']
                        ]
                    ]),
                    new fieldList('menu_id', [
                        'title' => LANG_CP_MENU_MOVE,
                        'generator' => function ($item) {

                            $items = cmsCore::getModel('menu')->getMenus();

                            return ['' => ''] + array_column($items, 'title', 'id');
                        },
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ]
        ];
    }
}
