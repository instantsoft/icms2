<?php

class formGroupsChangeOwner extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_OWNER_NEW_EMAIL,
                'childs' => array(
                    new fieldString('email', array(
                        'hint'  => LANG_GROUPS_OWNER_NEW_EMAIL_HINT,
                        'rules' => array(
                            array('required'),
                            array('email')
                        )
                    ))
                )
            )

        );

    }

}
