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

        $page_url = href_to($this->name, $profile['id'], 'subscribers');

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

        return $this->cms_template->render('profile_friends', array(
            'user'               => $this->cms_user,
            'tabs'               => $tabs,
            'tab'                => $this->tabs['subscribers'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
