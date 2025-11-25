<?php

class formWidgetContentCategoriesOptions extends cmsForm {

    public function init($options = false) {

        $preset_generator = function ($item, $request) {

            $list = ['' => ''];

            $ctype_name = is_array($item) ? array_value_recursive('options:ctype_name', $item) : false;

            if (!$ctype_name && $request) {
                $ctype_name = $request->get('options:ctype_name', '');
            }
            if (!$ctype_name) {
                return $list;
            }

            $presets = cmsCore::getModel('images')->getPresetsList();
            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;

            if ($ctype_name) {

                $ctype = cmsCore::getModel('content')->getContentTypeByName($ctype_name);
                if ($ctype) {

                    $_presets = [];

                    if ($presets && !empty($ctype['options']['cover_sizes'])) {
                        foreach ($presets as $key => $name) {
                            if (in_array($key, $ctype['options']['cover_sizes'])) {
                                $_presets[$key] = $name;
                            }
                        }
                    }

                    $presets = $_presets ?: $presets;
                }
            }

            return $list + $presets;
        };

        return [
            [
                'type' => 'fieldset',
                'title' => LANG_CONTENT_TYPE,
                'childs' => [
                    new fieldList('options:ctype_name', [
                        'generator' => function($c) {

                            $model = cmsCore::getModel('content');
                            $tree = $model->getContentTypes();

                            $items = [0 => LANG_WD_CONTENT_FILTER_DETECT];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldList('options:cover_preset', [
                        'title' => LANG_CP_CAT_CONTEXT_LIST_COVER_SIZES,
                        'parent' => [
                            'list' => 'options:ctype_name',
                            'url' => href_to('content', 'widget_cats_presets_ajax')
                        ],
                        'generator' => $preset_generator
                    ]),
                    new fieldList('options:category_id', [
                        'title'     => LANG_CATEGORY,
                        'parent'    => [
                            'list' => 'options:ctype_name',
                            'url'  => href_to('content', 'widget_cats_ajax', false, ['empty_title' => LANG_ALL])
                        ],
                        'generator' => function ($item, $request) {

                            $content_model = cmsCore::getModel('content');

                            $list = ['' => LANG_ALL];

                            $ctype_name = is_array($item) ? array_value_recursive('options:ctype_name', $item) : false;
                            if (!$ctype_name && $request) {
                                $ctype_name = $request->get('options:ctype_name', '');
                            }
                            if (!$ctype_name) {
                                return $list;
                            }
                            $ctype = $content_model->getContentTypeByName($ctype_name);
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
                        'visible_depend' => ['options:ctype_name' => ['hide' => ['0']]]
                    ])
                ]
            ],
            [
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => [
                    new fieldCheckbox('options:show_counts', [
                        'title' => LANG_CP_CATEGORIES_ITEMS_COUNT,
                        'default' => false
                    ]),
                    new fieldCheckbox('options:is_root', [
                        'title' => LANG_WD_CONTENT_CATS_SHOW_ROOT,
                        'default' => false
                    ]),
                    new fieldString('options:root_cat_title', [
                        'title' => LANG_WD_CONTENT_ROOT_CAT_TITLE,
                        'multilanguage' => true,
                        'visible_depend' => ['options:is_root' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('options:show_full_tree', [
                        'title' => LANG_WD_CONTENT_CATS_SHOW_FULL_TREE,
                        'default' => false
                    ])
                ]
            ]
        ];
    }

}
