<?php

class widgetUsersOnline extends cmsWidget {

    public function run() {

        $model = cmsCore::getModel('users');

        $groups = $this->getOption('groups');

        if ($groups) {
            $model->filterGroups($groups);
        }

        $profiles = $model->filterOnlineUsers()->getUsers();
        if (!$profiles) { return false; }

        $fields = cmsCore::getModel('content')->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        list($fields, $model) = cmsEventsManager::hook('profiles_list_filter', array($fields, $model));

        return array(
            'profiles'   => $profiles,
            'fields'     => $fields,
            'is_avatars' => $this->getOption('is_avatars')
        );

    }

}
