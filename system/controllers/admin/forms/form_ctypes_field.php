<?php

class formAdminCtypesField extends cmsForm {

    private $reserved_names = [
        'ctype', 'ctype_name', 'private_item_hint', 'is_private_item',
        'fields', 'fields_names', 'is_new', 'info_bar', 'category', 'categories'
    ];

    public function init($do, $ctype_name) {

        $model = cmsCore::getModel('content');

        return [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_BASIC,
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'hint'  => $do === 'edit' ? LANG_SYSTEM_EDIT_NOTICE : false,
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            ['max_length', 40],
                            [function ($controller, $data, $value) {

                                if (in_array($value, $this->reserved_names)) {
                                    return ERR_VALIDATE_INVALID;
                                }

                                return true;
                            }],
                            $do === 'add' ? ['unique_ctype_field', $ctype_name] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_CP_FIELD_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $model->getContentTypeTableName($ctype_name).'_fields'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldString('hint', [
                        'title' => LANG_CP_FIELD_HINT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $model->getContentTypeTableName($ctype_name).'_fields'
                        ],
                        'is_clean_disable' => true,
                        'rules' => [
                            ['max_length', 255]
                        ]
                    ]),
                    new fieldCheckbox('is_enabled', [
                        'title'   => LANG_IS_ENABLED,
                        'default' => 1
                    ])
                ]
            ],
            'type' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_TYPE,
                'childs' => [
                    new fieldList('type', [
                        'default'   => 'string',
                        'hint'      => $do === 'edit' ? LANG_CP_FIELD_TYPE_HINT : '',
                        'generator' => function () {
                            return cmsForm::getAvailableFormFields('only_public', 'content');
                        }
                    ])
                ]
            ],
            'group' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_FIELDSET,
                'childs' => [
                    new fieldList('fieldset', [
                        'title'     => LANG_CP_FIELD_FIELDSET_SELECT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $model->getContentTypeTableName($ctype_name).'_fields'
                        ],
                        'generator' => function ($field, $request, $formfield) use ($ctype_name) {
                            $model = cmsCore::getModel('content');
                            $fieldsets = $model->setLang($formfield->lang)->getContentFieldsets($ctype_name);
                            $items     = [''];
                            foreach ($fieldsets as $fieldset) {
                                $items[$fieldset] = $fieldset;
                            }
                            return $items;
                        }
                    ]),
                    new fieldString('new_fieldset', [
                        'title' => LANG_CP_FIELD_FIELDSET_ADD,
                        'hint' => LANG_CP_FIELD_FIELDSET_ADD_HINT,
                        'rules' => [
                            ['max_length', 32]
                        ]
                    ])
                ]
            ],
            'visibility' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_VISIBILITY,
                'childs' => [
                    new fieldCheckbox('is_in_item', [
                        'title'   => LANG_CP_FIELD_IN_ITEM,
                        'default' => true
                    ]),
                    new fieldList('options:is_in_item_pos', [
                        'title'              => LANG_CP_FIELD_IN_ITEM_POS,
                        'hint'               => LANG_CP_FIELD_IN_ITEM_POS_HINT,
                        'is_chosen_multiple' => true,
                        'default'            => ['page'],
                        'items' => [
                            'page'   => LANG_CP_FIELD_IN_ITEM_POS0,
                            'widget' => LANG_CP_FIELD_IN_ITEM_POS1
                        ],
                        'visible_depend' => ['is_in_item' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_in_list', [
                        'title' => LANG_CP_FIELD_IN_LIST,
                    ]),
                    new fieldListMultiple('options:context_list', [
                        'title'       => LANG_CP_FIELD_IN_LIST_CONTEXT,
                        'default'     => 0,
                        'show_all'    => true,
                        'is_vertical' => true,
                        'generator'   => function () use ($ctype_name) {

                            $lists = cmsEventsManager::hookAll('ctype_lists_context', 'template:' . $ctype_name);

                            $items = [];

                            if ($lists) {
                                foreach ($lists as $list) {
                                    $items = array_merge($items, $list);
                                }
                            }

                            return $items;
                        },
                        'visible_depend' => ['is_in_list' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('is_in_filter', [
                        'title' => LANG_CP_FIELD_IN_FILTER
                    ]),
                    new fieldList('options:relation_id', [
                        'title'     => LANG_CP_FIELD_IN_RELATION,
                        'generator' => function () use ($model, $ctype_name) {

                            $ctype = $model->getContentTypeByName($ctype_name);

                            $parents = $model->getContentTypeParents($ctype['id']);

                            $items = ['' => LANG_NO];

                            if (is_array($parents)) {
                                foreach ($parents as $parent) {
                                    $items[$parent['id']] = "{$ctype['title']} > {$parent['ctype_title']}";
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldList('options:visible_depend', [
                        'title'         => LANG_CP_FIELD_VISIBLE_DEPEND,
                        'hint'          => LANG_CP_FIELD_VISIBLE_DEPEND_HINT,
                        'add_title'     => LANG_ADD,
                        'is_multiple'   => true,
                        'dynamic_list'  => true,
                        'single_select' => 0,
                        'select_title'  => LANG_CP_FIELD_VISIBLE_DEPEND_F,
                        'multiple_keys' => [
                            'field'  => 'field', 'action' => 'field_select', 'value'  => 'field_value'
                        ],
                        'generator' => function () use ($model, $ctype_name) {

                            $fields = $model->getContentFields($ctype_name);

                            $items = [];

                            if ($fields) {
                                foreach ($fields as $field) {
                                    $items[$field['name']] = $field['title'];
                                }
                            }

                            return $items;
                        },
                        'value_items' => [
                            'show' => LANG_CP_FIELD_VISIBLE_DEPEND_SHOW,
                            'hide' => LANG_CP_FIELD_VISIBLE_DEPEND_HIDE
                        ]
                    ])
                ]
            ],
            'labels'  => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_LABELS,
                'childs' => [
                    new fieldList('options:label_in_list', [
                        'title'   => LANG_CP_FIELD_LABELS_IN_LIST,
                        'default' => 'left',
                        'items'   => [
                            'left' => LANG_CP_FIELD_LABEL_LEFT,
                            'top'  => LANG_CP_FIELD_LABEL_TOP,
                            'none' => LANG_CP_FIELD_LABEL_NONE
                        ]
                    ]),
                    new fieldList('options:label_in_item', [
                        'title'   => LANG_CP_FIELD_LABELS_IN_ITEM,
                        'default' => 'left',
                        'items'   => [
                            'left' => LANG_CP_FIELD_LABEL_LEFT,
                            'top'  => LANG_CP_FIELD_LABEL_TOP,
                            'none' => LANG_CP_FIELD_LABEL_NONE
                        ]
                    ]),
                    new fieldList('options:show_title', [
                        'title'   => LANG_CP_FIELD_LABELS_IN_FORM,
                        'default' => 1,
                        'items'   => [
                            1 => LANG_CP_FIELD_LABEL_TOP,
                            0 => LANG_CP_FIELD_LABEL_NONE
                        ]
                    ])
                ]
            ],
            'wrap' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_WRAP,
                'childs' => [
                    new fieldList('options:wrap_type', [
                        'title'   => LANG_CP_FIELD_WRAP_TYPE,
                        'default' => 'auto',
                        'items'   => [
                            'left'  => LANG_CP_FIELD_WRAP_LTYPE,
                            'right' => LANG_CP_FIELD_WRAP_RTYPE,
                            'none'  => LANG_CP_FIELD_WRAP_NTYPE,
                            'auto'  => LANG_CP_FIELD_WRAP_ATYPE
                        ]
                    ]),
                    new fieldString('options:wrap_width', [
                        'title'   => LANG_CP_FIELD_WRAP_WIDTH,
                        'hint'    => LANG_CP_FIELD_WRAP_WIDTH_HINT,
                        'default' => ''
                    ]),
                    new fieldString('options:wrap_style', [
                        'title'   => LANG_CP_FIELD_WRAP_STYLE,
                        'default' => ''
                    ])
                ]
            ],
            'format'  => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_FORMAT,
                'childs' => [
                    new fieldCheckbox('options:is_required', [
                        'title' => LANG_VALIDATE_REQUIRED,
                    ]),
                    new fieldCheckbox('options:is_digits', [
                        'title' => LANG_VALIDATE_DIGITS,
                    ]),
                    new fieldCheckbox('options:is_alphanumeric', [
                        'title' => LANG_VALIDATE_ALPHANUMERIC,
                    ]),
                    new fieldCheckbox('options:is_email', [
                        'title' => LANG_VALIDATE_EMAIL,
                    ]),
                    new fieldCheckbox('options:is_url', [
                        'title' => LANG_VALIDATE_URL,
                    ]),
                    new fieldCheckbox('options:is_regexp', [
                        'title' => LANG_CP_FIELD_REGEX
                    ]),
                    new fieldString('options:rules_regexp_str', [
                        'title' => LANG_CP_FIELD_REGEX_TEXT,
                        'hint' => LANG_CP_FIELD_REGEX_TEXT_HINT,
                        'visible_depend' => ['options:is_regexp' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:is_unique', [
                        'title' => LANG_VALIDATE_UNIQUE,
                    ])
                ]
            ],
            'values' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_VALUES,
                'childs' => [
                    new fieldText('values', [
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $model->getContentTypeTableName($ctype_name).'_fields'
                        ],
                        'size' => 8
                    ])
                ]
            ],
            'profile' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_PROFILE_VALUE,
                'childs' => [
                    new fieldList('options:profile_value', [
                        'hint'      => LANG_CP_FIELD_PROFILE_VALUE_HINT,
                        'generator' => function ($field) use ($model) {
                            $model->setTablePrefix('');
                            $fields = $model->filterIn('type', ['string', 'text', 'html', 'list', 'city'])->getContentFields('{users}');
                            $items  = ['' => LANG_NO] + array_collection_to_list($fields, 'name', 'title');
                            $model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);
                            return $items;
                        }
                    ])
                ]
            ],
            'read_access' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_GROUPS_READ,
                'childs' => [
                    new fieldListGroups('groups_read', [
                        'show_all' => true
                    ])
                ]
            ],
            'add_access' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_GROUPS_ADD,
                'childs' => [
                    new fieldListGroups('groups_add', [
                        'show_all' => true
                    ])
                ]
            ],
            'edit_access' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_GROUPS_EDIT,
                'childs' => [
                    new fieldListGroups('groups_edit', [
                        'show_all' => true
                    ])
                ]
            ],
            'filter_access' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_IN_FILTER,
                'childs' => [
                    new fieldListGroups('filter_view', [
                        'show_all' => true
                    ])
                ]
            ],
            'author_access' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_AUTHOR_ACCESS,
                'childs' => [
                    new fieldListMultiple('options:author_access', [
                        'items' => [
                            'is_read' => LANG_CP_FIELD_READING,
                            'is_edit' => LANG_CP_FIELD_EDITING,
                        ]
                    ])
                ]
            ]
        ];
    }
}
