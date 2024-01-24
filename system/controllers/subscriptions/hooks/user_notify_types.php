<?php

class onSubscriptionsUserNotifyTypes extends cmsAction {

    public function run() {

        return [
            'subscriptions' => [
                'title'   => LANG_SBSCR_NOTIFY_NEW,
                'default' => 'both',
                'options' => ['', 'email', 'pm', 'both']
            ]
        ];
    }

}
