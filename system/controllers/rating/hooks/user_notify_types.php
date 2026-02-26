<?php

class onRatingUserNotifyTypes extends cmsAction {

    public function run() {

        return [
            'rating_user_vote' => [
                'title'   => LANG_RATING_NOTIFY_VOTE,
                'options' => ['', 'pm'],
                'default' => ''
            ]
        ];
    }

}
