<?php

class onUsersUserPrivacyTypes extends cmsAction {

    public function run() {

        $types = [];

        $types['users_profile_view'] = [
            'title'   => LANG_USERS_PRIVACY_PROFILE_VIEW,
            'options' => ['', 'anyone', 'friends']
        ];

        if (!empty($this->options['is_friends_on'])) {

            $types['users_friendship'] = [
                'title'   => LANG_USERS_PRIVACY_FRIENDSHIP,
                'options' => ['', 'anyone']
            ];
        }

        if(!empty($this->options['show_reg_data'])) {

            $types['users_show_reg_data'] = [
                'title'   => LANG_USERS_PRIVACY_SHOW_REG_DATA,
                'options' => ['', 'anyone', 'friends']
            ];
        }

        if(!empty($this->options['show_last_visit'])) {

            $types['users_show_last_visit'] = [
                'title'   => LANG_USERS_PRIVACY_SHOW_LAST_VISIT,
                'options' => ['', 'anyone', 'friends']
            ];
        }

        if(!empty($this->options['show_user_groups'])) {

            $types['users_show_user_groups'] = [
                'title'   => LANG_USERS_PRIVACY_SHOW_USER_GROUPS,
                'options' => ['', 'anyone', 'friends']
            ];
        }

        return $types;
    }

}
