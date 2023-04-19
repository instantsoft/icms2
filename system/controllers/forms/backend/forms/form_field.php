<?php

class formFormsField extends cmsForm {

    public function init($do, $form_id) {

        $model = cmsCore::getModel('forms');

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
                            $do === 'add' ? ['unique_field', $form_id] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_CP_FIELD_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'forms_fields'
                        ],
                        'rules' => [
                            ['required'],
                            ['max_length', 128]
                        ]
                    ]),
                    new fieldString('hint', [
                        'title' => LANG_CP_FIELD_HINT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'forms_fields'
                        ],
                        'rules' => [
                            ['max_length', 200]
                        ]
                    ]),
                    new fieldCheckbox('is_enabled', [
                        'title'   => LANG_IS_ENABLED,
                        'default' => 1
                    ])
                ]
            ],
            'type' => [
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_TYPE,
                'childs' => [
                    new fieldList('type', [
                        'default' => 'string',
                        'generator' => function () {
                            return cmsForm::getAvailableFormFields('only_public', 'forms');
                        }
                    ])
                ]
            ],
            'group' => [
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_FIELDSET,
                'childs' => [
                    new fieldList('fieldset', [
                        'title' => LANG_CP_FIELD_FIELDSET_SELECT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'forms_fields'
                        ],
                        'generator' => function ($field, $request, $formfield) use ($model) {
                            $model->setLang($formfield->lang);
                            $fieldsets = $model->getFormFieldsets($field['form_id']);
                            $items = [''];
                            if ($fieldsets) {
                                foreach ($fieldsets as $fieldset) {
                                    $items[$fieldset] = $fieldset;
                                }
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
                    ])
                ]
            ],
            'values' => [
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_VALUES,
                'childs' => [
                    new fieldText('values', [
                        'is_strip_tags' => true,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'forms_fields'
                        ],
                        'size' => 8
                    ])
                ]
            ]
        ];
    }

}
