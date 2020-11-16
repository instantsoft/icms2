<?php

class actionUsersProfileFriends extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        $tabs = $this->controller->getProfileMenu($profile);

        if (!isset($this->tabs['friends'])){
            cmsCore::error404();
        }

        $this->model->filterFriends($profile['id'])->disableDeleteFilter();

        $page_url = href_to_profile($profile, 'friends');

        $profiles_list_html = $this->renderProfilesList($page_url, false, array(
            array(
                'title' => LANG_USERS_FRIENDS_DELETE,
                'class' => 'ajax-modal',
                'href'  => href_to('users', 'friend_delete', '{id}').'?back='.($profile['friends_count'] > 1 ? href_to_profile($profile, array('friends')) : ''),
                'handler' => function($user){
                    return $this->is_own_profile;
                }
            ),
            array(
                'title' => LANG_USERS_KEEP_IN_SUBSCRIBERS,
                'class' => 'ajax-modal',
                'href'  => href_to('users', 'keep_in_subscribers', '{id}').'?back='.href_to_profile($profile, array('subscribers')),
                'handler' => function($user){
                    return $this->is_own_profile && empty($user['is_deleted']);
                }
            )
        ));

        // Получаем поля
        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        return $this->cms_template->render('profile_friends', array(
            'user'               => $this->cms_user,
            'meta_profile'       => $meta_profile,
            'tabs'               => $tabs,
            'fields'             => $fields,
            'tab'                => $this->tabs['friends'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
