<?php
class formAdminMailCheck extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldString('email', array(
                        'title' => LANG_MAILCHECK_TO,
                        'rules' => array(
                            array('required'),
                            array('email')
                        )
                    )),

                    new fieldString('subject', array(
                        'title' => LANG_MAILCHECK_SUBJECT,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldText('body', array(
                        'title' => LANG_MAILCHECK_BODY,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            )

        );

    }


}
