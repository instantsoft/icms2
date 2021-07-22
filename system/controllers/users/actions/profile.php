<?php

class actionUsersProfile extends cmsAction {

    public $lock_explicit_call = true;

    private $is_friend_req = false;

    public function run($profile = null){

        $profile = cmsEventsManager::hook('users_profile_view', $profile);

        // отправлен запрос дружбы
        $this->is_friend_req = $this->options['is_friends_on'] ? $this->model->isFriendshipRequested($this->cms_user->id, $profile['id']) : false;

        $content = cmsCore::getController('content', $this->request);

        // Получаем поля
        $fields = $content->model->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');
        // Системные поля (ячейки в таблице, дата регистрации и т.п.)
        $sys_fields = $this->getSystemFields($profile);

        // Парсим значения полей
        foreach($fields as $name => $field){
            $fields[$name]['html'] = $field['handler']->setItem($profile)->parse($profile[$name]);
            $fields[$name]['string_value'] = $field['handler']->getStringValue($profile[$name]);
        }

        // Формируем метатеги
        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        // Доступность профиля для данного пользователя
        if ( !$this->cms_user->isPrivacyAllowed($profile, 'users_profile_view') ){
            return $this->cms_template->render('profile_closed', array(
                'profile'        => $profile,
                'meta_profile'   => $meta_profile,
                'sys_fields'     => $sys_fields,
                'fields'         => $fields,
                'user'           => $this->cms_user,
                'is_own_profile' => $this->is_own_profile,
                'is_friends_on'  => $this->options['is_friends_on'],
                'tool_buttons'   => $this->getToolButtons($profile)
            ));
        }

        // Друзья
        $friends = [];
        if($this->options['is_friends_on']){
            if(!empty($this->options['profile_max_friends_count'])){
                $this->model->limit($this->options['profile_max_friends_count']);
            }
            $friends = $this->model->getFriends($profile['id']);
        }

        // Контент
		$content->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);

		$is_filter_hidden = (!$this->is_own_profile && !$this->cms_user->is_admin);

        $content_counts = $content->model->getUserContentCounts($profile['id'], $is_filter_hidden, function($ctype) use ($profile, $content){
            if(!cmsUser::isAllowed($ctype['name'], 'add') &&
                    !cmsUser::getInstance()->isPrivacyAllowed($profile, 'view_user_'.$ctype['name'])){
                return false;
            }
            return cmsUser::get('id') == $profile['id'] ? true : $content->checkListPerm($ctype['name']);
        });

        list($profile, $fields) = cmsEventsManager::hook('profile_before_view', array($profile, $fields));

        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($profile){

            if ($field['is_system'] || !$field['is_in_item'] || empty($profile[$field['name']])) { return false; }

            // проверяем что группа пользователя имеет доступ к чтению этого поля
            if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) {
                // если группа пользователя не имеет доступ к чтению этого поля,
                // проверяем на доступ к нему для авторов
                if (!empty($profile['id']) && !empty($field['options']['author_access'])){
                    if (!in_array('is_read', $field['options']['author_access'])){ return false; }
                    if ($profile['id'] == $user->id){ return true; }
                }
                return false;
            }
            return true;

        }, $profile);

        return $this->cms_template->render('profile_view', array(
            'options'        => $this->options,
            'meta_profile'   => $meta_profile,
            'profile'        => $profile,
            'user'           => $this->cms_user,
            'is_own_profile' => $this->is_own_profile,
            'is_friends_on'  => $this->options['is_friends_on'],
            'tool_buttons'   => $this->getToolButtons($profile),
            'friends'        => $friends,
            'content_counts' => $content_counts,
            'sys_fields'     => $sys_fields,
            'fields'         => $fields,
            'fieldsets'      => $fieldsets,
            'wall_html'      => false, // Не используется, чтобы нотиса в старых шаблонах не было
            'tabs'           => $this->getProfileMenu($profile),
            'show_all_flink' => isset($this->tabs['friends'])
        ));

    }

    private function getSystemFields($profile) {

        $fields = ['date_reg' =>
            [
                'title' => LANG_USERS_PROFILE_REGDATE,
                'text'  => string_date_age_max($profile['date_reg'], true)
            ]
        ];

        if (!empty($this->options['show_user_groups'])){

            $groups = $this->model->getGroups();

            $groups_title = [];

            foreach ($profile['groups'] as $group_id) {
                $groups_title[] = $groups[$group_id]['title'];
            }

            $fields['groups'] = [
                'title' => LANG_GROUPS,
                'text'  => implode(', ', $groups_title)
            ];
        }

        if (!$profile['is_online']){
            $fields['date_log'] = [
                'title' => LANG_USERS_PROFILE_LOGDATE,
                'text'  => string_date_age_max($profile['date_log'], true)
            ];
        }

        if ($profile['inviter_id']) {
            $fields['inviter_id'] = [
                'title' => LANG_USERS_PROFILE_INVITED_BY,
                'href'  => href_to_profile($profile['inviter']),
                'text'  => $profile['inviter_nickname']
            ];
        }

        if($this->cms_user->is_admin && $profile['ip']){
            $fields['ip'] = [
                'title' => LANG_USERS_PROFILE_LAST_IP,
                'href'  => href_to('users').'?ip='.$profile['ip'],
                'text'  => $profile['ip']
            ];
        }

        $hook = cmsEventsManager::hook('user_profile_sys_fields', array(
            'profile' => $profile,
            'fields'  => $fields
        ));

        return $hook['fields'];
    }

    private function getToolButtons($profile) {

        $tool_buttons = array();

        if ($this->cms_user->is_logged && !$profile['is_deleted']) {

            $allowed_user_friendship = $this->cms_user->isPrivacyAllowed($profile, 'users_friendship', true);

            if (!$this->is_own_profile &&
                    !$profile['is_locked'] &&
                    (!$this->options['is_friends_on'] ||
                        ($this->options['is_friends_on'] && !$allowed_user_friendship))
                    ){

                if(!$this->is_subscribe_profile){
                    $tool_buttons['subscribe'] = array(
                        'title' => LANG_USERS_SUBSCRIBE,
                        'class' => 'subscribe ajax-modal', 'icon' => 'bell',
                        'href'  => href_to('users', 'subscribe', $profile['id'])
                    );
                } else {
                    $tool_buttons['unsubscribe'] = array(
                        'title' => LANG_USERS_UNSUBSCRIBE,
                        'class' => 'unsubscribe ajax-modal', 'icon' => 'bell-slash',
                        'href'  => href_to('users', 'unsubscribe', $profile['id'])
                    );
                }

            }

            if ($this->options['is_friends_on'] && !$this->is_own_profile && !$profile['is_locked']){

                if ($allowed_user_friendship){

                    if ($this->is_friend_profile){
                        $tool_buttons['friend_delete'] = array(
                            'title' => LANG_USERS_FRIENDS_DELETE,
                            'class' => 'user_delete ajax-modal', 'icon' => 'user-minus',
                            'href'  => href_to('users', 'friend_delete', $profile['id'])
                        );
                    } else if(!$this->is_friend_req) {
                        $tool_buttons['friend_add'] = array(
                            'title' => LANG_USERS_FRIENDS_ADD,
                            'class' => 'user_add ajax-modal', 'icon' => 'user-plus',
                            'href'  => href_to('users', 'friend_add', $profile['id'])
                        );
                    }

                }

            }

            if ($this->is_own_profile && $profile['invites_count']){
                $tool_buttons['invites'] = array(
                    'title'   => LANG_USERS_MY_INVITES,
                    'class'   => 'invites', 'icon' => 'user-tag',
                    'counter' => $profile['invites_count'],
                    'href'    => href_to_profile($profile, ['invites'])
                );
            }

            if ($this->is_own_profile || $this->cms_user->is_admin){
                $tool_buttons['settings'] = array(
                    'title' => LANG_USERS_EDIT_PROFILE,
                    'class' => 'settings', 'icon' => 'edit',
                    'href'  => href_to_profile($profile, ['edit'])
                );
            }

            if ($this->cms_user->is_admin){
                $tool_buttons['edit'] = array(
                    'title' => LANG_USERS_EDIT_USER,
                    'class' => 'edit', 'icon' => 'user-edit',
                    'href'  => href_to('admin', 'users', array('edit', $profile['id'])) . "?back=" . href_to('users', $profile['id'])
                );
            }

            if (cmsUser::isAllowed('users', 'delete', 'any', true) && !$this->is_own_profile){
                $tool_buttons['delete'] = array(
                    'title' => LANG_USERS_DELETE_PROFILE,
                    'class' => 'user_delete ajax-modal', 'icon' => 'minus-circle',
                    'href'  => href_to_profile($profile, ['delete'])
                );
            }

        }

        if ($profile['is_deleted'] && cmsUser::isAllowed('users', 'delete', 'any')) {
                $tool_buttons['restore'] = array(
                    'title' => LANG_USERS_RESTORE_PROFILE,
                    'class' => 'basket_remove ajax-modal', 'icon' => 'trash-restore',
                    'href'  => href_to_profile($profile, ['restore'])
                );
        }

        $buttons_hook = cmsEventsManager::hook('user_profile_buttons', array(
            'profile' => $profile,
            'buttons' => $tool_buttons
        ));

        return $buttons_hook['buttons'];

    }

}
