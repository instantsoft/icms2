<?php
/**
 * @property \modelUsers $model_users
 * @property \modelContent $model_content
 */
class widgetUsersList extends cmsWidget {

    public function run() {

        $show        = $this->getOption('show', 'all');
        $dataset     = $this->getOption('dataset', 'latest');
        $groups      = $this->getOption('groups');
        $list_fields = $this->getOption('list_fields');

        $show_fields = [];

        $fields = $this->model_content->setTablePrefix('')->getContentFields('{users}');

        list($fields, $this->model_users) = cmsEventsManager::hook('profiles_list_filter', [$fields, $this->model_users]);

        if ($list_fields) {
            foreach ($fields as $name => $field) {
                if (in_array($field['id'], $list_fields)) {
                    $show_fields[$name] = $field;
                }
            }
        }

        switch ($show) {

            case 'friends':
                if (!$this->cms_user->is_logged) {
                    return false;
                }
                $this->model_users->filterFriends($this->cms_user->id);
                break;

            case 'friends_online':
                if (!$this->cms_user->is_logged) {
                    return false;
                }
                $this->model_users->filterFriends($this->cms_user->id);
                $this->model_users->filterOnlineUsers();
                break;
        }

        switch ($dataset) {
            case 'latest': $this->model_users->orderBy('date_reg', 'desc');
                break;
            case 'rating': $this->model_users->orderBy('karma desc, rating desc');
                break;
            case 'popular': $this->model_users->orderBy('friends_count', 'desc');
                break;
            case 'subscribers': $this->model_users->orderBy('subscribers_count', 'desc');
                break;
            case 'date_log': $this->model_users->orderBy('date_log', 'desc');
                break;
        }

        if ($groups) {
            $this->model_users->filterGroups($groups);
        }

        $profiles = $this->model_users->limit($this->getOption('offset', 0), $this->getOption('limit', 10))->getUsers();

        if (!$profiles) {
            return false;
        }

        $this->model_users->makeProfileFields($show_fields, $profiles, $this->cms_user);

        list($profiles, $show_fields) = cmsEventsManager::hook('profiles_before_list', [$profiles, $show_fields]);

        return [
            'profiles'   => $profiles,
            'fields'     => $fields,
            'style'      => $this->getOption('style', 'list'),
            'is_avatars' => $this->getOption('is_avatars')
        ];
    }

}
