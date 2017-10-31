<?php
class formMessagesPmailing extends cmsForm {

    public function init() {

        return array(

            array(
                'type'   => 'fieldset',
                'title'  => LANG_PM_PMAILING_GROUPS,
                'childs' => array(
                    new fieldListGroups('groups', array(
                        'show_all' => true,
                        'rules' => array(
                            array('required')
                        )
                    ))
                )
            ),

            array(
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldHtml('message_text', array(
                        'title' => LANG_MESSAGE,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldList('type', array(
                        'title' => LANG_PM_PMAILING_TYPE,
                        'items' => array(
                            'notify'  => LANG_PM_PMAILING_TYPE_NOTIFY,
                            'message' => LANG_PM_PMAILING_TYPE_MESSAGE
                        )
                    )),
                    new fieldString('sender_user_email', array(
                        'title' => LANG_PM_SENDER_USER_ID,
                        'hint'  => LANG_PM_SENDER_USER_ID_HINT,
                        'autocomplete' => array('url' => href_to('admin', 'users', 'autocomplete')),
                        'rules' => array(
                            array('email')
                        )
                    ))
                )
            )

        );

    }

}
