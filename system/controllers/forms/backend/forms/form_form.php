<?php

class formFormsForm extends cmsForm {

    public $is_tabbed = true;

    public function init($do, $form_data = [], $fields = []) {

        $meta_fields = [
            'form_title' => LANG_FORMS_CP_TITLE,
            'form_data'  => LANG_FORMS_CP_META_DATA,
            'page_url'   => LANG_FORMS_PAGE_URL,
            'user_name'  => LANG_USER,
            'ip'         => 'IP'
        ];

        if ($fields) {
            foreach ($fields as $field) {
                $meta_fields[$field['name']] = $field['title'];
            }
        }

        return [
            'basic' => [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('name', [
                        'title'   => LANG_SYSTEM_NAME,
                        'hint'    => LANG_FORMS_CP_NAME_HINT,
                        'options' => [
                            'max_length'        => 32,
                            'show_symbol_count' => true
                        ],
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            (in_array($do, ['add', 'copy']) ? ['unique', 'forms', 'name'] : ['unique_exclude', 'forms', 'name', $form_data['id']])
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_FORMS_CP_TITLE,
                        'can_multilanguage'    => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table'          => 'forms'
                        ],
                        'options' => [
                            'max_length'        => 255,
                            'show_symbol_count' => true
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldHtml('description', [
                        'title' => LANG_DESCRIPTION,
                        'can_multilanguage'    => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table'          => 'forms'
                        ]
                    ]),
                    new fieldList('tpl_form', [
                        'title'     => LANG_FORMS_CP_TPL_FORM,
                        'generator' => function ($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/ui', 'form*.tpl.php', false, ['form_fields']);
                        }
                    ])
                ]
            ],
            'options' => [
                'title'  => LANG_OPTIONS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('options:show_title', [
                        'title' => LANG_SHOW_TITLE
                    ]),
                    new fieldCheckbox('options:available_by_link', [
                        'title' => LANG_FORMS_CP_AVAILABLE_BY_LINK
                    ]),
                    new fieldCheckbox('options:hide_after_submit', [
                        'title' => LANG_FORMS_CP_HIDE_AFTER_SUBMIT
                    ]),
                    new fieldString('options:submit_title', [
                        'title'             => LANG_FORMS_CP_SUBMIT_TITLE,
                        'hint'              => LANG_FORMS_CP_SUBMIT_TITLE_HINT,
                        'can_multilanguage' => true
                    ]),
                    new fieldList('options:send_type', [
                        'title'              => LANG_FORMS_CP_SEND_TYPE,
                        'hint'               => LANG_FORMS_CP_SEND_TYPE_HINT,
                        'is_chosen_multiple' => true,
                        'items' => [
                            'notice' => LANG_FORMS_CP_SEND_TYPE1,
                            'email'  => LANG_FORMS_CP_SEND_TYPE2,
                            'author' => LANG_FORMS_CP_SEND_TYPE3
                        ]
                    ]),
                    new fieldString('options:send_type_notice', [
                        'title'          => LANG_USERS,
                        'hint'           => LANG_FORMS_CP_SEND_USERS_HINT,
                        'autocomplete'   => ['url' => href_to('admin', 'users', 'autocomplete'), 'multiple' => true],
                        'visible_depend' => ['options:send_type:' => ['show' => ['notice']]]
                    ]),
                    new fieldHtml('options:notify_text', [
                        'title'             => LANG_FORMS_CP_FORM_NOTIFY_TEXT,
                        'hint'              => LANG_FORMS_CP_SEND_TEXT_FORM_HINT,
                        'can_multilanguage' => true,
                        'visible_depend'    => ['options:send_type:' => ['show' => ['notice']]],
                        'patterns_hint'     => [
                            'patterns'     => $meta_fields,
                            'text_panel'   => '',
                            'always_show'  => true,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ]),
                    new fieldString('options:send_type_email', [
                        'title'          => LANG_EMAIL,
                        'hint'           => LANG_FORMS_CP_SEND_EMAIL_HINT,
                        'visible_depend' => ['options:send_type:' => ['show' => ['email']]]
                    ]),
                    new fieldHtml('options:letter', [
                        'title'             => LANG_FORMS_CP_FORM_LETTER,
                        'hint'              => LANG_FORMS_CP_SEND_TEXT_FORM_HINT,
                        'can_multilanguage' => true,
                        'options' => [
                            'editor' => 'ace'
                        ],
                        'visible_depend' => ['options:send_type:' => ['show' => ['email']]],
                        'patterns_hint' => [
                            'patterns'     => $meta_fields,
                            'text_panel'   => '',
                            'always_show'  => true,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ]),
                    new fieldString('options:action', [
                        'title' => LANG_FORMS_CP_ACTION,
                        'hint'  => LANG_FORMS_CP_ACTION_HINT
                    ]),
                    new fieldList('options:method', [
                        'title' => LANG_FORMS_CP_METHOD,
                        'hint'  => LANG_FORMS_CP_METHOD_HINT,
                        'items' => [
                            'ajax' => 'POST ajax',
                            'post' => 'POST',
                            'get'  => 'GET'
                        ],
                        'visible_depend' => ['options:action' => ['hide' => ['']]]
                    ]),
                    new fieldText('options:send_text', [
                        'title'             => LANG_FORMS_CP_SEND_TEXT_FORM,
                        'hint'              => LANG_FORMS_CP_SEND_TEXT_FORM_HINT,
                        'can_multilanguage' => true,
                        'patterns_hint'     => [
                            'patterns'     => $meta_fields,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ]),
                    new fieldString('options:continue_link', [
                        'title' => LANG_FORMS_CP_CONTINUE_LINK
                    ])
                ]
            ]
        ];
    }

}
