<?php

class onMessagesUserPrivacyTypes extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return false;
        }

        return [
            'messages_pm' => [
                'title'   => LANG_PM_PRIVACY_CONTACT,
                'options' => ['anyone', 'friends']
            ]
        ];
    }

}
