<?php

class formUsersTab extends cmsForm {

    public function init($do) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldString('title', array(
                        'title' => LANG_CP_TAB_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),

                    new fieldCheckbox('is_active', array(
                        'title' => LANG_CP_TAB_IS_ACTIVE
                    )),

                    new fieldCheckbox('show_only_owner', array(
                        'title' => LANG_CP_TAB_SHOW_ONLY_OWNER
                    )),

                    new fieldListGroups('groups_view', array(
                        'title' => LANG_SHOW_TO_GROUPS,
                        'show_all' => true,
                        'show_guests' => true
                    )),

                    new fieldListGroups('groups_hide', array(
                        'title' => LANG_HIDE_FOR_GROUPS,
                        'show_all' => false,
                        'show_guests' => true
                    ))

                )
            ),

        );

    }

}