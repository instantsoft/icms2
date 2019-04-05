<?php

class actionUsersProfile extends cmsAction {

    public $lock_explicit_call = true;

    private $is_friend_req = false;

    public function run($profile = null){

        $profile = cmsEventsManager::hook('users_profile_view', $profile);

        // отправлен запрос дружбы
        $this->is_friend_req = $this->options['is_friends_on'] ? $this->model->isFriendshipRequested($this->cms_user->id, $profile['id']) : false;

        // Доступность профиля для данного пользователя
        if ( !$this->cms_user->isPrivacyAllowed($profile, 'users_profile_view') ){
            return $this->cms_template->render('profile_closed', array(
                'profile'        => $profile,
                'user'           => $this->cms_user,
                'is_own_profile' => $this->is_own_profile,
                'is_friends_on'  => $this->options['is_friends_on'],
                'tool_buttons'   => $this->getToolButtons($profile)
            ));
        }

        $content = cmsCore::getController('content', $this->request);

        // Получаем поля
        $fields = $content->model->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        // Парсим значения полей
        foreach($fields as $name => $field){
            $fields[$name]['html'] = $field['handler']->setItem($profile)->parse($profile[$name]);
            $fields[$name]['string_value'] = $field['handler']->getStringValue($profile[$name]);
        }

        // Друзья
        $friends = $this->options['is_friends_on'] ? $this->model->getFriends($profile['id']) : false;

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

        //
        // Стена
        //
        if ($this->options['is_wall']){

            $wall_target = array(
                'controller'   => 'users',
                'profile_type' => 'user',
                'profile_id'   => $profile['id']
            );

            $wall_permissions = $this->runHook('wall_permissions', array(
                'profile_type' => 'user',
                'profile_id'   => $profile
            ));

            $wall_html = $this->controller_wall->getWidget(LANG_USERS_PROFILE_WALL, $wall_target, $wall_permissions);

        }

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
            'profile'        => $profile,
            'user'           => $this->cms_user,
            'is_own_profile' => $this->is_own_profile,
            'is_friends_on'  => $this->options['is_friends_on'],
            'tool_buttons'   => $this->getToolButtons($profile),
            'show_all_flink' => isset($this->tabs['friends']),
            'friends'        => $friends,
            'content_counts' => $content_counts,
            'fields'         => $fields,
            'fieldsets'      => $fieldsets,
            'wall_html'      => isset($wall_html) ? $wall_html : false,
            'tabs'           => $this->getProfileMenu($profile)
        ));

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
                        'class' => 'subscribe ajax-modal',
                        'href'  => href_to('users', 'subscribe', $profile['id'])
                    );
                } else {
                    $tool_buttons['unsubscribe'] = array(
                        'title' => LANG_USERS_UNSUBSCRIBE,
                        'class' => 'unsubscribe ajax-modal',
                        'href'  => href_to('users', 'unsubscribe', $profile['id'])
                    );
                }

            }

            if ($this->options['is_friends_on'] && !$this->is_own_profile && !$profile['is_locked']){

                if ($allowed_user_friendship){

                    if ($this->is_friend_profile){
                        $tool_buttons['friend_delete'] = array(
                            'title' => LANG_USERS_FRIENDS_DELETE,
                            'class' => 'user_delete ajax-modal',
                            'href'  => href_to('users', 'friend_delete', $profile['id'])
                        );
                    } else if(!$this->is_friend_req) {
                        $tool_buttons['friend_add'] = array(
                            'title' => LANG_USERS_FRIENDS_ADD,
                            'class' => 'user_add ajax-modal',
                            'href'  => href_to('users', 'friend_add', $profile['id'])
                        );
                    }

                }

            }

            if ($this->is_own_profile && $profile['invites_count']){
                $tool_buttons['invites'] = array(
                    'title'   => LANG_USERS_MY_INVITES,
                    'class'   => 'invites',
                    'counter' => $profile['invites_count'],
                    'href'    => href_to('users', $profile['id'], 'invites')
                );
            }

            if ($this->is_own_profile || $this->cms_user->is_admin){
                $tool_buttons['settings'] = array(
                    'title' => LANG_USERS_EDIT_PROFILE,
                    'class' => 'settings',
                    'href'  => href_to('users', $profile['id'], 'edit')
                );
            }

            if ($this->cms_user->is_admin){
                $tool_buttons['edit'] = array(
                    'title' => LANG_USERS_EDIT_USER,
                    'class' => 'edit',
                    'href'  => href_to('admin', 'users', array('edit', $profile['id'])) . "?back=" . href_to('users', $profile['id'])
                );
            }

            if (cmsUser::isAllowed('users', 'delete', 'any', true) && !$this->is_own_profile){
                $tool_buttons['delete'] = array(
                    'title' => LANG_USERS_DELETE_PROFILE,
                    'class' => 'user_delete ajax-modal',
                    'href'  => href_to('users', $profile['id'], 'delete')
                );
            }

        }

        if ($profile['is_deleted'] && cmsUser::isAllowed('users', 'delete', 'any')) {
                $tool_buttons['restore'] = array(
                    'title' => LANG_USERS_RESTORE_PROFILE,
                    'class' => 'basket_remove ajax-modal',
                    'href'  => href_to('users', $profile['id'], 'restore')
                );
        }

        $buttons_hook = cmsEventsManager::hook('user_profile_buttons', array(
            'profile' => $profile,
            'buttons' => $tool_buttons
        ));

        return $buttons_hook['buttons'];

    }

}
