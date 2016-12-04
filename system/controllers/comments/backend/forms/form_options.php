<?php

class formCommentsOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return array(

            array(
                'type'  => 'fieldset',
                'title' => LANG_BASIC_OPTIONS,
                'childs' => array(

                    new fieldCheckbox('disable_icms_comments', array(
                        'title' => LANG_COMMENTS_DISABLE_ICMS_COMMENTS,
                        'hint' => LANG_COMMENTS_DISABLE_ICMS_COMMENTS_HINT
                    )),

                    new fieldCheckbox('is_guests', array(
                        'title' => LANG_COMMENTS_OPT_IS_GUESTS,
                        'hint' => LANG_COMMENTS_OPT_IS_GUESTS_HINT,
                    )),

                    new fieldCheckbox('is_guests_moderate', array(
                        'title' => LANG_COMMENTS_OPT_IS_GUESTS_MODERATE,
                        'default' => 1
                    )),

                    new fieldNumber('guest_ip_delay', array(
                        'title' => LANG_COMMENTS_OPT_GUESTS_DELAY,
                        'units' => LANG_MINUTE10,
                    )),

                    new fieldText('restricted_ips', array(
                        'title' => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_IPS,
                        'hint' => LANG_COMMENTS_OPT_GUESTS_RESTRICTED_IPS_HINT,
                    )),

                    new fieldCheckbox('dim_negative', array(
                        'title' => LANG_COMMENTS_OPT_DIM_NEGATIVE
                    )),

					new fieldCheckbox('update_user_rating', array(
                        'title' => LANG_COMMENTS_UPDATE_USER_RATING,
                        'hint' => LANG_COMMENTS_UPDATE_USER_RATING_HINT,
                    )),

                    new fieldNumber('limit_nesting', array(
                        'title'   => LANG_COMMENTS_LIMIT_NESTING,
                        'default' => 5,
                        'rules'   => array(array('required'))
                    )),

                    new fieldNumber('limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            )

        );

    }

}
