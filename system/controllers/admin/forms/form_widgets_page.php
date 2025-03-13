<?php

class formAdminWidgetsPage extends cmsForm {

    protected $disabled_fields = ['fast_add_cat', 'fast_add_ctype', 'fast_add_type', 'fast_add_item', 'fast_add_into'];

    public function init() {

        return [
            'title' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_BASIC,
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 64]
                        ]
                    ]),
                    new fieldString('body_css', [
                        'title' => LANG_CP_WIDGET_PAGE_BODY_CSS,
                        'rules' => [
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldList('layout', [
                        'title'   => LANG_CP_WIDGET_PAGE_LAYOUT,
                        'generator' => function ($item) {
                            $layouts = cmsTemplate::getInstance()->getAvailableTemplatesFiles('', '*.tpl.php');
                            $items = ['' => LANG_BY_DEFAULT];
                            if ($layouts) {
                                foreach ($layouts as $layout) {
                                    if ($layout == 'admin') {
                                        continue;
                                    }
                                    $items[$layout] = string_lang('LANG_' . cmsConfig::get('template') . '_THEME_LAYOUT_' . $layout, $layout);
                                }
                            }
                            return $items;
                        }
                    ])
                ]
            ],
            'urls' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_WIDGET_PAGE_URLS,
                'childs' => [
                    new fieldText('url_mask', [
                        'title' => LANG_CP_WIDGET_PAGE_URL_MASK,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldText('url_mask_not', [
                        'title' => LANG_CP_WIDGET_PAGE_URL_MASK_NOT,
                    ])
                ]
            ],
            'fast_add' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_WIDGETS_FA,
                'childs' => [
                    new fieldList('fast_add_ctype', [
                        'title'      => LANG_CONTENT_TYPE,
                        'is_virtual' => true,
                        'generator'  => function ($item) {
                            foreach (cmsCore::getModel('content')->getContentTypes() ?: [] as $ctype) {
                                $items[$ctype['name']] = $ctype['title'];
                            }
                            return $items;
                        }
                    ]),
                    new fieldList('fast_add_type', [
                        'title'      => LANG_CP_WIDGETS_FA_TYPE,
                        'is_virtual' => true,
                        'items'      => [
                            'items' => LANG_CP_WIDGETS_FA_ITEMS,
                            'cats'  => LANG_CP_WIDGETS_FA_CATS
                        ]
                    ]),
                    new fieldString('fast_add_item', [
                        'title'          => LANG_CP_WIDGETS_FA_TITLE_OR_URL,
                        'is_virtual'     => true,
                        'autocomplete'   => [
                            'url' => href_to('admin', 'widgets', 'page_autocomplete')
                        ],
                        'visible_depend' => ['fast_add_type' => ['show' => ['items']]]
                    ]),
                    new fieldList('fast_add_cat', [
                        'title'          => LANG_CATEGORY,
                        'is_virtual'     => true,
                        'items'          => [],
                        'visible_depend' => ['fast_add_type' => ['show' => ['cats']]],
                        'parent'         => [
                            'list' => 'fast_add_ctype',
                            'url'  => href_to('admin', 'widgets', 'page_content_cats')
                        ]
                    ]),
                    new fieldList('fast_add_into', [
                        'title'      => LANG_CP_WIDGETS_FA_ADD_TO,
                        'is_virtual' => true,
                        'items'      => [
                            ''     => LANG_CP_WIDGETS_FA_TO_POS,
                            '_not' => LANG_CP_WIDGETS_FA_TO_NOT
                        ]
                    ])
                ]
            ],
            'access' => [
                'type'   => 'fieldset',
                'title'  => LANG_PERMISSIONS,
                'childs' => [
                    new fieldListGroups('groups:view', [
                        'title'       => LANG_SHOW_TO_GROUPS,
                        'show_all'    => true,
                        'show_guests' => true
                    ]),
                    new fieldListGroups('groups:hide', [
                        'title'       => LANG_HIDE_FOR_GROUPS,
                        'show_all'    => false,
                        'show_guests' => true
                    ])
                ]
            ]
        ];
    }

}
