<?php

class onWallUserNotifyTypes extends cmsAction {

    public function run() {

        $users_options = cmsController::loadOptions('users');

        if (empty($users_options['is_wall'])) {
            return false;
        }

        return [
            'users_wall_write' => [
                'title'   => LANG_WALL_NOTIFY_NEW,
                'options' => ['', 'email']
            ]
        ];
    }

}
