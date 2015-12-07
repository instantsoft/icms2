<?php

class formCommentsOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_guests', array(
                        'title' => LANG_COMMENTS_OPT_IS_GUESTS,
                        'hint' => LANG_COMMENTS_OPT_IS_GUESTS_HINT,
                    )),

                    new fieldNumber('guest_ip_delay', array(
                        'title' => LANG_COMMENTS_OPT_GUESTS_DELAY,
                        'units' => LANG_MINUTE10,
                    )),

                    new fieldText('restricted_ips', array(
                        'title' => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_IPS,
                        'hint' => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_IPS_HINT,
                    )),

					new fieldCheckbox('update_user_rating', array(
                        'title' => LANG_COMMENTS_UPDATE_USER_RATING,
                        'hint' => LANG_COMMENTS_UPDATE_USER_RATING_HINT,
                    ))

                )
            ),

        );

    }

}
