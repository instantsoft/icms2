<?php
class formAdminCtypesBasic extends cmsForm {

    public function init($do) {

        $template = cmsTemplate::getInstance();

        return array(
            'titles' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'hint' => LANG_CP_SYSTEM_NAME_HINT,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            $do == 'add' ? array('unique', 'content_types', 'name') : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('description', array(
                        'title' => LANG_DESCRIPTION,
                        'rules' => array(
                            array('max_length', 255)
                        )
                    )),
                )
            ),
            'pub' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_PUBLICATION,
                'childs' => array(
                    new fieldCheckbox('is_premod_add', array(
                        'title' => LANG_CP_PREMOD_ADD
                    )),
                    new fieldCheckbox('is_premod_edit', array(
                        'title' => LANG_CP_PREMOD_EDIT
                    )),
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
                        )
                    ))
                )
            ),
            'categories' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_CATEGORIES,
                'childs' => array(
                    new fieldCheckbox('is_cats', array(
                        'title' => LANG_CP_CATEGORIES_ON
                    )),
                    new fieldCheckbox('is_cats_recursive', array(
                        'title' => LANG_CP_CATEGORIES_RECURSIVE
                    )),
                    new fieldCheckbox('options:is_empty_root', array(
                        'title' => LANG_CP_CATEGORIES_EMPTY_ROOT
                    )),
                    new fieldCheckbox('options:is_cats_multi', array(
                        'title' => LANG_CP_CATEGORIES_MULTI
                    )),
                    new fieldCheckbox('options:is_cats_change', array(
                        'title' => LANG_CP_CATEGORIES_CHANGE,
                        'default' => true
                    )),
                    new fieldCheckbox('options:is_cats_open_root', array(
                        'title' => LANG_CP_CATEGORIES_OPEN_ROOT
                    )),
                    new fieldCheckbox('options:is_cats_only_last', array(
                        'title' => LANG_CP_CATEGORIES_ONLY_LAST
                    )),
                    new fieldCheckbox('options:is_show_cats', array(
                        'title' => LANG_CP_CATEGORIES_SHOW
                    )),
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
                        'title' => LANG_CP_CT_GROUPS_ALLOW_ONLY
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
            'tags' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_TAGS,
                'childs' => array(
                    new fieldCheckbox('is_tags', array(
                        'title' => LANG_CP_TAGS_ON
                    )),
                    new fieldCheckbox('options:is_tags_in_list', array(
                        'title' => LANG_CP_TAGS_IN_LIST
                    )),
                    new fieldCheckbox('options:is_tags_in_item', array(
                        'title' => LANG_CP_TAGS_IN_ITEM
                    )),
                )
            ),
            'listview' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_LISTVIEW_OPTIONS,
                'childs' => array(
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
                        'title' => LANG_CP_LISTVIEW_FILTER_EXPAND
                    )),
                    new fieldList('options:list_style', array(
                        'title' => LANG_CP_LISTVIEW_STYLE,
                        'hint' => sprintf(LANG_CP_LISTVIEW_STYLE_HINT, $template->getName()),
                        'generator' => function() use($template){
                            return $template->getAvailableContentListStyles();
                        }
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
                    ))
                )
            ),
            'itemview' => array(
                'type' => 'fieldset',
                'is_collapsed' => true,
                'title' => LANG_CP_ITEMVIEW_OPTIONS,
                'childs' => array(
                    new fieldCheckbox('options:item_on', array(
                        'title' => LANG_CP_ITEMVIEW_ON,
                        'default' => true
                    )),
                    new fieldCheckbox('options:is_show_fields_group', array(
                        'title' => LANG_CP_ITEMVIEW_FIELDS_GROUP,
                    )),
                    new fieldCheckbox('options:hits_on', array(
                        'title' => LANG_CP_ITEMVIEW_HITS_ON,
                    )),
                    new fieldText('item_append_html', array(
                        'title' => LANG_CP_ITEMVIEW_APPEND_HTML,
                        'hint' => LANG_CP_ITEMVIEW_APPEND_HTML_HINT,
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
                        'rules' => array(
                            array('required'),
                        )
                    )),
                    new fieldString('options:seo_title_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint'  => LANG_CP_SEOMETA_ITEM_HINT
                    )),
                    new fieldString('options:seo_keys_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'hint'  => LANG_CP_SEOMETA_ITEM_HINT
                    )),
                    new fieldString('options:seo_desc_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint'  => LANG_CP_SEOMETA_ITEM_HINT
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
