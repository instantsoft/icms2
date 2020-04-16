<?php

class onMessagesUserPrivacyTypes extends cmsAction {

    public function run(){

        return array(
            'messages_pm' => array(
                'title' => LANG_PM_PRIVACY_CONTACT,
                'options' => array('anyone', 'friends')
            ),
        );

    }

}
