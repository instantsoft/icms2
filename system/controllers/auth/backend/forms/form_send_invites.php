<?php
class formAuthSendInvites extends cmsForm {

    public function init() {

        return array(

            array(
                'type'   => 'fieldset',
                'title'  => LANG_AUTH_INVITES_STARGET,
                'childs' => array(
                    new fieldListGroups('groups', array(
                        'title' => LANG_AUTH_INVITES_SGROUP,
                        'show_all' => true,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldString('user_email', array(
                        'title' => LANG_AUTH_INVITES_SUSER,
                        'autocomplete' => array('url' => href_to('admin', 'users', 'autocomplete')),
                        'rules' => array(
                            array('email')
                        )
                    )),
                    new fieldNumber('invites_qty', array(
                        'title' => LANG_AUTH_INVITES_QTY,
                        'default' => 3,
                        'rules' => array(
                            array('required'),
                            array('min', 1)
                        )
                    ))
                )
            ),

            array(
                'type'   => 'fieldset',
                'title'  => LANG_AUTH_INVITES_SPARAMS,
                'childs' => array(
                    new fieldNumber('invites_min_karma', array(
                        'title' => LANG_AUTH_INVITES_KARMA,
                        'default' => 0
                    )),
                    new fieldNumber('invites_min_rating', array(
                        'title' => LANG_AUTH_INVITES_RATING,
                        'default' => 0
                    )),
                    new fieldNumber('invites_min_days', array(
                        'title' => LANG_AUTH_INVITES_DATE,
                        'units' => LANG_DAY10
                    ))
                )
            )

        );

    }

}
