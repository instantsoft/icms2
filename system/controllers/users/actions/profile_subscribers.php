<?php
/**
 * @property \modelContent $model_content
 */
class actionUsersProfileSubscribers extends cmsAction {

    use icms\traits\services\fieldsParseable;

    public $lock_explicit_call = true;

    public function run($profile) {

        $tabs = $this->getProfileMenu($profile);

        if (!isset($this->tabs['subscribers'])) {
            return cmsCore::error404();
        }

        $this->model->joinInner('{users}_friends', 'fr', 'fr.user_id = i.id');

        $this->model->filterEqual('fr.friend_id', $profile['id'])->
                filterIsNull('fr.is_mutual')->
                disableDeleteFilter();

        $page_url = href_to_profile($profile, 'subscribers');

        $profiles_list_html = $this->renderProfilesList($page_url, false, [
            [
                'title'   => LANG_USERS_FRIENDS_ADD,
                'class'   => 'ajax-modal',
                'href'    => href_to('users', 'friend_add', '{id}'),
                'handler' => function ($user) {
                    return $this->is_own_profile &&
                    $this->options['is_friends_on'] &&
                    empty($user['is_locked']) &&
                    $this->cms_user->isPrivacyAllowed($user, 'users_friendship', true);
                }
            ],
            [
                'title'   => LANG_USERS_SUBSCRIBE,
                'class'   => 'ajax-modal',
                'href'    => href_to('users', 'subscribe', '{id}'),
                'handler' => function ($user) {
                    return $this->is_own_profile && empty($user['is_locked']) && !$this->cms_user->isSubscribe($user['id']) &&
                    (!$this->options['is_friends_on'] ||
                    ($this->options['is_friends_on'] && !$this->cms_user->isPrivacyAllowed($user, 'users_friendship', true))
                    );
                }
            ]
        ]);

        $fields = $this->parseContentFields(
            $this->model_content->setTablePrefix('')->getContentFields('{users}'),
            $profile
        );

        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        return $this->cms_template->render('profile_friends', [
            'user'               => $this->cms_user,
            'meta_profile'       => $meta_profile,
            'tabs'               => $tabs,
            'fields'             => $fields,
            'tab'                => $this->tabs['subscribers'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ]);
    }

}
