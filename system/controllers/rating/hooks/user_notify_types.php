<?php

class onRatingUserNotifyTypes extends cmsAction {

    public function run(){

        return array(
            'rating_user_vote' => array(
                'title'   => LANG_RATING_NOTIFY_VOTE,
                'options' => array('', 'pm'),
                'default' => ''
            )
        );

    }

}
