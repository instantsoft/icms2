<?php

class formWidgetActivityListOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('activity');

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:dataset', [
                        'title' => LANG_WD_ACTIVITY_LIST_DATASET,
                        'items' => array(
                            'all'     => LANG_ACTIVITY_DS_ALL,
                            'friends' => LANG_ACTIVITY_DS_FRIENDS,
                            'my'      => LANG_ACTIVITY_DS_MY,
                        )
                    ]),
                    new fieldCheckbox('options:show_avatars', [
                        'title' => LANG_WD_ACTIVITY_LIST_SHOW_AVATARS
                    ]),
                    new fieldCheckbox('options:date_group', [
                        'title' => LANG_WD_ACTIVITY_LIST_DATE_GROUP
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
