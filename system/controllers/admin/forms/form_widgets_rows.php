<?php
class formAdminWidgetsRows extends cmsForm {

    public function init($do) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_BASIC,
                'childs' => array(
                    new fieldHidden('template'),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 255)
                        )
                    )),
                    new fieldNumber('cols_count', array(
                        'title' => LANG_CP_WIDGETS_COL_COUNT,
                        'default' => 2,
                        'options' => [
                            'is_abs' => true,
                            'is_ceil' => true
                        ],
                        'rules' => [
                            array('min', 1),
                            array('max', 12),
                        ]
                    )),
                    new fieldList('nested_position', array(
                        'title' => LANG_CP_WIDGETS_ROW_NESTED_POSITION,
                        'items' => array(
                            'before'  => LANG_CP_WIDGETS_ROW_NESTED_POSITION1,
                            'after' => LANG_CP_WIDGETS_ROW_NESTED_POSITION2
                        )
                    )),
                    new fieldString('class', array(
                        'title' => LANG_CP_WIDGETS_ROW_CLASS,
                        'rules' => array(
                            array('max_length', 100)
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
                    ))
                )
            )
        );

    }

}
