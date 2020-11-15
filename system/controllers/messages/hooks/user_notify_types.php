<?php

class onMessagesUserNotifyTypes extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return false;
        }

        return [
            'messages_new' => [
                'title'   => LANG_PM_NOTIFY_NEW,
                'options' => ['none', 'email']
            ]
        ];
    }

}
