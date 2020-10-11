<?php
class widgetUsersList extends cmsWidget {

    public function run(){

        $show        = $this->getOption('show', 'all');
        $dataset     = $this->getOption('dataset', 'latest');
        $groups      = $this->getOption('groups');
        $list_fields = $this->getOption('list_fields');

        $user = cmsUser::getInstance();
        $model = cmsCore::getModel('users');
        $content_model = cmsCore::getModel('content');

        $show_fields = [];

        $content_model->setTablePrefix('')->orderBy('ordering');
        $fields = $content_model->getContentFields('{users}');

        list($fields, $model) = cmsEventsManager::hook('profiles_list_filter', array($fields, $model));

        if($list_fields){
            foreach ($fields as $name => $field) {
                if(in_array($field['id'], $list_fields)){
                    $show_fields[$name] = $field;
                }
            }
        }

        switch ($show){

            case 'friends':
                if (!$user->is_logged) { return false; }
                $model->filterFriends($user->id);
                break;

            case 'friends_online':
                if (!$user->is_logged) { return false; }
                $model->filterFriends($user->id);
                $model->filterOnlineUsers();
                break;

        }

        switch ($dataset){
            case 'latest': $model->orderBy('date_reg', 'desc');
                break;
            case 'rating': $model->orderBy('karma desc, rating desc');
                break;
            case 'popular': $model->orderBy('friends_count', 'desc');
                break;
            case 'subscribers': $model->orderBy('subscribers_count', 'desc');
                break;
            case 'date_log': $model->orderBy('date_log', 'desc');
                break;
        }

        if ($groups){
            $model->filterGroups($groups);
        }

        $profiles = $model->limit($this->getOption('limit', 10))->getUsers();
        if (!$profiles) { return false; }

        $model->makeProfileFields($show_fields, $profiles, $user);

        list($profiles, $show_fields) = cmsEventsManager::hook('profiles_before_list', [$profiles, $show_fields]);

        return array(
            'profiles'   => $profiles,
            'fields'     => $fields,
            'style'      => $this->getOption('style', 'list'),
            'is_avatars' => $this->getOption('is_avatars')
        );

    }

}
