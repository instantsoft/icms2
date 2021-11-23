<?php

class onWallUserPrivacyTypes extends cmsAction {

    public function run() {

        $users_options = cmsController::loadOptions('users');

        $types = [];

        if ($users_options['is_wall']) {

            $types['users_profile_wall'] = [
                'title'   => LANG_USERS_PRIVACY_PROFILE_WALL,
                'options' => ['', 'anyone', 'friends']
            ];

            $types['users_profile_wall_reply'] = [
                'title'   => LANG_USERS_PRIVACY_PROFILE_WALL_REPLY,
                'options' => ['', 'anyone', 'friends']
            ];
        }

        return $types;
    }

}
