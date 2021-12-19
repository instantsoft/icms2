<?php

class formWidgetUsersListOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('users');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:show', array(
                        'title' => LANG_WD_USERS_LIST_SHOW,
                        'items' => array(
                            'all' => LANG_WD_USERS_LIST_SHOW_ALL,
                            'friends' => LANG_WD_USERS_LIST_SHOW_FRIENDS,
                            'friends_online' => LANG_WD_USERS_LIST_SHOW_FRIENDS_ONLINE,
                        )
                    )),

                    new fieldList('options:dataset', array(
                        'title' => LANG_WD_USERS_LIST_DATASET,
                        'items' => array(
                            'latest'      => LANG_USERS_DS_LATEST,
                            'subscribers' => LANG_USERS_DS_SUBSCRIBERS,
                            'rating'      => LANG_USERS_DS_RATED,
                            'popular'     => LANG_USERS_DS_POPULAR,
                            'date_log'    => LANG_USERS_DS_DATE_LOG
                        )
                    )),

                    new fieldList('options:style', array(
                        'title' => LANG_WD_USERS_LIST_STYLE,
                        'items' => array(
                            'list' => LANG_WD_USERS_LIST_STYLE_LIST,
                            'tiles' => LANG_WD_USERS_LIST_STYLE_TILES,
                        )
                    )),

                    new fieldList('options:list_fields', array(
                        'title' => LANG_WD_USERS_LIST_LIST_FIELDS,
                        'is_chosen_multiple' => true,
                        'generator' => function($item) {

                            $model = cmsCore::getModel('content');
                            $model->setTablePrefix('');
                            $model->orderBy('ordering');
                            $fields = $model->getContentFields('{users}');

                            $items = array();

                            if ($fields) {
                                foreach ($fields as $field) {

                                    if(in_array($field['name'], array('nickname', 'avatar'))){
                                        continue;
                                    }

                                    if ($field['is_system'] || !$field['is_in_list']) { continue; }

                                    $items[$field['id']] = $field['title'];

                                }
                            }

                            return $items;

                        },
                        'visible_depend' => array('options:style' => array('show' => array('list')))
                    )),

                    new fieldListGroups('options:groups', array(
                        'title' => LANG_WD_USERS_LIST_GROUPS,
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ))

                )
            )

        );

    }

}
