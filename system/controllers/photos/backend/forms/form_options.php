<?php

class formPhotosOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $perm_childs = [];

        $perm_childs[] = new fieldListGroups('allow_add_public_albums', [
            'title' => LANG_PHOTOS_ALLOW_ADD_PUBLIC_ALBUMS
        ]);

        $perm_childs[] = new fieldCheckbox('allow_download', [
            'title' => LANG_PHOTOS_DOWNLOAD_ALLOW
        ]);

        $presets = cmsCore::getModel('images')->getPresetsList();

        foreach ($presets as $name => $title) {

            $perm_childs[] = new fieldListGroups('download_view:' . $name, [
                'title'          => sprintf(LANG_PHOTOS_DOWNLOAD_VIEW, $title),
                'show_all'       => true,
                'show_guests'    => true,
                'visible_depend' => ['allow_download' => ['show' => ['1']]]
            ]);

            $perm_childs[] = new fieldListGroups('download_hide:' . $name, [
                'title'          => sprintf(LANG_PHOTOS_DOWNLOAD_HIDE, $title),
                'show_guests'    => true,
                'visible_depend' => ['allow_download' => ['show' => ['1']]]
            ]);
        }

        return [
            [
                'title'  => LANG_OPTIONS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldListMultiple('sizes', [
                        'title'   => LANG_PHOTOS_SIZES,
                        'default' => ['big', 'normal', 'small'],
                        'items'   => $presets,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldList('preset', [
                        'title'   => LANG_PHOTOS_PRESET,
                        'default' => 'big',
                        'items'   => $presets,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldList('preset_small', [
                        'title'   => LANG_PHOTOS_PRESET_SMALL,
                        'default' => 'normal',
                        'items'   => $presets,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldList('preset_related', [
                        'title'   => LANG_PHOTOS_PRESET_RELATED,
                        'default' => 'normal',
                        'items'   => $presets,
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldText('types', [
                        'title'   => LANG_PHOTOS_TYPES,
                        'hint'    => LANG_PHOTOS_TYPES_HINT,
                        'is_strip_tags' => true,
                        'default' => "1 | Фото\n2 | Векторы\n3 | Иллюстрации",
                        'size'    => 8
                    ]),
                    new fieldList('ordering', [
                        'title'   => LANG_SORTING,
                        'default' => 'date_pub',
                        'items'   => modelPhotos::getOrderList()
                    ]),
                    new fieldList('orderto', [
                        'title'   => LANG_PHOTOS_SORT_ORDERTO,
                        'default' => 'desc',
                        'items'   => [
                            'asc'  => LANG_SORTING_ASC,
                            'desc' => LANG_SORTING_DESC
                        ]
                    ]),
                    new fieldNumber('limit', [
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 16,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldNumber('related_limit', [
                        'title'   => LANG_PHOTOS_RELATED_LIMIT,
                        'default' => 20
                    ]),
                    new fieldCheckbox('hide_photo_item_info', [
                        'title'   => LANG_PHOTOS_HIDE_PHOTO_ITEM_INFO
                    ]),
                    new fieldString('url_pattern', [
                        'title'   => LANG_PHOTOS_URL_PATTERN,
                        'prefix'  => '/photos/',
                        'suffix'  => '.html',
                        'default' => '{id}-{title}',
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('editor', [
                        'title' => LANG_PARSER_HTML_EDITOR,
                        'default' => cmsConfig::get('default_editor'),
                        'generator' => function($item) {
                            $items = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    ]),
                    new fieldList('editor_presets', [
                        'title'        => LANG_PARSER_HTML_EDITOR_GR,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_SELECT,
                        'multiple_keys' => [
                            'group_id' => 'field', 'preset_id' => 'field_select'
                        ],
                        'generator' => function($item) {
                            $users_model = cmsCore::getModel('users');
                            $items = [];
                            $groups = $users_model->getGroups(false);
                            foreach($groups as $group){
                                $items[$group['id']] = $group['title'];
                            }
                            return $items;
                        },
                        'values_generator' => function() {
                            $items = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor) {
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    ]),
                    new fieldList('typograph_id', [
                        'title'     => LANG_PARSER_TYPOGRAPH,
                        'default'   => 3,
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
            ],
            [
                'title'  => LANG_PERMISSIONS,
                'type'   => 'fieldset',
                'childs' => $perm_childs
            ]
        ];
    }

}
