<?php

class formAdminCtypesFilter extends cmsForm {

    public function init($do, $ctype, $fields, $props_fields, $table_name, $filter) {

        $meta_item_fields = [
            'title'             => LANG_TITLE,
            'description'       => LANG_DESCRIPTION,
            'f_title'           => LANG_FILTER . ': ' . LANG_TITLE,
            'f_description'     => LANG_FILTER . ': ' . LANG_DESCRIPTION,
            'ctype_title'       => LANG_CONTENT_TYPE . ': ' . LANG_TITLE,
            'ctype_description' => LANG_CONTENT_TYPE . ': ' . LANG_DESCRIPTION,
            'ctype_label1'      => LANG_CP_NUMERALS_1_LABEL,
            'ctype_label2'      => LANG_CP_NUMERALS_2_LABEL,
            'ctype_label10'     => LANG_CP_NUMERALS_10_LABEL,
            'filter_string'     => LANG_FILTERS
        ];

        $slug_field_rules = [
            ['required'],
            ['slug']
        ];

        if ($do == 'add') {
            $slug_field_rules[] = ['unique', $table_name, 'slug'];
        } else {
            $slug_field_rules[] = ['unique_exclude', $table_name, 'slug', $filter['id']];
        }

        $filters = $filters_props = [];

        foreach ($fields as $_field) {

            if ((!$_field['handler']->allow_index || $_field['handler']->filter_type === false) && $_field['type'] != 'parent') {
                continue;
            }

            $field = $_field['handler'];

            $field->setName('filters:' . $field->getName());

            $field->display_input = 'getFilterInput';
            $field->show_filter_input_title = true;
            $field->is_denormalization = false;

            $field->setContext('filter')->setItem(['ctype_name' => $ctype['name'], 'id' => null]);

            $required_key = array_search(['required'], $field->getRules());
            if ($required_key !== false) {
                unset($field->rules[$required_key]);
            }

            $filters[] = $field;
        }

        if (!empty($props_fields)) {

            $props_bind = $this->getContentPropsBind($ctype['name']);

            foreach ($props_fields as $id => $props_field) {

                if ((!$props_field->allow_index || $props_field->filter_type === false)) {
                    continue;
                }

                $props_field->setName('filters:p' . $id);

                $props_field->display_input = 'getFilterInput';
                $props_field->show_filter_input_title = true;

                if (isset($props_bind[$id])) {
                    $props_field->visible_depend = ['filters:category_id' => ['show' => $props_bind[$id]]];
                }

                $props_field->setContext('filter')->setItem(['ctype_name' => $ctype['name'], 'id' => null]);

                $required_key = array_search(['required'], $props_field->rules);
                if ($required_key !== false) {
                    unset($props_field->rules[$required_key]);
                }

                $filters_props[] = $props_field;
            }

            if ($filters_props) {
                $filters_props = [
                    new fieldList(
                        'filters:category_id',
                        [
                            'title' => LANG_CATEGORY,
                            'items' => $this->getFormCategories($ctype)
                        ]
                    )
                ] + $filters_props;
            }
        }

        return [
            'basic' => [
                'type' => 'fieldset',
                'title'  => LANG_CP_BASIC,
                'childs' => [
                    new fieldString('slug', [
                        'title' => LANG_SYSTEM_NAME,
                        'prefix' => href_to(((cmsConfig::get('ctype_default') && in_array($ctype['name'], cmsConfig::get('ctype_default'))) ? '' : $ctype['name'])),
                        'options' => [
                            'max_length' => 100,
                            'show_symbol_count' => true
                        ],
                        'rules' => $slug_field_rules
                    ]),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'options' => [
                            'max_length' => 100,
                            'show_symbol_count' => true
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldHtml('description', [
                        'title' => LANG_DESCRIPTION,
                        'hint' => LANG_CP_FILTER_DESC_HINT,
                        'store_via_html_filter' => true,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ]
                    ])
                ]
            ],
            'filter' => [
                'title'  => LANG_CP_FILTER_FIELDS,
                'type'   => 'fieldset',
                'childs' => $filters
            ],
            'filter_props' => [
                'title'  => LANG_CP_FILTER_PROPS,
                'type'   => 'fieldset',
                'childs' => $filters_props
            ],
            'cats' => [
                'title'  => LANG_CP_FILTER_CATS,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldListMultiple('cats', [
                        'show_all'  => true,
                        'generator' => function ($prop) use ($ctype) {
                            $model = cmsCore::getModel('content');
                            $tree = $model->limit(0)->getCategoriesTree($ctype['name']);
                            foreach ($tree as $c) {
                                $items[$c['id']] = $c['title'];
                            }
                            return $items;
                        }
                    ])
                ]
            ],
            'seo' => [
                'title' => LANG_SEO,
                'type' => 'fieldset',
                'childs' => [
                    new fieldString('seo_h1', [
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_h1_pattern']) ? $ctype['options']['seo_cat_h1_pattern'] : null),
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldString('seo_title', [
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_title_pattern']) ? $ctype['options']['seo_cat_title_pattern'] : null),
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldString('seo_keys', [
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_keys_pattern']) ? $ctype['options']['seo_cat_keys_pattern'] : null),
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),
                    new fieldText('seo_desc', [
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint' => ($meta_item_fields ? LANG_CP_SEOMETA_DS_HINT : ''),
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => $table_name
                        ],
                        'patterns_hint' => ($meta_item_fields ? ['patterns' =>  $meta_item_fields] : ''),
                        'default' => (!empty($ctype['options']['seo_cat_desc_pattern']) ? $ctype['options']['seo_cat_desc_pattern'] : null),
                        'is_strip_tags' => true,
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ])
                ]
            ]
        ];
    }

    private function getFormCategories($ctype) {

        $level_offset   = 0;
        $last_header_id = false;
        $items          = ['' => ''];

        $tree = cmsCore::getModel('content')->limit(0)->getCategoriesTree($ctype['name']);
        if (!$tree) {
            return $items;
        }

        foreach ($tree as $c) {

            if ($ctype['options']['is_cats_only_last']) {

                $dash_pad = $c['ns_level'] - 1 >= 0 ? str_repeat('-', $c['ns_level'] - 1) . ' ' : '';

                if ($c['ns_right'] - $c['ns_left'] == 1) {
                    if ($last_header_id !== false && $last_header_id != $c['parent_id']) {
                        $items['opt' . $c['id']] = [str_repeat('-', $c['ns_level'] - 1) . ' ' . $c['title']];
                    }
                    $items[$c['id']] = $dash_pad . $c['title'];
                } else if ($c['parent_id'] > 0) {
                    $items['opt' . $c['id']] = [$dash_pad . $c['title']];
                    $last_header_id = $c['id'];
                }

                continue;
            }

            if (!$ctype['options']['is_cats_only_last']) {

                if ($c['parent_id'] == 0 && !$ctype['options']['is_cats_open_root']) {
                    $level_offset = 1;
                    continue;
                }

                $items[$c['id']] = str_repeat('-- ', $c['ns_level'] - $level_offset) . ' ' . $c['title'];

                continue;
            }
        }

        return $items;
    }

    private function getContentPropsBind($ctype_name) {

        $model = cmsCore::getModel('content');

        $bind_table_name = $model->table_prefix . $ctype_name . '_props_bind';

        $items = $model->get($bind_table_name);

        $result = [];

        if ($items) {
            foreach ($items as $item) {
                $result[$item['prop_id']][] = $item['cat_id'];
            }
        }

        return $result;
    }
}
