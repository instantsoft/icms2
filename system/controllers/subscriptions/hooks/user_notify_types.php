<?php

class onSubscriptionsUserNotifyTypes extends cmsAction {

    public function run(){

        return array(
            'subscriptions' => array(
                'title'   => LANG_SBSCR_NOTIFY_NEW,
                'default' => 'both',
                'options' => array('', 'email', 'pm', 'both')
            )
        );

    }

}
