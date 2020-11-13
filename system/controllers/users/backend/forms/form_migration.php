<?php

class formUsersMigration extends cmsForm {

    public function init($do) {

        $groups = cmsCore::getModel('users')->getGroups();

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldString('title', array(
                        'title' => LANG_USERS_MIG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 256)
                        )
                    )),

                    new fieldCheckbox('is_active', array(
                        'title' => LANG_USERS_MIG_IS_ACTIVE,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldList('is_keep_group', array(
                        'title' => LANG_USERS_MIG_ACTION,
                        'items' => array(
                            0 => LANG_USERS_MIG_ACTION_CHANGE,
                            1 => LANG_USERS_MIG_ACTION_ADD
                        )
                    )),

                    new fieldList('group_from_id', array(
                        'title' => LANG_USERS_MIG_FROM,
                        'generator' => function() use($groups){
                            return array_collection_to_list($groups, 'id', 'title');
                        }
                    )),

                    new fieldList('group_to_id', array(
                        'title' => LANG_USERS_MIG_TO,
                        'generator' => function() use($groups){
                            return array_collection_to_list($groups, 'id', 'title');
                        }
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_passed', array(
                        'title' => LANG_USERS_MIG_COND_DATE,
                    )),

                    new fieldList('passed_from', array(
                        'title' => LANG_USERS_MIG_PASSED_FROM,
                        'items' => array(
                            0 => LANG_USERS_MIG_PASSED_REG,
                            1 => LANG_USERS_MIG_PASSED_MIG
                        )
                    )),

                    new fieldNumber('passed_days', array(
                        'title' => LANG_USERS_MIG_PASSED,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_rating', array(
                        'title' => LANG_USERS_MIG_COND_RATING,
                    )),

                    new fieldNumber('rating', array(
                        'title' => LANG_USERS_MIG_RATING,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_karma', array(
                        'title' => LANG_USERS_MIG_COND_KARMA,
                    )),

                    new fieldNumber('karma', array(
                        'title' => LANG_USERS_MIG_KARMA,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_notify', array(
                        'title' => LANG_USERS_MIG_NOTIFY,
                    )),

                    new fieldHtml('notify_text', array(
                        'title' => LANG_USERS_MIG_NOTIFY_TEXT
                    ))

                )
            )

        );

    }

}
