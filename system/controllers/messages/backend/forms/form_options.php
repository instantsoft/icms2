<?php

class formMessagesOptions extends cmsForm {

    public function init() {

        return [
            [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('use_queue', [
                        'title' => LANG_PM_USE_QUEUE
                    ])
                ]
            ],
            [
                'title'  => LANG_PM_MESSAGES,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldCheckbox('is_enable_pm', [
                        'title'   => LANG_PM_IS_ENABLE_PM,
                        'default' => true
                    ]),
                    new fieldCheckbox('is_contact_first_select', [
                        'title'          => LANG_PM_SELECT_FIRST_CONTACT,
                        'hint'           => LANG_PM_SELECT_FIRST_CONTACT_HINT,
                        'default'        => false,
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldList('editor', [
                        'title'     => LANG_PARSER_HTML_EDITOR,
                        'default'   => cmsConfig::get('default_editor'),
                        'generator' => function ($item) {
                            $items   = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach ($editors as $editor) {
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if ($ps) {
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        },
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldList('editor_presets', [
                        'title'         => LANG_PARSER_HTML_EDITOR_GR,
                        'is_multiple'   => true,
                        'dynamic_list'  => true,
                        'select_title'  => LANG_SELECT,
                        'multiple_keys' => [
                            'group_id'  => 'field', 'preset_id' => 'field_select'
                        ],
                        'generator'     => function ($item) {
                            $users_model = cmsCore::getModel('users');

                            $items = [];

                            $groups = $users_model->getGroups();

                            foreach ($groups as $group) {
                                $items[$group['id']] = $group['title'];
                            }

                            return $items;
                        },
                        'values_generator' => function () {
                            $items   = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach ($editors as $editor) {
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if ($ps) {
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        },
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldList('typograph_id', [
                        'title'     => LANG_PARSER_TYPOGRAPH,
                        'default'   => 2,
                        'generator' => function ($item) {
                            $items   = [];
                            $presets = (new cmsModel())->get('typograph_presets') ?: [];
                            foreach ($presets as $preset) {
                                $items[$preset['id']] = $preset['title'];
                            }
                            return $items;
                        },
                        'rules' => [
                            ['required']
                        ],
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('limit', [
                        'title'          => LANG_PM_LIMIT,
                        'default'        => 5,
                        'rules'          => [
                            ['required'],
                            ['min', 1]
                        ],
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('time_delete_old', [
                        'title'          => LANG_PM_TIME_DELETE_OLD,
                        'hint'           => LANG_PM_TIME_DELETE_OLD_HINT,
                        'default'        => 0,
                        'units'          => LANG_DAY10,
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldList('realtime_mode', [
                        'title'          => LANG_PM_REALTIME_MODE,
                        'items'          => [
                            'ajax'   => 'Ajax',
                            'socket' => LANG_PM_REALTIME_MODE_SOCKET
                        ],
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('refresh_time', [
                        'title'          => LANG_PM_REFRESH_TIME,
                        'default'        => 15,
                        'units'          => LANG_SECOND10,
                        'rules'          => [
                            ['required'],
                            ['min', 1]
                        ],
                        'visible_depend' => ['realtime_mode' => ['show' => ['ajax']], 'is_enable_pm' => ['hide' => ['0']]]
                    ]),
                    new fieldString('socket_host', [
                        'title'          => LANG_PM_REALTIME_SOCKET_HOST,
                        'visible_depend' => ['realtime_mode' => ['show' => ['socket']], 'is_enable_pm' => ['hide' => ['0']]]
                    ]),
                    new fieldNumber('socket_port', [
                        'title'          => LANG_PM_REALTIME_SOCKET_PORT,
                        'default'        => 3000,
                        'rules'          => [
                            ['min', 1]
                        ],
                        'visible_depend' => ['realtime_mode' => ['show' => ['socket']], 'is_enable_pm' => ['hide' => ['0']]]
                    ]),
                    new fieldListGroups('groups_allowed', [
                        'title'    => LANG_PERMISSIONS,
                        'show_all' => true,
                        'default'  => [0],
                        'visible_depend' => ['is_enable_pm' => ['show' => ['1']]]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_EMAIL,
                'childs' => [
                    new fieldHtml('email_template', [
                        'title' => LANG_PM_EMAIL_TEMPLATE,
                        'patterns_hint' => [
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN,
                            'patterns' =>  [
                                'body'            => LANG_PM_EMAIL_BODY,
                                'year'            => LANG_YEAR,
                                'site'            => LANG_CP_SETTINGS_SITENAME,
                                'unsubscribe_url' => LANG_PM_EMAIL_UNSUBSCRIBE_URL
                            ]
                        ],
                        'options' => [
                            'editor' => 'ace'
                        ]
                    ])
                ]
            ]
        ];
    }

}
