<?php
class widgetUsersOnline extends cmsWidget {

    public function run(){

        $is_avatars = $this->getOption('is_avatars');

        $model = cmsCore::getModel('users');

        $profiles = $model->
                        joinInner('sessions_online', 'online', 'i.id = online.user_id')->
                        getUsers();

        if (!$profiles) { return false; }

        return array(
            'profiles' => $profiles,
            'is_avatars' => $is_avatars,
        );

    }

}
