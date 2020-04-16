<?php
class formAdminWidgetsPage extends cmsForm {

    public function init() {

        return array(
            'title' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 64)
                        )
                    )),
                )
            ),
            'urls' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_WIDGET_PAGE_URLS,
                'childs' => array(

                    new fieldText('url_mask', array(
                        'title' => LANG_CP_WIDGET_PAGE_URL_MASK,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldText('url_mask_not', array(
                        'title' => LANG_CP_WIDGET_PAGE_URL_MASK_NOT,
                    )),
                )
            ),
            'fast_add' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_WIDGETS_FA,
                'childs' => array(
                    new fieldList('fast_add_ctype', array(
                        'title' => LANG_CONTENT_TYPE,
                        'is_virtual' => true,
                        'generator' => function($item){
                            foreach(cmsCore::getModel('content')->getContentTypes()?:array() as $ctype) {
                                $items[$ctype['name']] = $ctype['title'];
                            }
                            return $items;
                        }

                    )),
                    new fieldList('fast_add_type', array(
                        'title' => LANG_CP_WIDGETS_FA_TYPE,
                        'is_virtual' => true,
                        'items' => array(
                            'items' => LANG_CP_WIDGETS_FA_ITEMS,
                            'cats' => LANG_CP_WIDGETS_FA_CATS
                        )

                    )),
                    new fieldString('fast_add_item', array(
                        'title' => LANG_CP_WIDGETS_FA_TITLE_OR_URL,
                        'is_virtual' => true,
                        'autocomplete' => array(
                            'url' => href_to('admin', 'widgets', 'page_autocomplete')
                        ),
                        'visible_depend' => array('fast_add_type' => array('show' => array('items')))
                    )),
                    new fieldList('fast_add_cat', array(
                        'title' => LANG_CATEGORY,
                        'is_virtual' => true,
                        'items' => array(),
                        'visible_depend' => array('fast_add_type' => array('show' => array('cats'))),
                        'parent' => array(
                            'list' => 'fast_add_ctype',
                            'url' => href_to('admin', 'widgets', 'page_content_cats')
                        )
                    )),
                    new fieldList('fast_add_into', array(
                        'title' => LANG_CP_WIDGETS_FA_ADD_TO,
                        'is_virtual' => true,
                        'items' => array(
                            '' => LANG_CP_WIDGETS_FA_TO_POS,
                            '_not' => LANG_CP_WIDGETS_FA_TO_NOT
                        )

                    ))
                )
            ),
            'access' => array(
                'type' => 'fieldset',
                'title' => LANG_PERMISSIONS,
                'childs' => array(
                    new fieldListGroups('groups:view', array(
                        'title' => LANG_SHOW_TO_GROUPS,
                        'show_all' => true,
                        'show_guests' => true
                    )),
                    new fieldListGroups('groups:hide', array(
                        'title' => LANG_HIDE_FOR_GROUPS,
                        'show_all' => false,
                        'show_guests' => true
                    )),
                    new fieldListMultiple('countries:view', array(
                        'title'     => LANG_SHOW_TO_COUNTRIES,
                        'default'   => 0,
                        'show_all'  => true,
                        'generator' => function ($page){
                            $model = new cmsModel();
                            return array_collection_to_list(
                                $model->selectOnly('name')->
                                select('id')->
                                orderBy('ordering', 'asc')->
                                get('geo_countries'), 'id', 'name'
                            );
                        }
                    )),
                    new fieldListMultiple('countries:hide', array(
                        'title'     => LANG_HIDE_TO_COUNTRIES,
                        'default'   => 0,
                        'generator' => function ($page){
                            $model = new cmsModel();
                            return array_collection_to_list(
                                $model->selectOnly('name')->
                                select('id')->
                                orderBy('ordering', 'asc')->
                                get('geo_countries'), 'id', 'name'
                            );
                        }
                    )),
                )
            ),
        );

    }

}
