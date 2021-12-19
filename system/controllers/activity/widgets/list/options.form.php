<?php

class formWidgetActivityListOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('activity');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:dataset', array(
                        'title' => LANG_WD_ACTIVITY_LIST_DATASET,
                        'items' => array(
                            'all' => LANG_ACTIVITY_DS_ALL,
                            'friends' => LANG_ACTIVITY_DS_FRIENDS,
                            'my' => LANG_ACTIVITY_DS_MY,
                        )
                    )),

                    new fieldCheckbox('options:show_avatars', array(
                        'title' => LANG_WD_ACTIVITY_LIST_SHOW_AVATARS,
                    )),

                    new fieldCheckbox('options:date_group', array(
                        'title' => LANG_WD_ACTIVITY_LIST_DATE_GROUP,
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    )),

                )
            ),

        );

    }

}
