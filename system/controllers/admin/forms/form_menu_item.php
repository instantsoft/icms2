<?php

class formAdminMenuItem extends cmsForm {

    public function init($menu_id, $current_id) {

        return [
            [
                'title' => LANG_CP_BASIC,
                'type'  => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_enabled', [
                        'title'   => LANG_IS_ENABLED,
                        'default' => 1
                    ]),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'is_clean_disable' => true,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'menu_items'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 64]
                        ]
                    ]),
                    new fieldHidden('menu_id', []),
                    new fieldList('parent_id', [
                        'title' => LANG_CP_MENU_ITEM_PARENT,
                        'generator' => function ($item) use ($menu_id, $current_id) {

                            $menu_model = cmsCore::getModel('menu');
                            $tree = $menu_model->getMenuItemsTree($menu_id, false);

                            $items = [0 => LANG_ROOT_NODE];

                            if ($tree) {
                                foreach ($tree as $tree_item) {
                                    if (!empty($current_id)) {
                                        if ($tree_item['id'] == $current_id) {
                                            continue;
                                        }
                                    }
                                    $items[$tree_item['id']] = str_repeat('- ', $tree_item['level']) . ' ' . $tree_item['title'];
                                }
                            }

                            return $items;
                        }
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_MENU_ITEM_ACTION,
                'childs' => [
                    new fieldString('url', [
                        'title' => LANG_CP_MENU_ITEM_ACTION_URL,
                        'hint'  => LANG_CP_MENU_ITEM_ACTION_URL_HINT,
                        'rules' => [
                            ['max_length', 255]
                        ]
                    ]),
                    new fieldList('options:target', [
                        'title' => LANG_CP_MENU_ITEM_ACTION_TARGET,
                        'items' => [
                            '_self'   => LANG_CP_MENU_ITEM_TARGET_SELF,
                            '_blank'  => LANG_CP_MENU_ITEM_TARGET_BLANK,
                            '_parent' => LANG_CP_MENU_ITEM_TARGET_PARENT,
                            '_top'    => LANG_CP_MENU_ITEM_TARGET_TOP,
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldString('options:class', [
                        'title' => LANG_CSS_CLASS,
                    ]),
                    new fieldString('options:icon', [
                        'title' => LANG_CP_MENU_ITEM_ICON,
                        'suffix' => '<a href="#" class="icms-icon-select" data-href="' . href_to('admin', 'settings', ['theme', cmsConfig::get('template'), 'icon_list']) . '"><span>' . LANG_CP_ICON_SELECT . '</span></a>',
                    ]),
                    new fieldCheckbox('options:hide_title', [
                        'title' => LANG_CP_MENU_ITEM_HIDE_TITLE,
                        'visible_depend' => ['options:icon' => ['hide' => ['']]]
                    ])
                ]
            ],
            'access' => [
                'type'   => 'fieldset',
                'title'  => LANG_PERMISSIONS,
                'childs' => [
                    new fieldListGroups('groups_view', [
                        'title'       => LANG_SHOW_TO_GROUPS,
                        'show_all'    => true,
                        'show_guests' => true
                    ]),
                    new fieldListGroups('groups_hide', [
                        'title'       => LANG_HIDE_FOR_GROUPS,
                        'show_all'    => false,
                        'show_guests' => true
                    ])
                ]
            ]
        ];
    }
}
