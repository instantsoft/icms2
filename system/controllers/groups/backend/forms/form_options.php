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

                    new fieldString('change_owner_email', array(
                        'title' => LANG_GROUPS_OPT_CHANGE_OWNER_EMAIL,
                        'rules' => array(
                            array('email')
                        )
                    ))

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
