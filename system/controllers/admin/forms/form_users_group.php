<?php
class formAdminUsersGroup extends cmsForm {

    public function init($do) {

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            array('max_length', 32),
                            $do == 'add' ? array('unique', '{users}_groups', 'name') : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 32)
                        )
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldCheckbox('is_public', array(
                        'title' => LANG_CP_USER_GROUP_IS_PUBLIC
                    )),
                    new fieldCheckbox('is_filter', array(
                        'title' => LANG_CP_USER_GROUP_IS_FILTER
                    )),
                )
            ),
        );

    }

}
