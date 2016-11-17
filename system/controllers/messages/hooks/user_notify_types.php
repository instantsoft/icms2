<?php

class onMessagesUserNotifyTypes extends cmsAction {

    public function run() {

        return array(
            'messages_new' => array(
                'title' => LANG_PM_NOTIFY_NEW,
                'options' => array('none', 'email')
            )
        );

    }

}