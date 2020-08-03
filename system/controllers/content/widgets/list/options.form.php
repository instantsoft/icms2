<?php

class formWidgetContentListOptions extends cmsForm {

    public function init($options = false) {

        $content_model = cmsCore::getModel('content');

        $field_generator = function ($item, $request) use($content_model) {
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
                $list = $list + array_collection_to_list($fields, 'name', 'title');
            }
            return $list;
        };

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:widget_type', array(
                        'title'   => LANG_WD_CONTENT_WIDGET_TYPE,
                        'default' => 'list',
                        'items'   => array(
                            'list'    => LANG_WD_CONTENT_WIDGET_TYPE1,
                            'related' => LANG_WD_CONTENT_WIDGET_TYPE2
                        )
                    )),
                    new fieldList('options:ctype_id', array(
                        'title'     => LANG_CONTENT_TYPE,
                        'generator' => function($ctype) use($content_model) {

                            $tree = $content_model->getContentTypes();

                            $items = array(0 => LANG_WD_CONTENT_FILTER_DETECT);

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = $item['title'];
                                }
                            }
                            return $items;
                        },
                    )),
                    new fieldList('options:category_id', array(
                        'title'     => LANG_CATEGORY,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_cats_ajax')
                        ),
                        'generator' => function($item, $request) use($content_model) {
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
                        'visible_depend' => array('options:ctype_id' => array('hide' => array('0')))
                    )),
                    new fieldList('options:dataset', array(
                        'title'     => LANG_WD_CONTENT_LIST_DATASET,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_datasets_ajax')
                        ),
                        'generator' => function($item, $request) use($content_model) {
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
                                $list = $list + array_collection_to_list($datasets, 'id', 'title');
                            }
                            return $list;
                        },
                        'visible_depend' => array('options:ctype_id' => array('hide' => array('0')))
                    )),
                    new fieldList('options:relation_id', array(
                        'title'     => LANG_WD_CONTENT_LIST_RELATION,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_relations_ajax')
                        ),
                        'generator' => function($item, $request) use($content_model) {
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
                        'visible_depend' => array('options:ctype_id' => array('hide' => array('0')))
                    )),
                    new fieldList('options:image_field', array(
                        'title'     => LANG_WD_CONTENT_LIST_IMAGE,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ),
                        'generator' => $field_generator,
                        'visible_depend' => array('options:ctype_id' => array('hide' => array('0')))
                    )),
                    new fieldList('options:teaser_field', array(
                        'title'     => LANG_WD_CONTENT_LIST_TEASER,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ),
                        'generator' => $field_generator,
                        'visible_depend' => array('options:ctype_id' => array('hide' => array('0')))
                    )),
                    new fieldCheckbox('options:auto_group', array(
                        'title' => LANG_CP_WO_AUTO_GROUP,
                        'hint'  => LANG_CP_WO_AUTO_GROUP_HINT
                    )),
                    new fieldList('options:style', array(
                        'title'   => LANG_WD_CONTENT_LIST_STYLE,
                        'default' => 'basic',
                        'items'   => array(
                            'basic'       => LANG_WD_CONTENT_LIST_STYLE_BASIC,
                            'featured'    => LANG_WD_CONTENT_LIST_STYLE_FEATURED,
                            'tiles_big'   => LANG_WD_CONTENT_LIST_STYLE_TILES_BIG,
                            'tiles_small' => LANG_WD_CONTENT_LIST_STYLE_TILES_SMALL,
                            'compact'     => LANG_WD_CONTENT_LIST_STYLE_COMPACT,
                            ''            => LANG_WD_CONTENT_LIST_STYLE_CUSTOM
                        )
                    )),
                    new fieldCheckbox('options:show_details', array(
                        'title' => LANG_WD_CONTENT_LIST_DETAILS
                    )),
                    new fieldNumber('options:teaser_len', array(
                        'title' => LANG_PARSER_HTML_TEASER_LEN,
                        'hint'  => LANG_PARSER_HTML_TEASER_LEN_HINT
                    )),
                    new fieldNumber('options:limit', array(
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules'   => array(
                            array('required')
                        )
                    ))
                )
            )
        );

    }

}
