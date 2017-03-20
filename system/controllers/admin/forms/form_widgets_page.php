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
