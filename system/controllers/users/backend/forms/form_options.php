<?php

class formUsersOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_USERS_LIST,
                'childs' => array(

                    new fieldCheckbox('is_ds_online', array(
                        'title' => sprintf(LANG_USERS_OPT_DS_SHOW, LANG_USERS_DS_ONLINE),
                    )),
                    new fieldCheckbox('is_ds_rating', array(
                        'title' => sprintf(LANG_USERS_OPT_DS_SHOW, LANG_USERS_DS_RATED),
                    )),
                    new fieldCheckbox('is_ds_popular', array(
                        'title' => sprintf(LANG_USERS_OPT_DS_SHOW, LANG_USERS_DS_POPULAR),
                    )),
                    new fieldCheckbox('is_filter', array(
                        'title' => LANG_USERS_OPT_FILTER_SHOW,
                    )),
                    new fieldNumber('limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldListGroups('list_allowed', array(
                        'title' => LANG_USERS_OPT_LIST_ALLOWED,
                        'show_all' => true,
                        'default'  => array(0)
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_USERS_PROFILE,
                'childs' => array(

                    new fieldCheckbox('is_auth_only', array(
                        'title' => LANG_USERS_OPT_AUTH_ONLY,
                    )),

                    new fieldCheckbox('is_status', array(
                        'title' => LANG_USERS_OPT_STATUSES_ENABLED,
                    )),

                    new fieldCheckbox('is_wall', array(
                        'title' => LANG_USERS_OPT_WALL_ENABLED,
                    )),

                    new fieldCheckbox('is_themes_on', array(
                        'title' => LANG_USERS_OPT_THEME,
                        'hint' => LANG_USERS_OPT_THEME_HINT,
                    )),

                    new fieldNumber('max_tabs', array(
                        'title' => LANG_USERS_OPT_MAX_TABS,
                        'hint' => LANG_USERS_OPT_MAX_TABS_HINT,
                        'default' => 6
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_USERS_SOCIALITY,
                'childs' => array(

                    new fieldCheckbox('is_friends_on', array(
                        'title' => LANG_USERS_OPT_FRIENDSHIP,
                    )),

                    new fieldCheckbox('is_karma_comments', array(
                        'title' => LANG_USERS_OPT_KARMA_COMMENTS,
                    )),

                    new fieldNumber('karma_time', array(
                        'title' => LANG_USERS_OPT_KARMA_TIME,
                        'hint' => LANG_USERS_OPT_KARMA_TIME_HINT,
                        'default' => 30
                    ))

                )
            )

        );

    }

}
