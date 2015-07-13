<?php

class formWidgetUsersOnlineOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldCheckbox('options:is_avatars', array(
                        'title' => LANG_WD_USERS_ONLINE_AVATARS
                    )),


                    new fieldListGroups('options:groups', array(
                        'title' => LANG_WD_USERS_ONLINE_GROUPS,
                    )),

                )
            ),

        );

    }

}
