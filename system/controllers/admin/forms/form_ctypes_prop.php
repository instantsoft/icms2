<?php

class formAdminCtypesProp extends cmsForm {

    public function init($do, $ctype) {

        $model = cmsCore::getModel('backend_content');

        $table_name = $model->getContentTypeTableName($ctype['name'], '_props');

        return [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_BASIC,
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_CP_PROP_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 100]
                        ]
                    ]),
                    new fieldCheckbox('is_in_filter', [
                        'title' => LANG_CP_FIELD_IN_FILTER,
                    ])
                ]
            ],
            'type'   => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_TYPE,
                'childs' => [
                    new fieldList('type', [
                        'default' => 'list',
                        'items'   => modelBackendContent::PROP_FIELDS
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
                            'table' => $table_name
                        ],
                        'generator' => function ($field, $request, $formfield) use ($ctype) {
                            $model = cmsCore::getModel('content');
                            $fieldsets = $model->setLang($formfield->lang)->getContentFieldsets($ctype['name'], '_props');
                            $items     = [''];
                            foreach ($fieldsets as $fieldset) {
                                $items[$fieldset] = $fieldset;
                            }
                            return $items;
                        }
                    ]),
                    new fieldString('new_fieldset', [
                        'title' => LANG_CP_FIELD_FIELDSET_ADD,
                        'rules' => [
                            ['max_length', 32]
                        ]
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
                    new fieldString('options:rules_regexp_error', [
                        'title' => LANG_CP_FIELD_REGEX_ERROR,
                        'hint' => LANG_CP_FIELD_REGEX_ERROR_HINT,
                        'multilanguage' => true,
                        'visible_depend' => ['options:is_regexp' => ['show' => ['1']]]
                    ])
                ]
            ],
            'values' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_PROP_VALUES,
                'childs' => [
                    new fieldText('values', [
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'size'          => 8,
                        'is_strip_tags' => true,
                        'hint'          => LANG_CP_PROP_VALUES_HINT
                    ])
                ]
            ],
            'cats'   => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_PROP_CATS,
                'childs' => [
                    new fieldList('cats', [
                        'is_multiple'              => true,
                        'multiple_select_deselect' => true,
                        'is_tree'                  => true,
                        'generator'                => function ($prop) use ($model, $ctype) {
                            $tree = $model->limit(0)->getCategoriesTree($ctype['name'], false);
                            foreach ($tree as $c) {
                                $items[$c['id']] = str_repeat('- ', $c['ns_level']) . ' ' . $c['title'];
                            }
                            return $items;
                        }
                    ])
                ]
            ]
        ];
    }
}
