<?php
class formAdminCtypesBasic extends cmsForm {

    public function init($do, $ctype) {

        $template = new cmsTemplate(cmsConfig::get('template'));

        $meta_item_fields = '<a href="#">{'.implode('}</a> <a href="#">{', [
            'title',
            'description',
            'ctype_title',
            'ctype_description',
            'ctype_label1',
            'ctype_label2',
            'ctype_label10',
            'filter_string'
        ]).'}</a>';

        $item_fields = ['category'];

        if(!empty($ctype['name'])){

            $_item_fields = cmsCore::getModel('content')->orderBy('ordering')->getContentFields($ctype['name']);

            foreach ($_item_fields as $field) {

                $item_fields[] = $field['name'];

            }

        }

        $item_fields = '<a href="#">{'.implode('}</a> <a href="#">{', $item_fields).'}</a>';

        return array(
            'titles' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'hint' => LANG_CP_SYSTEM_NAME_HINT,
                        'options'=>array(
                            'max_length' => 32,
                            'show_symbol_count' => true
                        ),
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            $do == 'add' ? array('unique', 'content_types', 'name') : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'options'=>array(
                            'max_length' => 100,
                            'show_symbol_count' => true
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldHtml('description', array(
                        'title' => LANG_DESCRIPTION
                    )),
                )
            ),
            'pub' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_PUBLICATION,
                'childs' => array(
                    new fieldCheckbox('is_date_range', array(
                        'title' => LANG_CP_IS_PUB_CONTROL,
                        'hint' => LANG_CP_IS_PUB_CONTROL_HINT
                    )),
                    new fieldList('options:is_date_range_process', array(
                        'title' => LANG_CP_IS_PUB_CONTROL_PROCESS,
                        'default' => 'hide',
                        'items' => array(
                            'hide'      => LANG_CP_IS_PUB_CONTROL_PROCESS_HIDE,
                            'delete'    => LANG_CP_IS_PUB_CONTROL_PROCESS_DEL,
                            'in_basket' => LANG_BASKET_DELETE
                        ),
                        'visible_depend' => array('is_date_range' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:disable_drafts', array(
                        'title' => LANG_CP_DISABLE_DRAFTS
                    ))
                )
            ),
            'categories' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CATEGORIES,
                'childs' => array(
                    new fieldCheckbox('is_cats', array(
                        'title' => LANG_CP_CATEGORIES_ON
                    )),
                    new fieldCheckbox('is_cats_recursive', array(
                        'title' => LANG_CP_CATEGORIES_RECURSIVE,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:is_empty_root', array(
                        'title' => LANG_CP_CATEGORIES_EMPTY_ROOT,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:is_cats_multi', array(
                        'title' => LANG_CP_CATEGORIES_MULTI,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:is_cats_change', array(
                        'title' => LANG_CP_CATEGORIES_CHANGE,
                        'default' => true,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:is_cats_open_root', array(
                        'title' => LANG_CP_CATEGORIES_OPEN_ROOT,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:is_cats_only_last', array(
                        'title' => LANG_CP_CATEGORIES_ONLY_LAST,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:is_show_cats', array(
                        'title' => LANG_CP_CATEGORIES_SHOW,
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldListMultiple('options:cover_sizes', array(
                        'title' => LANG_CP_CAT_COVER_SIZES,
                        'default' => array(),
                        'generator' => function (){
                            $presets = cmsCore::getModel('images')->getPresetsList();
                            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                            return $presets;
                        },
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    )),
                    new fieldList('options:context_list_cover_sizes', array(
                        'title'        => LANG_CP_CAT_CONTEXT_LIST_COVER_SIZES,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'generator' => function($ctype) use ($template){
                            return $template->getAvailableContentListStyles();
                        },
                        'values_generator' => function() {
                            $presets = cmsCore::getModel('images')->getPresetsList();
                            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                            return $presets;
                        },
                        'visible_depend' => array('is_cats' => array('show' => array('1')))
                    ))
                )
            ),
            'folders' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_FOLDERS,
                'childs' => array(
                    new fieldCheckbox('is_folders', array(
                        'title' => LANG_CP_FOLDERS_ON,
                        'hint' => LANG_CP_FOLDERS_HINT
                    )),
                )
            ),
            'groups' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_CT_GROUPS,
                'childs' => array(
                    new fieldCheckbox('is_in_groups', array(
                        'title' => LANG_CP_CT_GROUPS_ALLOW
                    )),
                    new fieldCheckbox('is_in_groups_only', array(
                        'title' => LANG_CP_CT_GROUPS_ALLOW_ONLY,
                        'visible_depend' => array('is_in_groups' => array('show' => array('1')))
                    )),
                )
            ),
            'comments' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_COMMENTS,
                'childs' => array(
                    new fieldCheckbox('is_comments', array(
                        'title' => LANG_CP_COMMENTS_ON
                    )),
                )
            ),
            'ratings' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_RATING,
                'childs' => array(
                    new fieldCheckbox('is_rating', array(
                        'title' => LANG_CP_RATING_ON
                    )),
                )
            ),
            'listview' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_LISTVIEW_OPTIONS,
                'childs' => array(
                    new fieldCheckbox('options:list_off_breadcrumb', array(
                        'title' => LANG_CP_LIST_OFF_BREADCRUMB
                    )),
                    new fieldCheckbox('options:list_on', array(
                        'title' => LANG_CP_LISTVIEW_ON,
                        'default' => true
                    )),
                    new fieldCheckbox('options:profile_on', array(
                        'title' => LANG_CP_PROFILELIST_ON,
                        'default' => true
                    )),
                    new fieldCheckbox('options:list_show_filter', array(
                        'title' => LANG_CP_LISTVIEW_FILTER
                    )),
                    new fieldCheckbox('options:list_expand_filter', array(
                        'title' => LANG_CP_LISTVIEW_FILTER_EXPAND,
                        'visible_depend' => array('options:list_show_filter' => array('show' => array('1')))
                    )),
                    new fieldList('options:privacy_type', array(
                        'title'   => LANG_CP_PRIVACY_TYPE,
                        'default' => 'hide',
                        'items'   => array(
                            'hide'       => LANG_CP_PRIVACY_TYPE_HIDE,
                            'show_title' => LANG_CP_PRIVACY_TYPE_SHOW_TITLE,
                            'show_all'   => LANG_CP_PRIVACY_TYPE_SHOW_ALL
                        )
                    )),
                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldList('options:list_style', array(
                        'title' => LANG_CP_LISTVIEW_STYLE,
                        'is_chosen_multiple' => true,
                        'hint' => sprintf(LANG_CP_LISTVIEW_STYLE_HINT, $template->getName()),
                        'generator' => function() use($template){
                            return $template->getAvailableContentListStyles();
                        }
                    )),
                    new fieldList('options:list_style_names', array(
                        'title'        => LANG_CP_LIST_STYLE_NAMES,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'multiple_keys' => array(
                            'name' => 'field', 'value' => 'field_value'
                        ),
                        'generator' => function($ctype) use ($template){
                            return $template->getAvailableContentListStyles();
                        }
                    )),
                    new fieldList('options:context_list_style', array(
                        'title'        => LANG_CP_CONTEXT_LIST_STYLE,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_CP_CONTEXT_SELECT_LIST,
                        'generator' => function($ctype) use ($do){

                            $lists = cmsEventsManager::hookAll('ctype_lists_context', 'template'.($do != 'add' ? ':'.$ctype['name'] : ''));

                            $items = array();

                            if($lists){
                                foreach ($lists as $list) {
                                    $items = array_merge($items, $list);
                                }
                            }

                            return $items;
                        },
                        'values_generator' => function() use($template){
                            return $template->getAvailableContentListStyles();
                        }
                    ))
                )
            ),
            'itemview' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_ITEMVIEW_OPTIONS,
                'childs' => array(
                    new fieldCheckbox('options:item_off_breadcrumb', array(
                        'title' => LANG_CP_LIST_OFF_BREADCRUMB
                    )),
                    new fieldCheckbox('options:item_on', array(
                        'title' => LANG_CP_ITEMVIEW_ON,
                        'default' => true
                    )),
                    new fieldCheckbox('options:is_show_fields_group', array(
                        'title' => LANG_CP_ITEMVIEW_FIELDS_GROUP,
                        'visible_depend' => array('options:item_on' => array('show' => array('1')))
                    )),
                    new fieldCheckbox('options:hits_on', array(
                        'title' => LANG_CP_ITEMVIEW_HITS_ON,
                        'visible_depend' => array('options:item_on' => array('show' => array('1')))
                    )),
                    new fieldText('options:share_code', array(
                        'title' => LANG_CP_ITEMVIEW_SHARE_CODE,
                        'visible_depend' => array('options:item_on' => array('show' => array('1')))
                    )),
                    new fieldText('item_append_html', array(
                        'title' => LANG_CP_ITEMVIEW_APPEND_HTML,
                        'hint' => LANG_CP_ITEMVIEW_APPEND_HTML_HINT,
                        'visible_depend' => array('options:item_on' => array('show' => array('1')))
                    )),
                )
            ),
            'seo-items' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_SEOMETA,
                'childs' => array(
                    new fieldCheckbox('options:is_manual_title', array(
                        'title' => LANG_CP_SEOMETA_MANUAL_TITLE,
                    )),
                    new fieldCheckbox('is_auto_keys', array(
                        'title' => LANG_CP_SEOMETA_AUTO_KEYS,
                        'default' => true
                    )),
                    new fieldCheckbox('is_auto_desc', array(
                        'title' => LANG_CP_SEOMETA_AUTO_DESC,
                        'default' => true
                    )),
                    new fieldCheckbox('is_auto_url', array(
                        'title' => LANG_CP_AUTO_URL,
                        'default' => true
                    )),
                    new fieldCheckbox('is_fixed_url', array(
                        'title' => LANG_CP_FIXED_URL
                    )),
                    new fieldString('url_pattern', array(
                        'title' => LANG_CP_URL_PATTERN,
                        'prefix' => '/articles/',
                        'suffix' => '.html',
                        'default' => '{id}-{title}',
                        'options'=>array(
                            'max_length' => 255,
                            'show_symbol_count' => true
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldString('options:seo_title_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $item_fields)
                    )),
                    new fieldString('options:seo_keys_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $item_fields)
                    )),
                    new fieldString('options:seo_desc_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $item_fields)
                    ))
                )
            ),
            'seo-cats' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_SEOMETA_CATS,
                'childs' => array(
                    new fieldCheckbox('options:is_cats_title', array(
                        'title' => LANG_CP_SEOMETA_CATS_TITLE
                    )),
                    new fieldCheckbox('options:is_cats_h1', array(
                        'title' => LANG_CP_SEOMETA_CATS_H1
                    )),
                    new fieldCheckbox('options:is_cats_keys', array(
                        'title' => LANG_CP_SEOMETA_CATS_KEYS
                    )),
                    new fieldCheckbox('options:is_cats_desc', array(
                        'title' => LANG_CP_SEOMETA_CATS_DESC
                    )),
                    new fieldCheckbox('options:is_cats_auto_url', array(
                        'title' => LANG_CP_CATS_AUTO_URL,
                        'default' => true
                    )),
                    new fieldString('options:seo_cat_h1_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $meta_item_fields)
                    )),
                    new fieldString('options:seo_cat_title_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $meta_item_fields)
                    )),
                    new fieldString('options:seo_cat_keys_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $meta_item_fields)
                    )),
                    new fieldString('options:seo_cat_desc_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint' => sprintf(LANG_CP_SEOMETA_FIELDS, $meta_item_fields)
                    ))
                )
            ),
            'seo' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_SEOMETA_DEFAULT,
                'childs' => array(
                    new fieldString('seo_title', array(
                        'title' => LANG_SEO_TITLE,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),
                    new fieldString('seo_keys', array(
                        'title' => LANG_SEO_KEYS,
                        'hint' => LANG_SEO_KEYS_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),
                    new fieldText('seo_desc', array(
                        'title' => LANG_SEO_DESC,
                        'hint' => LANG_SEO_DESC_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    ))
                )
            ),
            'collapsed' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_IS_COLLAPSED,
                'childs' => array(
                    new fieldListMultiple('options:is_collapsed', array(
                        'generator' => function ($ctype) use($do){

                            $items = array(
                                'folder' => LANG_CP_FOLDERS,
                                'group_wrap' => LANG_CP_CT_GROUPS
                            );

                            if($do != 'add'){

                                $model = cmsCore::getModel('content');

                                $fieldset_titles = $model->orderBy('ordering')->getContentFieldsets($ctype['id']);

                                if($fieldset_titles){
                                    foreach ($fieldset_titles as $fieldset) {
                                        $items[md5($fieldset)] = $fieldset;
                                    }
                                }

                            }

                            return $items + array(
                                'tags_wrap'    => LANG_TAGS,
                                'privacy_wrap' => LANG_CP_FIELD_PRIVACY,
                                'is_comment'   => LANG_CP_COMMENTS,
                                'seo_wrap'     => LANG_SEO,
                                'pub_wrap'     => LANG_CP_PUBLICATION,
                            );

                        }
                    ))
                )
            )
        );

    }

}
