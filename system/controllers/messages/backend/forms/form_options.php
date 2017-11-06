<?php

class formMessagesOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldNumber('limit', array(
                        'title'   => LANG_PM_LIMIT,
                        'default' => 5,
                        'rules' => array(
                            array('required'),
                            array('min', 1)
                        )
                    )),

                    new fieldNumber('time_delete_old', array(
                        'title'   => LANG_PM_TIME_DELETE_OLD,
                        'hint'    => LANG_PM_TIME_DELETE_OLD_HINT,
                        'default' => 0,
                        'units'   => LANG_DAY10
                    )),

                    new fieldNumber('refresh_time', array(
                        'title'   => LANG_PM_REFRESH_TIME,
                        'default' => 15,
                        'units'   => LANG_SECOND10,
                        'rules' => array(
                            array('required'),
                            array('min', 1)
                        )
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_PERMISSIONS,
                'childs' => array(

                    new fieldListGroups('groups_allowed', array(
                        'show_all' => true,
                        'default'  => array(0)
                    ))

                )
            )

        );

    }

}
