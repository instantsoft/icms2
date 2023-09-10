<?php
class formAdminCtypesBasic extends cmsForm {

    public function init($do, $ctype) {

        $template = new cmsTemplate(cmsConfig::get('template'));

        $meta_ctype_fields = [
            'ctype_title'       => LANG_CONTENT_TYPE . ': ' . LANG_TITLE,
            'ctype_description' => LANG_CONTENT_TYPE . ': ' . LANG_DESCRIPTION,
            'ctype_label1'      => LANG_CP_NUMERALS_1_LABEL,
            'ctype_label2'      => LANG_CP_NUMERALS_2_LABEL,
            'ctype_label10'     => LANG_CP_NUMERALS_10_LABEL,
            'filter_string'     => LANG_FILTERS
        ];

        $meta_item_fields = [
            'title'             => LANG_TITLE,
            'description'       => LANG_DESCRIPTION,
            'ctype_title'       => LANG_CONTENT_TYPE . ': ' . LANG_TITLE,
            'ctype_description' => LANG_CONTENT_TYPE . ': ' . LANG_DESCRIPTION,
            'ctype_label1'      => LANG_CP_NUMERALS_1_LABEL,
            'ctype_label2'      => LANG_CP_NUMERALS_2_LABEL,
            'ctype_label10'     => LANG_CP_NUMERALS_10_LABEL,
            'filter_string'     => LANG_FILTERS
        ];

        $item_fields = [
            'category'   => LANG_CATEGORY,
            'hits_count' => LANG_HITS,
            'comments'   => LANG_COMMENTS,
            'rating'     => LANG_RATING,
            'tags'       => LANG_TAGS
        ];

        if (!empty($ctype['name'])) {

            $_item_fields = cmsCore::getModel('content')->orderBy('ordering')->getContentFields($ctype['name']);

            foreach ($_item_fields as $field) {
                $item_fields[$field['name']] = $field['title'];
            }
        }

        $this->setData('meta_item_fields', $meta_item_fields);
        $this->setData('meta_ctype_fields', $meta_ctype_fields);
        $this->setData('item_fields', $item_fields);

        return [
            'titles' => [
                'title' => LANG_BASIC_OPTIONS,
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'hint' => LANG_CP_SYSTEM_NAME_HINT,
                        'options' => [
                            'max_length' => 32,
                            'show_symbol_count' => true
                        ],
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            $do == 'add' ? ['unique', 'content_types', 'name'] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_types'
                        ],
                        'options' => [
                            'max_length' => 100,
                            'show_symbol_count' => true
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldHtml('description', [
                        'title' => LANG_DESCRIPTION,
                        'can_multilanguage' => true,
                        'store_via_html_filter' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_types'
                        ]
                    ]),
                ]
            ],
            'pub' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_PUBLICATION,
                'childs' => [
                    new fieldCheckbox('is_date_range', [
                        'title' => LANG_CP_IS_PUB_CONTROL,
                        'hint' => LANG_CP_IS_PUB_CONTROL_HINT
                    ]),
                    new fieldList('options:is_date_range_process', [
                        'title' => LANG_CP_IS_PUB_CONTROL_PROCESS,
                        'default' => 'hide',
                        'items' => [
                            'hide'      => LANG_CP_IS_PUB_CONTROL_PROCESS_HIDE,
                            'delete'    => LANG_CP_IS_PUB_CONTROL_PROCESS_DEL,
                            'in_basket' => LANG_BASKET_DELETE
                        ],
                        'visible_depend' => ['is_date_range' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('options:notify_end_date_days', [
                        'title' => LANG_CP_NOTIFY_END_DATE_DAYS,
                        'units' => LANG_DAYS,
                        'default' => 1,
                        'visible_depend' => ['is_date_range' => ['show' => ['1']]]
                    ]),
                    new fieldString('options:notify_end_date_notice', [
                        'title' => LANG_MESSAGE,
                        'multilanguage' => true,
                        'is_clean_disable' => true,
                        'default' => 'Через %s публикация вашего контента <a href="%s">%s</a> будет прекращена.',
                        'visible_depend' => ['is_date_range' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:disable_drafts', [
                        'title' => LANG_CP_DISABLE_DRAFTS
                    ])
                ]
            ],
            'categories' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CATEGORIES,
                'childs' => [
                    new fieldCheckbox('is_cats', [
                        'title' => LANG_CP_CATEGORIES_ON
                    ]),
                    new fieldCheckbox('is_cats_recursive', [
                        'title' => LANG_CP_CATEGORIES_RECURSIVE,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_empty_root', [
                        'title' => LANG_CP_CATEGORIES_EMPTY_ROOT,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_cats_multi', [
                        'title' => LANG_CP_CATEGORIES_MULTI,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_cats_change', [
                        'title' => LANG_CP_CATEGORIES_CHANGE,
                        'default' => true,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_cats_open_root', [
                        'title' => LANG_CP_CATEGORIES_OPEN_ROOT,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_cats_only_last', [
                        'title' => LANG_CP_CATEGORIES_ONLY_LAST,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_show_cats', [
                        'title' => LANG_CP_CATEGORIES_SHOW,
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldListMultiple('options:cover_sizes', [
                        'title' => LANG_CP_CAT_COVER_SIZES,
                        'default' => [],
                        'generator' => function () {
                            $presets = cmsCore::getModel('images')->getPresetsList();
                            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                            return $presets;
                        },
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:context_list_cover_sizes', [
                        'title'        => LANG_CP_CAT_CONTEXT_LIST_COVER_SIZES,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'generator' => function ($ctype) use ($template) {
                            return $template->getAvailableContentListStyles();
                        },
                        'values_generator' => function () {
                            $presets = cmsCore::getModel('images')->getPresetsList();
                            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                            return $presets;
                        },
                        'visible_depend' => ['is_cats' => ['show' => ['1']]]
                    ])
                ]
            ],
            'folders' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_FOLDERS,
                'childs' => [
                    new fieldCheckbox('is_folders', [
                        'title' => LANG_CP_FOLDERS_ON,
                        'hint' => LANG_CP_FOLDERS_HINT
                    ])
                ]
            ],
            'listview' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_LISTVIEW_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:list_off_breadcrumb', [
                        'title' => LANG_CP_LIST_OFF_BREADCRUMB
                    ]),
                    new fieldCheckbox('options:list_off_breadcrumb_ctype', [
                        'title' => LANG_CP_LIST_OFF_BREADCRUMB_CTYPE,
                        'visible_depend' => ['options:list_off_breadcrumb' => ['show' => ['0']]]
                    ]),
                    new fieldCheckbox('options:list_on', [
                        'title' => LANG_CP_LISTVIEW_ON,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:list_off_index', [
                        'title' => LANG_CP_LISTVIEW_OFF_INDEX,
                        'hint'  => LANG_CP_LISTVIEW_OFF_INDEX_HINT,
                        'default' => false,
                        'visible_depend' => ['options:list_on' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:profile_on', [
                        'title' => LANG_CP_PROFILELIST_ON,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:list_show_filter', [
                        'title' => LANG_CP_LISTVIEW_FILTER
                    ]),
                    new fieldCheckbox('options:list_expand_filter', [
                        'title' => LANG_CP_LISTVIEW_FILTER_EXPAND,
                        'visible_depend' => ['options:list_show_filter' => ['show' => ['1']]]
                    ]),
                    new fieldList('options:privacy_type', [
                        'title'   => LANG_CP_PRIVACY_TYPE,
                        'default' => 'hide',
                        'items'   => [
                            'hide'       => LANG_CP_PRIVACY_TYPE_HIDE,
                            'show_title' => LANG_CP_PRIVACY_TYPE_SHOW_TITLE,
                            'show_all'   => LANG_CP_PRIVACY_TYPE_SHOW_ALL
                        ]
                    ]),
                    new fieldNumber('options:limit', [
                        'title' => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldList('options:list_style', [
                        'title' => LANG_CP_LISTVIEW_STYLE,
                        'is_chosen_multiple' => true,
                        'hint' => sprintf(LANG_CP_LISTVIEW_STYLE_HINT, $template->getName()),
                        'generator' => function () use ($template) {
                            return $template->getAvailableContentListStyles();
                        }
                    ]),
                    new fieldList('options:list_style_options', [
                        'title'        => LANG_CP_LIST_STYLE_OPTIONS,
                        'hint'         => LANG_CP_LIST_STYLE_OPTIONS_HINT,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'multiple_keys' => [
                            'name' => 'field', 'value' => 'field_value'
                        ],
                        'generator' => function ($ctype) use ($template) {
                            return $template->getAvailableContentListStyles();
                        }
                    ]),
                    new fieldList('options:list_style_names', [
                        'title'        => LANG_CP_LIST_STYLE_NAMES,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'multiple_keys' => [
                            'name' => 'field', 'value' => 'field_value'
                        ],
                        'generator' => function ($ctype) use ($template) {
                            return $template->getAvailableContentListStyles();
                        }
                    ]),
                    new fieldList('options:context_list_style', [
                        'title'        => LANG_CP_CONTEXT_LIST_STYLE,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'generator' => function ($item) use ($do, $ctype) {

                            $lists = cmsEventsManager::hookAll('ctype_lists_context', 'template' . ($do != 'add' ? ':' . $ctype['name'] : ''));

                            $items = [];

                            if ($lists) {
                                foreach ($lists as $list) {
                                    $items = array_merge($items, $list);
                                }
                            }

                            return $items;
                        },
                        'values_generator' => function () use ($template) {
                            return $template->getAvailableContentListStyles();
                        }
                    ])
                ]
            ],
            'itemview' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_ITEMVIEW_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:item_off_breadcrumb', [
                        'title' => LANG_CP_LIST_OFF_BREADCRUMB
                    ]),
                    new fieldCheckbox('options:item_on', [
                        'title' => LANG_CP_ITEMVIEW_ON,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:is_show_fields_group', [
                        'title' => LANG_CP_ITEMVIEW_FIELDS_GROUP,
                        'visible_depend' => ['options:item_on' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:hits_on', [
                        'title' => LANG_CP_ITEMVIEW_HITS_ON,
                        'visible_depend' => ['options:item_on' => ['show' => ['1']]]
                    ]),
                    new fieldListGroups('options:hits_groups_view', [
                        'title' => LANG_CP_ITEMVIEW_HITS_GROUPS_VIEW,
                        'show_all' => true,
                        'visible_depend' => ['options:item_on' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:disable_info_block', [
                        'title' => LANG_CP_ITEMVIEW_OFF_INFO_BLOCK,
                        'visible_depend' => ['options:item_on' => ['show' => ['1']]]
                    ]),
                    new fieldText('options:share_code', [
                        'title' => LANG_CP_ITEMVIEW_SHARE_CODE,
                        'visible_depend' => ['options:item_on' => ['show' => ['1']]]
                    ]),
                    new fieldText('item_append_html', [
                        'title' => LANG_CP_ITEMVIEW_APPEND_HTML,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_types'
                        ],
                        'hint' => LANG_CP_ITEMVIEW_APPEND_HTML_HINT,
                        'visible_depend' => ['options:item_on' => ['show' => ['1']]]
                    ]),
                ]
            ],
            'seo-items' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_SEOMETA,
                'childs' => [
                    new fieldCheckbox('options:is_manual_title', [
                        'title' => LANG_CP_SEOMETA_MANUAL_TITLE,
                    ]),
                    new fieldCheckbox('is_auto_keys', [
                        'title' => LANG_CP_SEOMETA_AUTO_KEYS,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_auto_desc', [
                        'title' => LANG_CP_SEOMETA_AUTO_DESC,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_auto_url', [
                        'title' => LANG_CP_AUTO_URL,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_fixed_url', [
                        'title' => LANG_CP_FIXED_URL
                    ]),
                    new fieldString('url_pattern', [
                        'title' => LANG_CP_URL_PATTERN,
                        'prefix' => '/' . (!empty($ctype['name']) ? $ctype['name'] : '') . '/',
                        'suffix' => '.html',
                        'default' => '{id}-{title}',
                        'options' => [
                            'max_length' => 255,
                            'show_symbol_count' => true
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('options:seo_title_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns' =>  $item_fields
                        ]
                    ]),
                    new fieldString('options:seo_keys_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'can_multilanguage' => true,
                        'default' => '{content|string_get_meta_keywords}',
                        'patterns_hint' => [
                            'patterns' =>  $item_fields
                        ]
                    ]),
                    new fieldString('options:seo_desc_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'can_multilanguage' => true,
                        'default' => '{content|string_get_meta_description}',
                        'patterns_hint' => [
                            'patterns' =>  $item_fields
                        ]
                    ])
                ]
            ],
            'seo-cats' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_SEOMETA_CATS,
                'childs' => [
                    new fieldCheckbox('options:is_cats_title', [
                        'title' => LANG_CP_SEOMETA_CATS_TITLE
                    ]),
                    new fieldCheckbox('options:is_cats_h1', [
                        'title' => LANG_CP_SEOMETA_CATS_H1
                    ]),
                    new fieldCheckbox('options:is_cats_keys', [
                        'title' => LANG_CP_SEOMETA_CATS_KEYS
                    ]),
                    new fieldCheckbox('options:is_cats_desc', [
                        'title' => LANG_CP_SEOMETA_CATS_DESC
                    ]),
                    new fieldCheckbox('options:is_cats_auto_url', [
                        'title' => LANG_CP_CATS_AUTO_URL,
                        'default' => true
                    ]),
                    new fieldCheckbox('options:is_cats_first_level_slug', [
                        'title' => LANG_CP_CATS_FIRST_LEVEL_SLUG,
                        'default' => false
                    ]),
                    new fieldString('options:seo_cat_h1_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns' =>  $meta_item_fields
                        ]
                    ]),
                    new fieldString('options:seo_cat_title_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns' =>  $meta_item_fields
                        ]
                    ]),
                    new fieldString('options:seo_cat_keys_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns' =>  $meta_item_fields
                        ]
                    ]),
                    new fieldString('options:seo_cat_desc_pattern', [
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns' =>  $meta_item_fields
                        ]
                    ])
                ]
            ],
            'seo' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_SEOMETA_DEFAULT,
                'childs' => [
                    new fieldString('options:seo_ctype_h1_pattern', [
                        'can_multilanguage' => true,
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'patterns_hint' => [
                            'patterns' =>  $meta_ctype_fields
                        ]
                    ]),
                    new fieldString('seo_title', [
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_types'
                        ],
                        'patterns_hint' => [
                            'patterns' =>  $meta_ctype_fields
                        ],
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldString('seo_keys', [
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_types'
                        ],
                        'patterns_hint' => [
                            'patterns' =>  $meta_ctype_fields
                        ],
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldText('seo_desc', [
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_types'
                        ],
                        'is_strip_tags' => true,
                        'patterns_hint' => [
                            'patterns' =>  $meta_ctype_fields
                        ],
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ])
                ]
            ],
            'collapsed' => [
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_IS_COLLAPSED,
                'childs' => [
                    new fieldListMultiple('options:is_collapsed', [
                        'generator' => function ($item) use ($do, $ctype) {

                            $items = [
                                'folder' => LANG_CP_FOLDERS,
                                'group_wrap' => LANG_CP_CT_GROUPS
                            ];

                            if ($do !== 'add') {

                                $model = cmsCore::getModel('content');

                                $fieldset_titles = $model->orderBy('ordering')->getContentFieldsets($ctype['id']);

                                if ($fieldset_titles) {
                                    foreach ($fieldset_titles as $fieldset) {
                                        $items[md5($fieldset)] = $fieldset;
                                    }
                                }
                            }

                            return $items + [
                                'tags_wrap'    => LANG_TAGS,
                                'privacy_wrap' => LANG_CP_FIELD_PRIVACY,
                                'is_comment'   => LANG_CP_COMMENTS,
                                'seo_wrap'     => LANG_SEO,
                                'pub_wrap'     => LANG_CP_PUBLICATION,
                            ];
                        }
                    ])
                ]
            ]
        ];
    }
}
