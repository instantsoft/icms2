<?php
class widgetUsersOnline extends cmsWidget {

    public function run(){

        $profiles = cmsCore::getModel('users')->filterOnlineUsers()->getUsers();
        if (!$profiles) { return false; }

        return array(
            'profiles' => $profiles,
            'is_avatars' => $this->getOption('is_avatars')
        );

    }

}
