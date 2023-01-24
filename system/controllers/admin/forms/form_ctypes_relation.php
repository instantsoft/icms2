<?php

class formAdminCtypesRelation extends cmsForm {

    public function init($do, $ctype_id) {

        $content_model = cmsCore::getModel('content');

        return [
            'basic' => [
                'type' => 'fieldset',
                'childs' => [

                    new fieldList('child_ctype_id', [
                        'title' => LANG_CP_RELATION_CHILD,
                        'generator' => function () use ($ctype_id, $do, $content_model) {

                            $items = $rel_names = [];

                            $relation_childs = cmsEventsManager::hookAll('ctype_relation_childs', $ctype_id);

                            if (is_array($relation_childs)) {

                                $relations = $content_model->getContentTypeChilds($ctype_id);

                                if ($relations) {
                                    foreach ($relations as $relation) {
                                        $rel_names[] = $relation['target_controller'] . ':' . $relation['child_ctype_id'];
                                    }
                                }

                                foreach ($relation_childs as $relation_child) {
                                    foreach ($relation_child['types'] as $name => $title) {
                                        if ($do === 'add') {
                                            if (!in_array($name, $rel_names)) {
                                                $items[$name] = $title;
                                            }
                                        } else {
                                            $items[$name] = $title;
                                        }
                                    }
                                }
                            }

                            return $items;
                        }
                    ]),

                    new fieldString('title', [
                        'title' => LANG_CP_RELATION_TITLE,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_relations'
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),

                    new fieldList('options:dataset_id', [
                        'title' => LANG_CP_CTYPE_DATASET,
                        'parent' => [
                            'list' => 'child_ctype_id',
                            'url'  => href_to('content', 'widget_datasets_ajax')
                        ],
                        'generator' => function ($item, $request) use ($content_model) {
                            $list     = ['0' => ''];
                            $ctype_id = is_array($item) ? array_value_recursive('child_ctype_id', $item) : false;
                            if ($request) {
                                $ctype_id = $request->get('child_ctype_id', '');
                            }
                            if (!$ctype_id) {
                                return $list;
                            }
                            list($target, $id) = explode(':', $ctype_id);
                            $datasets = $content_model->getContentDatasets($id ? $id : $target);
                            if ($datasets) {
                                $list += array_collection_to_list($datasets, 'id', 'title');
                            }
                            return $list;
                        }
                    ])

                ]
            ],
            'layout' => [
                'type' => 'fieldset',
                'title' => LANG_CP_RELATION_LAYOUT,
                'childs' => [

                    new fieldList('layout', [
                        'title' => LANG_CP_RELATION_LAYOUT_TYPE,
                        'items' => [
                            'list' => LANG_CP_RELATION_LAYOUT_LIST,
                            'tab' => LANG_CP_RELATION_LAYOUT_TAB,
                            'hidden' => LANG_CP_RELATION_LAYOUT_HIDDEN,
                        ]
                    ]),

                    new fieldNumber('options:limit', [
                        'title' => LANG_CP_RELATION_LAYOUT_LIMIT,
                        'hint' => LANG_CP_RELATION_LAYOUT_LIMIT_HINT,
                        'default' => 10,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),

                    new fieldCheckbox('options:is_hide_empty', [
                        'title' => LANG_CP_RELATION_LAYOUT_HIDE_EMPTY
                    ]),

                    new fieldCheckbox('options:is_hide_title', [
                        'title' => LANG_CP_RELATION_LAYOUT_HIDE_TITLE
                    ]),

                    new fieldCheckbox('options:is_hide_filter', [
                        'title' => LANG_CP_RELATION_LAYOUT_HIDE_FILTER
                    ])
                ]
            ],
            'tab-opts' => [
                'type' => 'fieldset',
                'title' => LANG_CP_RELATION_TAB_OPTS,
                'childs' => [

                    new fieldString('seo_title', [
                        'title' => LANG_CP_RELATION_TAB_SEO_TITLE,
                        'hint' => LANG_CP_RELATION_TAB_SEO_HINT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_relations'
                        ],
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),

                    new fieldString('seo_keys', [
                        'title' => LANG_CP_RELATION_TAB_SEO_KEYS,
                        'hint' => LANG_CP_RELATION_TAB_SEO_HINT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_relations'
                        ],
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ]),

                    new fieldText('seo_desc', [
                        'title' => LANG_CP_RELATION_TAB_SEO_DESC,
                        'hint' => LANG_CP_RELATION_TAB_SEO_HINT,
                        'can_multilanguage' => true,
                        'multilanguage_params' => [
                            'is_table_field' => true,
                            'table' => 'content_relations'
                        ],
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
}
