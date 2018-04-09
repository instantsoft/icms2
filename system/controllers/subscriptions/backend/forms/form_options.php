<?php

class formSubscriptionsOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_BASIC,
                'childs' => array(

                    new fieldCheckbox('need_auth', array(
                        'title'   => LANG_SBSCR_NEED_AUTH,
                        'default' => 0
                    )),

                    new fieldCheckbox('guest_email_confirmation', array(
                        'title'   => LANG_SBSCR_GUEST_EMAIL_CONFIRMATION,
                        'default' => 1,
                        'visible_depend' => array('need_auth' => array('hide' => array('1')))
                    )),

                    new fieldNumber('verify_exp', array(
                        'title'   => LANG_SBSCR_VERIFY_EXP,
                        'units'   => LANG_HOURS,
                        'default' => 24,
                        'visible_depend' => array('need_auth' => array('hide' => array('1')))
                    )),

                    new fieldString('admin_email', array(
                        'title' => LANG_SBSCR_ADMIN_EMAIL
                    )),

                    new fieldNumber('limit', array(
                        'title'   => LANG_SBSCR_LIMIT,
                        'default' => 20
                    ))

                )
            )

        );

    }

}
