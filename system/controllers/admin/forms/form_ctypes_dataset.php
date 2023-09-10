<?php

class formAdminCtypesDataset extends cmsForm {

    public function init($do, $ctype, $cats_list, $fields_list, $dataset = []) {

        $ctype_id = (!empty($ctype['id']) ? $ctype['id'] : $ctype['name']);

        $lists = cmsEventsManager::hookAll('ctype_lists_context', 'dataset:' . $ctype['name']);

        $ds_lists = [];

        if ($lists) {
            foreach ($lists as $list) {
                $ds_lists = array_merge($ds_lists, $list);
            }
        }

        $name_rules = [['required'], ['sysname']];
        if ($do == 'add') {
            $name_rules[] = ['unique_ctype_dataset', $ctype_id, false];
        } else {
            $name_rules[] = ['unique_ctype_dataset', $ctype_id, $dataset['id']];
        }

        // Значит не тип контента
        if (!is_numeric($ctype_id)) {

            $name_rules[] = ['unique', $ctype_id, 'slug'];

            $meta_item_fields = [];
        } else {

            $meta_item_fields = [
                'title'             => LANG_TITLE,
                'description'       => LANG_DESCRIPTION,
                'ds_title'          => LANG_CP_DATASET . ': ' . LANG_TITLE,
                'ds_description'    => LANG_CP_DATASET . ': ' . LANG_DESCRIPTION,
                'ctype_title'       => LANG_CONTENT_TYPE . ': ' . LANG_TITLE,
                'ctype_description' => LANG_CONTENT_TYPE . ': ' . LANG_DESCRIPTION,
                'ctype_label1'      => LANG_CP_NUMERALS_1_LABEL,
                'ctype_label2'      => LANG_CP_NUMERALS_2_LABEL,
                'ctype_label10'     => LANG_CP_NUMERALS_10_LABEL,
                'filter_string'     => LANG_FILTERS
            ];
        }

        $form = [
            'basic' => [
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => $name_rules
                    ]),
                    new fieldString('title', [
                        'title' => LANG_CP_DATASET_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_datasets'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldHtml('description', [
                        'title' => LANG_DESCRIPTION,
                        'store_via_html_filter' => true,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_datasets'
                        ]
                    ]),
                    new fieldNumber('max_count', [
                        'title' => LANG_LIST_LIMIT,
                        'default' => 0,
                        'rules' => [
                            ['max', 65535]
                        ]
                    ]),
                    new fieldCheckbox('is_visible', [
                        'title' => LANG_CP_DATASET_IS_VISIBLE,
                        'default' => true
                    ])
                ]
            ],
            'sorting' => [
                'title'  => LANG_SORTING,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('sorting', [
                        'add_title'    => LANG_SORTING_ADD,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_SORTING_FIELD,
                        'multiple_keys' => [
                            'by' => 'field', 'to' => 'field_select'
                        ],
                        'generator' => function () use ($fields_list) {

                            $items = [];

                            if ($fields_list) {
                                foreach ($fields_list as $field) {
                                    $items[$field['value']] = $field['title'];
                                }
                            }

                            return $items;
                        },
                        'value_items' => [
                            'asc'  => LANG_SORTING_ASC,
                            'desc' => LANG_SORTING_DESC
                        ]
                    ])
                ]
            ],
            'filter' => [
                'title'  => LANG_FILTERS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('filters', [
                        'add_title'    => LANG_FILTER_ADD,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'single_select' => 0,
                        'select_title' => LANG_FILTER_FIELD,
                        'multiple_keys' => [
                            'field' => 'field', 'condition' => 'field_select', 'value' => 'field_value'
                        ],
                        'generator' => function () use ($fields_list) {

                            $items = [];

                            if ($fields_list) {
                                foreach ($fields_list as $field) {
                                    $items[$field['value']] = [
                                        'title' => $field['title'],
                                        'data'  => [
                                            'ns' => $field['type']
                                        ]
                                    ];
                                }
                            }

                            return $items;
                        },
                        'value_items' => [
                            'int'  => [
                                'eq' => '=',
                                'gt' => '&gt;',
                                'lt' => '&lt;',
                                'ge' => '&ge;',
                                'le' => '&le;',
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ],
                            'str'  => [
                                'eq' => '=',
                                'lk' => LANG_FILTER_LIKE,
                                'ln' => LANG_FILTER_NOT_LIKE,
                                'lb' => LANG_FILTER_LIKE_BEGIN,
                                'lf' => LANG_FILTER_LIKE_END,
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ],
                            'date'  => [
                                'eq' => '=',
                                'gt' => '&gt;',
                                'lt' => '&lt;',
                                'ge' => '&ge;',
                                'le' => '&le;',
                                'dy' => LANG_FILTER_DATE_YOUNGER,
                                'do' => LANG_FILTER_DATE_OLDER,
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ]
                        ]
                    ])
                ]
            ],
            'seo' => [
                'title' => LANG_SEO,
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('seo_h1', [
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_datasets'
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_h1_pattern']) ? $ctype['options']['seo_cat_h1_pattern'] : null),
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldString('seo_title', [
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_datasets'
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_title_pattern']) ? $ctype['options']['seo_cat_title_pattern'] : null),
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldString('seo_keys', [
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_datasets'
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_keys_pattern']) ? $ctype['options']['seo_cat_keys_pattern'] : null),
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldText('seo_desc', [
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_datasets'
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_desc_pattern']) ? $ctype['options']['seo_cat_desc_pattern'] : null),
                        'is_strip_tags' => true,
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ])
                ]
            ],
            'gv' => [
                'title' => LANG_SHOW_TO_GROUPS,
                'type' => 'fieldset',
                'childs' => [
                    new fieldListGroups('groups_view', [
                        'show_all' => true,
                        'show_guests' => true
                    ])
                ]
            ],
            'gh' => [
                'title' => LANG_HIDE_FOR_GROUPS,
                'type' => 'fieldset',
                'childs' => [
                    new fieldListGroups('groups_hide', [
                        'show_all' => false,
                        'show_guests' => true
                    ])
                ]
            ],
            'list_show' => [
                'title' => LANG_CP_FIELD_IN_LIST_CONTEXT,
                'type' => 'fieldset',
                'childs' => [
                    new fieldList('list:show', [
                        'is_chosen_multiple' => true,
                        'items' => $ds_lists
                    ]),
                ]
            ],
            'list_hide' => [
                'title' => LANG_CP_FIELD_NOT_IN_LIST_CONTEXT,
                'type' => 'fieldset',
                'childs' => [
                    new fieldList('list:hide', [
                        'is_chosen_multiple' => true,
                        'items' => $ds_lists
                    ]),
                ]
            ]
        ];

        if (!empty($ctype['is_cats']) && $cats_list) {
            $form['cv'] = [
                'title' => LANG_CP_CATS_VIEW,
                'type' => 'fieldset',
                'childs' => [
                    new fieldList('cats_view', [
                        'is_chosen_multiple' => true,
                        'items' => $cats_list
                    ]),
                ]
            ];
            $form['ch'] = [
                'title' => LANG_CP_CATS_HIDE,
                'type' => 'fieldset',
                'childs' => [
                    new fieldList('cats_hide', [
                        'is_chosen_multiple' => true,
                        'items' => $cats_list
                    ])
                ]
            ];
        }

        return $form;
    }
}
