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
                        'title' => LANG_CP_TAB_IS_ACTIVE,
                    )),

                )
            ),

        );

    }

}