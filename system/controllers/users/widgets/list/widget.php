<?php
class widgetUsersList extends cmsWidget {

    public function run(){

        $show = $this->getOption('show', 'all');
        $dataset = $this->getOption('dataset', 'latest');
        $groups = $this->getOption('groups');
        $is_avatars = $this->getOption('is_avatars');
        $limit = $this->getOption('limit', 10);
        $style = $this->getOption('style', 'list');

        $user = cmsUser::getInstance();
        $model = cmsCore::getModel('users');

        switch ($show){

            case 'friends':
                if (!$user->is_logged) { return false; }
                $model->filterFriends($user->id);
                break;

            case 'friends_online':
                if (!$user->is_logged) { return false; }
                $model->filterFriends($user->id);
                $model->joinInner('sessions_online', 'online', 'i.id = online.user_id');
                break;

        }

        switch ($dataset){
            case 'latest': $model->orderBy('date_reg', 'desc'); break;
            case 'rating': $model->orderBy('karma desc, rating desc'); break;
            case 'popular': $model->orderBy('friends_count', 'desc'); break;
            case 'date_log': $model->orderBy('date_log', 'desc'); break;
        }

        if ($groups){
            $model->filterGroups($groups);
        }

        $profiles = $model->
                        limit($limit)->
                        getUsers();

        if (!$profiles) { return false; }

        return array(
            'profiles' => $profiles,
            'style' => $style,
            'is_avatars' => $is_avatars,
        );

    }

}
