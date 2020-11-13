<?php

class formGroupsOptions extends cmsForm {

    public function init() {

        return array(

            'list' => array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_LIST,
                'childs' => array(

                    new fieldCheckbox('is_filter', array(
                        'title' => LANG_CP_LISTVIEW_FILTER,
                    ))

                )
            ),

            'view' => array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_VIEW,
                'childs' => array(

                    new fieldString('change_owner_email', array(
                        'title' => LANG_GROUPS_OPT_CHANGE_OWNER_EMAIL,
                        'rules' => array(
                            array('email')
                        )
                    ))

                )
            ),

            'limit' => array(
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
