<?php

class formContentCategory extends cmsForm {

    public function init($ctype, $action) {

        $model = cmsCore::getModel('content');

        $table_name = $model->getContentCategoryTableName($ctype['name']);

        $fieldsets = [
            'base' => [
                'title'  => LANG_BASIC_OPTIONS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_CATEGORY_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'options' => [
                            'max_length' => 200
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('parent_id', [
                        'title' => LANG_PARENT_CATEGORY,
                        'generator' => function ($cat) use ($ctype, $model) {

                            $tree = $model->limit(0)->getCategoriesTree($ctype['name']);

                            if ($tree) {
                                foreach ($tree as $item) {
                                    // при редактировании исключаем себя и вложенные
                                    // подкатегории из списка выбора родителя
                                    if (isset($cat['ns_left'])) {
                                        if ($item['ns_left'] >= $cat['ns_left'] && $item['ns_right'] <= $cat['ns_right']) {
                                            continue;
                                        }
                                    }
                                    $items[$item['id']] = str_repeat('- ', $item['ns_level']) . ' ' . $item['title'];
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldHtml('description', [
                        'title' => LANG_CATEGORY_DESCRIPTION,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ]
                    ]),
                    new fieldCheckbox('is_hidden', [
                        'title' => LANG_CATEGORY_IS_HIDDEN
                    ])
                ]
            ]
        ];

        // Если заданы пресеты
        if (!empty($ctype['options']['cover_sizes'])){

            $fieldsets['cat_cover'] = [
                'type'   => 'fieldset',
                'title'  => LANG_CATEGORY_COVER,
                'childs' => [
                    new fieldImage('cover', [
                        'options' => [
                            'allow_import_link' => true,
                            'sizes' => $ctype['options']['cover_sizes']
                        ]
                    ])
                ]
            ];
        }

        // Если ручной ввод ключевых слов или описания, то добавляем поля для этого
        if (!empty($ctype['options']['is_cats_title']) ||
                !empty($ctype['options']['is_cats_h1']) ||
                !empty($ctype['options']['is_cats_keys']) ||
                !empty($ctype['options']['is_cats_desc'])) {

            $fieldsets['cat_seo'] = [
                'type'   => 'fieldset',
                'title'  => LANG_SEO,
                'childs' => []
            ];

            if (!empty($ctype['options']['is_cats_h1'])){

                $fieldsets['cat_seo']['childs'][] = new fieldString('seo_h1', [
                    'title'   => LANG_SEO_H1,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'options' => [
                        'max_length' => 256,
                        'show_symbol_count' => true
                    ]
                ]);
            }

            if (!empty($ctype['options']['is_cats_title'])) {

                $fieldsets['cat_seo']['childs'][] = new fieldString('seo_title', [
                    'title'   => LANG_SEO_TITLE,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'options' => [
                        'max_length' => 256,
                        'show_symbol_count' => true
                    ]
                ]);
            }

            if (!empty($ctype['options']['is_cats_keys'])) {

                $fieldsets['cat_seo']['childs'][] = new fieldString('seo_keys', [
                    'title'   => LANG_SEO_KEYS,
                    'hint'    => LANG_SEO_KEYS_HINT,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'options' => [
                        'max_length' => 256,
                        'show_symbol_count' => true
                    ]
                ]);
            }

            if (!empty($ctype['options']['is_cats_desc'])) {

                $fieldsets['cat_seo']['childs'][] = new fieldText('seo_desc', [
                    'title'         => LANG_SEO_DESC,
                    'hint'          => LANG_SEO_DESC_HINT,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'is_strip_tags' => true,
                    'options'       => [
                        'max_length' => 256,
                        'show_symbol_count' => true
                    ]
                ]);
            }
        }

        // Если ручной ввод SLUG, то добавляем поле для этого
        if (empty($ctype['options']['is_cats_auto_url']) && cmsCore::getLanguageName() === cmsConfig::get('language')){

            $fieldsets['cat_seo_slug'] = [
                'type'   => 'fieldset',
                'title'  => LANG_SLUG,
                'childs' => [
                    new fieldString('slug_key', [
                        'rules' => [
                            ['required'],
                            ['max_length', 255]
                        ]
                    ])
                ]
            ];
        }

        // для администраторов показываем поля доступа
        if (cmsUser::isAdmin()) {

            $fieldsets['cat_perms'] = [
                'type'   => 'fieldset',
                'title'  => LANG_PERMISSIONS,
                'childs' => [
                    new fieldListGroups('allow_add', [
                        'title'       => LANG_CONTENT_CATS_ALLOW_ADD,
                        'hint'        => LANG_CONTENT_CATS_ALLOW_ADD_HINT,
                        'show_all'    => true,
                        'show_guests' => false
                    ])
                ]
            ];
        }

        return $fieldsets;
    }

}
