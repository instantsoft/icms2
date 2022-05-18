<?php

class formWidgetContentSliderOptions extends cmsForm {

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
                $list += array_collection_to_list($fields, 'name', 'title');
            }
            return $list;
        };

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:ctype_id', array(
                        'title'     => LANG_CONTENT_TYPE,
                        'default'   => 1,
                        'generator' => function($ctype) {
                            $model = cmsCore::getModel('content');
                            $tree  = $model->getContentTypes();
                            $items = [];
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
                        }
                    )),
                    new fieldList('options:dataset', array(
                        'title'     => LANG_WD_CONTENT_SLIDER_DATASET,
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
                                $list += array_collection_to_list($datasets, 'id', 'title');
                            }
                            return $list;
                        }
                    )),
                    new fieldList('options:image_field', array(
                        'title'     => LANG_WD_CONTENT_SLIDER_IMAGE,
                        'rules'     => array(
                            array('required')
                        ),
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ),
                        'generator' => $field_generator
                    )),
                    new fieldList('options:big_image_field', array(
                        'title'     => LANG_WD_CONTENT_SLIDER_BIG_IMAGE,
                        'hint'      => LANG_WD_CONTENT_SLIDER_BIG_IMAGE_HINT,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ),
                        'generator' => $field_generator
                    )),
                    new fieldList('options:big_image_preset', array(
                        'title'     => LANG_WD_CONTENT_SLIDER_BIG_IMAGE_PRESET,
                        'generator' => function($item) {
                            return cmsCore::getModel('images')->getPresetsList(true) + array('original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL);
                        },
                    )),
                    new fieldList('options:teaser_field', array(
                        'title'     => LANG_WD_CONTENT_SLIDER_TEASER,
                        'parent'    => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ),
                        'generator' => $field_generator
                    )),
                    new fieldNumber('options:teaser_len', array(
                        'title' => LANG_PARSER_HTML_TEASER_LEN,
                        'hint'  => LANG_PARSER_HTML_TEASER_LEN_HINT,
                    )),
                    new fieldNumber('options:delay', array(
                        'title'   => LANG_WD_CONTENT_SLIDER_DELAY,
                        'hint'    => LANG_WD_CONTENT_SLIDER_DELAY_HINT,
                        'default' => 5,
                        'units'   => LANG_SECOND10
                    )),
                    new fieldNumber('options:limit', array(
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 4,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ))
                )
            )
        );

    }

}
