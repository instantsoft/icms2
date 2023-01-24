<?php

class formAdminCtypesProp extends cmsForm {

    public function init($do, $ctype) {

        $model = cmsCore::getModel('backend_content');

        $table_name = $model->table_prefix . $ctype['name'] . '_props';

        return [
            'basic' => [
                'type'   => 'fieldset',
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
            'group' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_FIELDSET,
                'childs' => [
                    new fieldList('fieldset', [
                        'title'     => LANG_CP_FIELD_FIELDSET_SELECT,
                        'generator' => function ($prop) use ($model, $ctype) {
                            $fieldsets = $model->getContentPropsFieldsets($ctype['name']);
                            $items     = [''];
                            if (is_array($fieldsets)) {
                                foreach ($fieldsets as $fieldset) {
                                    $items[$fieldset] = $fieldset;
                                }
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
            'type'   => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_FIELD_TYPE,
                'childs' => [
                    new fieldList('type', [
                        'default' => 'list',
                        'items'   => [
                            'list'          => LANG_PARSER_LIST,
                            'list_multiple' => LANG_PARSER_LIST_MULTIPLE,
                            'string'        => LANG_PARSER_STRING,
                            'color'         => LANG_PARSER_COLOR,
                            'number'        => LANG_PARSER_NUMBER,
                            'checkbox'      => LANG_PARSER_CHECKBOX
                        ]
                    ]),
                    new fieldCheckbox('options:is_required', [
                        'title' => LANG_VALIDATE_REQUIRED
                    ])
                ]
            ],
            'number' => [
                'type'   => 'fieldset',
                'title'  => LANG_PARSER_NUMBER,
                'childs' => [
                    new fieldString('options:units', [
                        'title' => LANG_CP_PROP_UNITS,
                        'can_multilanguage' => true
                    ]),
                    new fieldCheckbox('options:is_filter_range', [
                        'title' => LANG_PARSER_NUMBER_FILTER_RANGE
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
                    ]),
                    new fieldCheckbox('options:is_filter_multi', [
                        'title' => LANG_PARSER_LIST_FILTER_MULTI
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
