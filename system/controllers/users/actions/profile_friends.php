<?php

class actionUsersProfileFriends extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        $tabs = $this->controller->getProfileMenu($profile);

        if (!isset($this->tabs['friends'])){
            cmsCore::error404();
        }

        // Проверяем наличие друзей
        if (!$this->model->getFriendsCount($profile['id'])) { cmsCore::error404(); }

        $this->model->filterFriends($profile['id']);

        $page_url = href_to($this->name, $profile['id'], 'friends');
        $profiles_list_html = $this->renderProfilesList($page_url);

        return $this->cms_template->render('profile_friends', array(
            'user'               => $this->cms_user,
            'tabs'               => $tabs,
            'tab'                => $this->tabs['friends'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
