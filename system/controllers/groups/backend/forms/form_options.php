<?php

class formGroupsOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_LIST,
                'childs' => array(

                    new fieldCheckbox('is_filter', array(
                        'title' => LANG_CP_LISTVIEW_FILTER,
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_VIEW,
                'childs' => array(

                    new fieldCheckbox('is_wall', array(
                        'title' => LANG_GROUPS_OPT_WALL_ENABLED,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_LIST_LIMIT,
                'childs' => array(

                    new fieldNumber('limit', array(
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            )

        );

    }

}
