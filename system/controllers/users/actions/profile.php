<?php

class actionUsersProfile extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();

        $profile = cmsEventsManager::hook('users_profile_view', $profile);

        // Отношения
        $is_own_profile = $user->id == $profile['id'];
        $is_friends_on = $this->options['is_friends_on'];
        $is_friend_profile = $user->isFriend($profile['id']);
        $is_friend_req = $is_friends_on ? $this->model->isFriendshipRequested($user->id, $profile['id']) : false;

        // Доступность профиля для данного пользователя
        if ( !$user->isPrivacyAllowed($profile, 'users_profile_view') ){
            return cmsTemplate::getInstance()->render('profile_closed', array(
                'profile' => $profile,
                'user' => $user,
                'is_own_profile' => $is_own_profile,
                'is_friends_on' => $is_friends_on,
                'is_friend_profile' => $is_friend_profile,
                'is_friend_req' => $is_friend_req,
            ));
        }

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('users');

        // Друзья
        $friends = $is_friends_on ? $this->model->getFriends($profile['id']) : false;

        // Контент	
		$content_model = cmsCore::getModel('content');
		
		$is_filter_hidden = (!$is_own_profile && !$user->is_admin);
		
        $content_counts = $content_model->getUserContentCounts($profile['id'], $is_filter_hidden);
		
        //
        // Стена
        //
        if ($this->options['is_wall']){

            $wall_controller = cmsCore::getController('wall', $this->request);

            $wall_title = LANG_USERS_PROFILE_WALL;

            $wall_target = array(
                'controller' => 'users',
                'profile_type' => 'user',
                'profile_id' => $profile['id']
            );

            $wall_permissions = array(
                'add' => $user->is_logged && $user->isPrivacyAllowed($profile, 'users_profile_wall'),
                'delete' => ($user->is_admin || ($user->id == $profile['id'])),
            );

            $wall_html = $wall_controller->getWidget($wall_title, $wall_target, $wall_permissions);

        }

        return cmsTemplate::getInstance()->render('profile_view', array(
            'profile' => $profile,
            'user' => $user,
            'is_own_profile' => $is_own_profile,
            'is_friends_on' => $is_friends_on,
            'is_friend_profile' => $is_friend_profile,
            'is_friend_req' => $is_friend_req,
            'friends' => $friends,
            'content_counts' => $content_counts,
            'fields' => $fields,
            'wall_html' => isset($wall_html) ? $wall_html : false,
            'tabs' => $this->getProfileMenu($profile)
        ));

    }

}
