<?php
class widgetUsersOnline extends cmsWidget {

    public function run(){

        $is_avatars = $this->getOption('is_avatars');

        $model = cmsCore::getModel('users');

        $profiles = $model->
                        filterEqual('is_online', 1)->
                        getUsers();

        if (!$profiles) { return false; }

        return array(
            'profiles' => $profiles,
            'is_avatars' => $is_avatars,
        );

    }

}
