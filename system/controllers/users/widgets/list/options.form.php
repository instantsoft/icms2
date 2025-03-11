<?php

class formWidgetUsersListOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:show', [
                        'title' => LANG_WD_USERS_LIST_SHOW,
                        'items' => [
                            'all'            => LANG_WD_USERS_LIST_SHOW_ALL,
                            'friends'        => LANG_WD_USERS_LIST_SHOW_FRIENDS,
                            'friends_online' => LANG_WD_USERS_LIST_SHOW_FRIENDS_ONLINE,
                        ]
                    ]),
                    new fieldList('options:dataset', [
                        'title' => LANG_WD_USERS_LIST_DATASET,
                        'items' => [
                            'latest'      => LANG_USERS_DS_LATEST,
                            'subscribers' => LANG_USERS_DS_SUBSCRIBERS,
                            'rating'      => LANG_USERS_DS_RATED,
                            'popular'     => LANG_USERS_DS_POPULAR,
                            'date_log'    => LANG_USERS_DS_DATE_LOG
                        ]
                    ]),
                    new fieldList('options:style', [
                        'title' => LANG_WD_USERS_LIST_STYLE,
                        'items' => [
                            'list'  => LANG_WD_USERS_LIST_STYLE_LIST,
                            'tiles' => LANG_WD_USERS_LIST_STYLE_TILES,
                        ]
                    ]),
                    new fieldList('options:list_fields', [
                        'title'              => LANG_WD_USERS_LIST_LIST_FIELDS,
                        'is_chosen_multiple' => true,
                        'generator'          => function ($item) {

                            $fields = cmsCore::getModel('content')->setTablePrefix('')->getContentFields('{users}');

                            $items = [];

                            if ($fields) {
                                foreach ($fields as $field) {

                                    if (in_array($field['name'], ['nickname', 'avatar']) || $field['is_system']) {
                                        continue;
                                    }

                                    $items[$field['id']] = $field['title'];
                                }
                            }

                            return $items;
                        },
                        'visible_depend' => ['options:style' => ['show' => ['list']]]
                    ]),
                    new fieldListGroups('options:groups', [
                        'title' => LANG_WD_USERS_LIST_GROUPS,
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
            ]
        ];
    }

}
