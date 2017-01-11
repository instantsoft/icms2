<?php

class formAdminCtypesRelation extends cmsForm {

    public function init($do, $ctype_id) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldList('child_ctype_id', array(
                        'title' => LANG_CP_RELATION_CHILD,
                        'generator' => function() use ($ctype_id) {

                            $ctypes = cmsCore::getModel('content')->getContentTypes();

                            $items =  array_collection_to_list($ctypes, 'id', 'title');

                            unset($items[$ctype_id]);

                            return $items;

                        }
                    )),

                    new fieldString('title', array(
                        'title' => LANG_CP_RELATION_TITLE,
                        'rules' => array(
                            array('required')
                        )
                    )),

                )
            ),
            'layout' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_RELATION_LAYOUT,
                'childs' => array(

                    new fieldList('layout', array(
                        'title' => LANG_CP_RELATION_LAYOUT_TYPE,
                        'items' => array(
                            'list' => LANG_CP_RELATION_LAYOUT_LIST,
                            'tab' => LANG_CP_RELATION_LAYOUT_TAB,
                            'hidden' => LANG_CP_RELATION_LAYOUT_HIDDEN,
                        )
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_CP_RELATION_LAYOUT_LIMIT,
                        'hint' => LANG_CP_RELATION_LAYOUT_LIMIT_HINT
                    )),

                    new fieldCheckbox('options:is_hide_empty', array(
                        'title' => LANG_CP_RELATION_LAYOUT_HIDE_EMPTY
                    )),

                    new fieldCheckbox('options:is_hide_title', array(
                        'title' => LANG_CP_RELATION_LAYOUT_HIDE_TITLE
                    )),

                    new fieldCheckbox('options:is_hide_filter', array(
                        'title' => LANG_CP_RELATION_LAYOUT_HIDE_FILTER
                    )),

                )
            ),
            'tab-opts' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_RELATION_TAB_OPTS,
                'childs' => array(

                    new fieldString('seo_title', array(
                        'title' => LANG_CP_RELATION_TAB_SEO_TITLE,
                        'hint' => LANG_CP_RELATION_TAB_SEO_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),

                    new fieldString('seo_keys', array(
                        'title' => LANG_CP_RELATION_TAB_SEO_KEYS,
                        'hint' => LANG_CP_RELATION_TAB_SEO_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),

                    new fieldText('seo_desc', array(
                        'title' => LANG_CP_RELATION_TAB_SEO_DESC,
                        'hint' => LANG_CP_RELATION_TAB_SEO_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    ))

                )
            )
        );

    }

}
