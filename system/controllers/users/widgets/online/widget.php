<?php

class widgetUsersOnline extends cmsWidget {

    public function run() {

        $model = cmsCore::getModel('users');

        $groups = $this->getOption('groups', []);
        $groups_hide = $this->getOption('groups_hide', []);

        if ($groups || $groups_hide) {

            $where = 'm.user_id = i.id';

            if($groups){

                $groups_list = implode(',', $groups);

                $where .= " AND m.group_id IN ({$groups_list})";
            }

            if($groups_hide){

                $groups_hide_list = implode(',', $groups_hide);

                $where .= " AND m.group_id NOT IN ({$groups_hide_list})";
            }

            $model->join('{users}_groups_members', 'm', $where);
        }

        $profiles = $model->filterOnlineUsers()->getUsers();
        if (!$profiles) {
            return false;
        }

        $fields = cmsCore::getModel('content')->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        list($fields, $model) = cmsEventsManager::hook('profiles_list_filter', array($fields, $model));

        return [
            'profiles'   => $profiles,
            'fields'     => $fields,
            'is_avatars' => $this->getOption('is_avatars')
        ];
    }

}
