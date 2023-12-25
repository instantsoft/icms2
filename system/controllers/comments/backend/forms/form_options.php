<?php

class formCommentsOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_BASIC_OPTIONS,
                'childs' => [
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
                        }
                    ]),
                    new fieldList('editor_presets', [
                        'title'         => LANG_PARSER_HTML_EDITOR_GR,
                        'is_multiple'   => true,
                        'dynamic_list'  => true,
                        'select_title'  => LANG_SELECT,
                        'multiple_keys' => array(
                            'group_id'  => 'field', 'preset_id' => 'field_select'
                        ),
                        'generator'     => function ($item) {
                            $users_model = cmsCore::getModel('users');

                            $items = [];

                            $groups = $users_model->getGroups(true);

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
                        }
                    ]),
                    new fieldList('typograph_id', [
                        'title'     => LANG_PARSER_TYPOGRAPH,
                        'default'   => 1,
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
                        ]
                    ]),
                    new fieldCheckbox('disable_icms_comments', [
                        'title' => LANG_COMMENTS_DISABLE_ICMS_COMMENTS,
                        'hint'  => LANG_COMMENTS_DISABLE_ICMS_COMMENTS_HINT
                    ]),
                    new fieldListMultiple('show_list', [
                        'title'     => LANG_COMMENTS_SHOW_LIST,
                        'default'   => 0,
                        'show_all'  => true,
                        'generator' => function ($item) {

                            $items = [];

                            $comments_targets = cmsEventsManager::hookAll('comments_targets');

                            if (is_array($comments_targets)) {
                                foreach ($comments_targets as $comments_target) {
                                    foreach ($comments_target['types'] as $name => $title) {
                                        $items[$name] = $title;
                                    }
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldCheckbox('is_guests', [
                        'title' => LANG_COMMENTS_OPT_IS_GUESTS,
                        'hint'  => LANG_COMMENTS_OPT_IS_GUESTS_HINT,
                    ]),
                    new fieldCheckbox('is_guests_moderate', [
                        'title'          => LANG_COMMENTS_OPT_IS_GUESTS_MODERATE,
                        'default'        => 1,
                        'visible_depend' => ['is_guests' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('guest_ip_delay', [
                        'title'          => LANG_COMMENTS_OPT_GUESTS_DELAY,
                        'units'          => LANG_MINUTE10,
                        'visible_depend' => ['is_guests' => ['show' => ['1']]]
                    ]),
                    new fieldText('restricted_ips', [
                        'title'          => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_IPS,
                        'hint'           => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_IPS_HINT,
                        'visible_depend' => ['is_guests' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('show_author_email', [
                        'title'          => LANG_COMMENTS_OPT_SHOW_AUTHOR_EMAIL,
                        'default'        => 1,
                        'visible_depend' => ['is_guests' => ['show' => ['1']]]
                    ]),
                    new fieldText('restricted_emails', [
                        'title'          => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_EMAILS,
                        'visible_depend' => ['is_guests' => ['show' => ['1']]]
                    ]),
                    new fieldText('restricted_names', [
                        'title'          => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_NAMES,
                        'visible_depend' => ['is_guests' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('dim_negative', [
                        'title' => LANG_COMMENTS_OPT_DIM_NEGATIVE
                    ]),
                    new fieldCheckbox('update_user_rating', [
                        'title' => LANG_COMMENTS_UPDATE_USER_RATING,
                        'hint'  => LANG_COMMENTS_UPDATE_USER_RATING_HINT,
                    ]),
                    new fieldNumber('limit_nesting', [
                        'title'   => LANG_COMMENTS_LIMIT_NESTING,
                        'default' => 5,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldNumber('limit', [
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ]
        ];
    }

}
