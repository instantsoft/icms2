<?php

class actionUsersProfileSubscribers extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        $tabs = $this->controller->getProfileMenu($profile);

        if (!isset($this->tabs['subscribers'])){
            cmsCore::error404();
        }

        $this->model->joinInner('{users}_friends', 'fr', 'fr.user_id = i.id');

        $this->model->filterEqual('fr.friend_id', $profile['id'])->
                filterIsNull('fr.is_mutual')->
                disableDeleteFilter();

        $page_url = href_to_profile($profile, 'subscribers');

        $profiles_list_html = $this->renderProfilesList($page_url, false, array(
            array(
                'title' => LANG_USERS_FRIENDS_ADD,
                'class' => 'ajax-modal',
                'href'  => href_to('users', 'friend_add', '{id}'),
                'handler' => function($user){
                    return $this->is_own_profile &&
                            $this->options['is_friends_on'] &&
                            empty($user['is_locked']) &&
                            $this->cms_user->isPrivacyAllowed($user, 'users_friendship', true);
                }
            ),
            array(
                'title' => LANG_USERS_SUBSCRIBE,
                'class' => 'ajax-modal',
                'href'  => href_to('users', 'subscribe', '{id}'),
                'handler' => function($user){
                    return $this->is_own_profile && empty($user['is_locked']) && !$this->cms_user->isSubscribe($user['id']) &&
                            (!$this->options['is_friends_on'] ||
                                ($this->options['is_friends_on'] && !$this->cms_user->isPrivacyAllowed($user, 'users_friendship', true))
                            );
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
            'tab'                => $this->tabs['subscribers'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
