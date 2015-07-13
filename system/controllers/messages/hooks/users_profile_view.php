<?php

class onMessagesUsersProfileView extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();

        if (!$user->is_logged) { return $profile; }

        if ($user->id != $profile['id']) {

            cmsTemplate::getInstance()->addToolButton(array(
                'title' => LANG_PM_SEND_TO_USER,
                'class' => 'messages ajax-modal',
                'href' => href_to($this->name, 'write', $profile['id'])
            ));

        }

        return $profile;

    }

}
