<?php

class formWidgetContentListOptions extends cmsForm {

    public function init($options = false) {

        $content_model = cmsCore::getModel('content');

        $field_generator = function ($item, $request) use ($content_model) {
            $list     = ['' => ''];
            $ctype_id = is_array($item) ? array_value_recursive('options:ctype_id', $item) : false;
            if (!$ctype_id && $request) {
                $ctype_id = $request->get('options:ctype_id', 0);
            }
            if (!$ctype_id) {
                return $list;
            }
            $ctype = $content_model->getContentType($ctype_id);
            if (!$ctype) {
                return $list;
            }
            $fields = $content_model->getContentFields($ctype['name']);
            if ($fields) {
                $list += array_collection_to_list($fields, 'name', 'title');
            }
            return $list;
        };

        return [
            'woptions' => [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:widget_type', [
                        'title'   => LANG_WD_CONTENT_WIDGET_TYPE,
                        'default' => 'list',
                        'items'   => [
                            'list'    => LANG_WD_CONTENT_WIDGET_TYPE1,
                            'related' => LANG_WD_CONTENT_WIDGET_TYPE2,
                            'random'  => LANG_WD_CONTENT_WIDGET_TYPE3
                        ]
                    ]),
                    new fieldList('options:related_type', [
                        'title'   => LANG_WD_CONTENT_RELATED_TYPE,
                        'default' => 'title',
                        'items'   => [
                            'title' => LANG_WD_CONTENT_RELATED_TYPE1,
                            'tags'  => LANG_WD_CONTENT_RELATED_TYPE2,
                            'cat'   => LANG_WD_CONTENT_RELATED_TYPE3
                        ],
                        'visible_depend' => ['options:widget_type' => ['show' => ['related']]]
                    ]),
                    new fieldList('options:ctype_id', [
                        'title'     => LANG_CONTENT_TYPE,
                        'generator' => function ($ctype) use ($content_model) {

                            $tree = $content_model->getContentTypes();

                            $items = [0 => LANG_WD_CONTENT_FILTER_DETECT];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = $item['title'];
                                }
                            }
                            return $items;
                        },
                    ]),
                    new fieldList('options:category_id', [
                        'title'     => LANG_CATEGORY,
                        'parent'    => [
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_cats_ajax')
                        ],
                        'generator' => function ($item, $request) use ($content_model) {
                            $list     = ['' => ''];
                            $ctype_id = is_array($item) ? array_value_recursive('options:ctype_id', $item) : false;
                            if (!$ctype_id && $request) {
                                $ctype_id = $request->get('options:ctype_id', 0);
                            }
                            if (!$ctype_id) {
                                return $list;
                            }
                            $ctype = $content_model->getContentType($ctype_id);
                            if (!$ctype) {
                                return $list;
                            }

                            $cats = $content_model->getCategoriesTree($ctype['name']);

                            if ($cats) {
                                foreach ($cats as $cat) {
                                    if ($cat['ns_level'] > 1) {
                                        $cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
                                    }
                                    $list[$cat['id']] = $cat['title'];
                                }
                            }
                            return $list;
                        },
                        'visible_depend' => ['options:ctype_id' => ['hide' => ['0']]]
                    ]),
                    new fieldList('options:dataset', [
                        'title'     => LANG_WD_CONTENT_LIST_DATASET,
                        'parent'    => [
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_datasets_ajax')
                        ],
                        'generator' => function ($item, $request) use ($content_model) {
                            $list     = ['0' => ''];
                            $ctype_id = is_array($item) ? array_value_recursive('options:ctype_id', $item) : false;
                            if (!$ctype_id && $request) {
                                $ctype_id = $request->get('options:ctype_id', 0);
                            }
                            if (!$ctype_id) {
                                return $list;
                            }
                            $datasets = $content_model->getContentDatasets($ctype_id);
                            if ($datasets) {
                                $list += array_collection_to_list($datasets, 'id', 'title');
                            }
                            return $list;
                        },
                        'visible_depend' => ['options:ctype_id' => ['hide' => ['0']]]
                    ]),
                    new fieldList('options:relation_id', [
                        'title'     => LANG_WD_CONTENT_LIST_RELATION,
                        'parent'    => [
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_relations_ajax')
                        ],
                        'generator' => function ($item, $request) use ($content_model) {
                            $list     = ['0' => ''];
                            $ctype_id = is_array($item) ? array_value_recursive('options:ctype_id', $item) : false;
                            if (!$ctype_id && $request) {
                                $ctype_id = $request->get('options:ctype_id', 0);
                            }
                            if (!$ctype_id) {
                                return $list;
                            }
                            $ctype = $content_model->getContentType($ctype_id);
                            if (!$ctype) {
                                return $list;
                            }
                            $parents = $content_model->getContentTypeParents($ctype_id);
                            if (is_array($parents)) {
                                foreach ($parents as $parent) {
                                    $list[$parent['id']] = "{$ctype['title']} > {$parent['ctype_title']}";
                                }
                            }
                            return $list;
                        },
                        'visible_depend' => ['options:ctype_id' => ['hide' => ['0']]]
                    ]),
                    new fieldList('options:filter_id', [
                        'title'     => LANG_WD_CONTENT_LIST_FILTER,
                        'parent'    => [
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_filters_ajax')
                        ],
                        'generator' => function ($item, $request) use ($content_model) {
                            $list     = ['0' => ''];
                            $ctype_id = is_array($item) ? array_value_recursive('options:ctype_id', $item) : false;
                            if (!$ctype_id && $request) {
                                $ctype_id = $request->get('options:ctype_id', 0);
                            }
                            if (!$ctype_id) {
                                return $list;
                            }
                            $ctype = $content_model->getContentType($ctype_id);
                            if (!$ctype) {
                                return $list;
                            }
                            if (!$content_model->isFiltersTableExists($ctype['name'])) {
                                return $list;
                            }
                            $filters = $content_model->getContentFilters($ctype['name']);
                            if (is_array($filters)) {
                                foreach ($filters as $filter) {
                                    $list[$filter['id']] = $filter['title'];
                                }
                            }
                            return $list;
                        },
                        'visible_depend' => ['options:ctype_id' => ['hide' => ['0']]]
                    ]),
                    new fieldString('options:filter_hook', [
                        'title'          => LANG_WD_CONTENT_LIST_FILTER_HOOK,
                        'hint'           => LANG_WD_CONTENT_LIST_FILTER_HOOK_HINT,
                        'visible_depend' => ['options:ctype_id' => ['hide' => ['0']]]
                    ]),
                    new fieldCheckbox('options:auto_group', [
                        'title' => LANG_CP_WO_AUTO_GROUP,
                        'hint'  => LANG_CP_WO_AUTO_GROUP_HINT
                    ]),
                    new fieldCheckbox('options:auto_user', [
                        'title' => LANG_WD_CONTENT_AUTO_USER,
                        'hint'  => LANG_WD_CONTENT_AUTO_USER_HINT
                    ]),
                    new fieldNumber('options:offset', [
                        'title'   => LANG_LIST_OFFSET,
                        'hint'    => LANG_LIST_OFFSET_HINT,
                        'default' => 0
                    ]),
                    new fieldNumber('options:limit', [
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ],
            'fields_options' => [
                'type'     => 'fieldset',
                'title'    => LANG_CP_CTYPE_FIELDS,
                'is_empty' => true,
                'parent'   => [
                    'list' => 'options:ctype_id',
                    'url'  => href_to('content', 'widget_fields_options_ajax')
                ],
                'childs'   => [
                    new cmsFormField('fake', [
                        'title' => LANG_CP_CTYPE_NOT_SET,
                        'html'  => ''
                    ])
                ]
            ],
            'deprecated'     => [
                'type'   => 'fieldset',
                'title'  => LANG_WD_CONTENT_DEPRECATED,
                'childs' => [
                    new cmsFormField('fake_deprecated_hint', [
                        'title' => '',
                        'hint'  => LANG_WD_CONTENT_LIST_FIELD_HINT,
                        'html'  => ''
                    ]),
                    new fieldList('options:image_field', [
                        'title'     => LANG_WD_CONTENT_LIST_IMAGE,
                        'parent'    => [
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ],
                        'generator' => $field_generator
                    ]),
                    new fieldList('options:teaser_field', [
                        'title'     => LANG_WD_CONTENT_LIST_TEASER,
                        'parent'    => [
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ],
                        'generator' => $field_generator
                    ]),
                    new fieldNumber('options:teaser_len', [
                        'title' => LANG_PARSER_HTML_TEASER_LEN,
                        'hint'  => LANG_PARSER_HTML_TEASER_LEN_HINT
                    ]),
                    new fieldCheckbox('options:show_details', [
                        'title' => LANG_WD_CONTENT_LIST_DETAILS
                    ])
                ]
            ]
        ];
    }

}
