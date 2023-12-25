<?php

class formWallOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_BASIC_OPTIONS,
                'childs' => [
                    new fieldNumber('limit', [
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldNumber('show_entries', [
                        'title'   => LANG_WALL_SHOW_ENTRIES,
                        'default' => 5,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldList('order_by', [
                        'title'   => LANG_SORTING,
                        'default' => 'date_pub',
                        'rules'   => [
                            ['required']
                        ],
                        'items'   => [
                            'date_pub'        => LANG_DATE_PUB,
                            'date_last_reply' => LANG_WALL_SORTING_DATE_LAST_REPLY
                        ]
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
                        'generator' => function ($item) {
                            $users_model = cmsCore::getModel('users');

                            $items = [];

                            $groups = $users_model->getGroups(false);

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
                    ])
                ]
            ]
        ];
    }

}
