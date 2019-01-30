<?php
class widgetUsersOnline extends cmsWidget {

    public function run(){

        $model = cmsCore::getModel('users');

        $groups = $this->getOption('groups');

        if ($groups) {
            $model->filterGroups($groups);
        }

        $profiles = $model->filterOnlineUsers()->getUsers();
        if (!$profiles) { return false; }

        return array(
            'profiles' => $profiles,
            'is_avatars' => $this->getOption('is_avatars')
        );

    }

}
