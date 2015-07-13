<?php

class formMessagesOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldNumber('limit', array(
                        'title' => LANG_PM_LIMIT,
                        'default' => 5
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_PERMISSIONS,
                'childs' => array(

                    new fieldListGroups('groups_allowed', array(
                        'show_all' => true,
                        'default' => array(0)
                    ))

                )
            ),

        );

    }

}
