<?php

class actionUsersProfileFriends extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        // Проверяем наличие друзей
        if (!$this->model->getFriendsCount($profile['id'])) { cmsCore::error404(); }

        $this->model->filterFriends($profile['id']);

        $page_url = href_to($this->name, $profile['id'], 'friends');
        $profiles_list_html = $this->renderProfilesList($page_url);

        return $template->render('profile_friends', array(
            'user' => $user,
            'profile' => $profile,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
