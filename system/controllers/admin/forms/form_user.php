<?php
class formAdminUser extends cmsForm {

    public function init($do) {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_USER,
                'childs' => array(

                    new fieldString('email', array(
                        'title' => LANG_EMAIL,
                        'rules' => array(
                            array('required'),
                            array('email'),
                            $do=='add' ? array('unique', '{users}', 'email') : false
                        )
                    )),

                    new fieldString('nickname', array(
                        'title' => LANG_NICKNAME,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('password1', array(
                        'title' => LANG_NEW_PASS,
                        'is_password' => true,
                        'rules' => array(
                            $do=='add' ? array('required') : false,
                        )
                    )),

                    new fieldString('password2', array(
                        'title' => LANG_RETYPE_NEW_PASS,
                        'is_password' => true,
                        'rules' => array(
                            $do=='add' ? array('required') : false,
                        )
                    )),

                )
            ),

            'permissions' => array(
                'type' => 'fieldset',
                'title' => LANG_PERMISSIONS,
                'childs' => array(
                    new fieldCheckbox('is_admin', array(
                        'title' => LANG_USER_IS_ADMIN,
                        'default' => false
                    )),
                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_USER_GROUP,
                'childs' => array(

                    new fieldListGroups('groups', array(
                        'show_all' => false,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_USER_LOCKING,
                'childs' => array(

                    new fieldCheckbox('is_locked', array(
                        'title' => LANG_CP_USER_IS_LOCKED
                    )),

                    new fieldDate('lock_until', array(
                        'title' => LANG_CP_USER_LOCK_UNTIL
                    )),

                    new fieldString('lock_reason', array(
                        'title' => LANG_CP_USER_LOCK_REASON,
                        'rules' => array(
                            array('max_length', 250)
                        )
                    )),

                )
            ),

        );

    }


}
