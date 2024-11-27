<?php
/**
 * @property \modelContent $model_content
 */
class actionUsersProfileFriends extends cmsAction {

    use icms\traits\services\fieldsParseable;

    public $lock_explicit_call = true;

    public function run($profile) {

        $tabs = $this->getProfileMenu($profile);

        if (!isset($this->tabs['friends'])) {
            return cmsCore::error404();
        }

        $this->model->filterFriends($profile['id'])->disableDeleteFilter();

        $page_url = href_to_profile($profile, 'friends');

        $profiles_list_html = $this->renderProfilesList($page_url, false, [
            [
                'title'   => LANG_USERS_FRIENDS_DELETE,
                'class'   => 'ajax-modal',
                'href'    => href_to('users', 'friend_delete', '{id}') . '?back=' . ($profile['friends_count'] > 1 ? href_to_profile($profile, ['friends']) : ''),
                'handler' => function ($user) {
                    return $this->is_own_profile;
                }
            ],
            [
                'title'   => LANG_USERS_KEEP_IN_SUBSCRIBERS,
                'class'   => 'ajax-modal',
                'href'    => href_to('users', 'keep_in_subscribers', '{id}') . '?back=' . href_to_profile($profile, ['subscribers']),
                'handler' => function ($user) {
                    return $this->is_own_profile && empty($user['is_deleted']);
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
            'tab'                => $this->tabs['friends'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ]);
    }

}
