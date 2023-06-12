<?php

class formUsersField extends cmsForm {

    private $reserved_names = ['ctype', 'ctype_name', 'item_css_class', 'notice_title', 'actions'];

    public function init($do) {

        return [
            'basic' => [
                'type' => 'fieldset',
                'title' => LANG_CP_BASIC,
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'hint'  => $do === 'edit' ? LANG_SYSTEM_EDIT_NOTICE : false,
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            ['max_length', 20],
                            [function($controller, $data, $value) {

                                if(in_array($value, $this->reserved_names)){
                                    return ERR_VALIDATE_INVALID;
                                }

                                return true;
                            }],
                            $do === 'add' ? ['unique_field'] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_CP_FIELD_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => '{users}_fields'
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
                            'table' => '{users}_fields'
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
                            return cmsForm::getAvailableFormFields('only_public', 'users');
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
                            'table' => '{users}_fields'
                        ],
                        'generator' => function ($field, $request, $formfield) {
                            $model = cmsCore::getModel('content');
                            $model->setTablePrefix('')->setLang($formfield->lang);
                            $fieldsets = $model->getContentFieldsets('{users}');
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
                        'title'   => LANG_CP_FIELD_IN_PROFILE,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_in_list', [
                        'title' => LANG_CP_FIELD_IN_LIST,
                    ]),
                    new fieldCheckbox('is_in_filter', [
                        'title' => LANG_CP_FIELD_IN_FILTER
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
                        'generator' => function () {

                            $model = cmsCore::getModel('content');
                            $model->setTablePrefix('');

                            $fields = $model->getContentFields('{users}');

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
                    ])
                ]
            ],
            'format'  => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_FORMAT,
                'childs' => [
                    new fieldCheckbox('options:is_required', [
                        'title' => LANG_VALIDATE_REQUIRED
                    ]),
                    new fieldCheckbox('options:is_digits', [
                        'title' => LANG_VALIDATE_DIGITS
                    ]),
                    new fieldCheckbox('options:is_alphanumeric', [
                        'title' => LANG_VALIDATE_ALPHANUMERIC
                    ]),
                    new fieldCheckbox('options:is_email', [
                        'title' => LANG_VALIDATE_EMAIL
                    ]),
                    new fieldCheckbox('options:is_url', [
                        'title' => LANG_VALIDATE_URL
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
                        'title' => LANG_VALIDATE_UNIQUE
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
                            'table' => '{users}_fields'
                        ],
                        'size' => 8
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
