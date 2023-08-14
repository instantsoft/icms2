<?php
/**
 * @property \modelUsers $model
 */
class users extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

    /**
     * Профиль текущего авторизованного пользователя
     * @var boolean
     */
    public $is_own_profile = false;

    /**
     * Просматриваемый профиль в дружбе с текущим пользователем
     * @var boolean
     */
    public $is_friend_profile = false;

    /**
     * Текущий пользователь подписан на просматриваемого
     * @var boolean
     */
    public $is_subscribe_profile = false;

    /**
     * Текущий просматриваемый профиль
     * @var array
     */
    public $profile = [];

    public $tabs = [];
    public $tabs_controllers = [];

    public function routeAction($action_name) {

        if (is_numeric($action_name)) {

            // $action_name это id пользователя
            $profile = $this->model->getUser($action_name, 'join_inviter');
            if (!$profile) {
                return cmsCore::error404();
            }

            if ($profile['slug'] !== $action_name) {
                return $this->redirectTo($this->name, $profile['slug'], $this->current_params, $this->cms_core->uri_query, 301);
            }

        } else {

            if ($this->isActionExists($action_name)) {
                return $action_name;
            }

            $profile = $this->model->getUserBySlug($action_name);
            // Не нашли профиль, значит это набор списка
            if (!$profile) {

                array_unshift($this->current_params, $action_name);

                return 'index';
            }
        }

        // разблокируем вызов экшенов, которым запрещено вызываться напрямую
        $this->lock_explicit_call = false;

        $this->setCurrentProfile($profile);

        if (!$this->cms_user->is_logged && $this->options['is_auth_only']) {
            return cmsUser::goLogin();
        }

        if ($this->options['is_themes_on']) {
            $this->cms_template->applyProfileStyle($this->profile);
        }

        $this->current_params = $this->cms_core->uri_params;

        // кешируем запись для получения ее в виджетах
        cmsModel::cacheResult('current_profile', $this->profile);

        // Нет параметров после названия экшена (/users/id) - значит
        // это главная страница профиля, первым параметром добавляем
        // сам профиль
        if (!$this->cms_core->uri_params) {

            $this->cms_template->addHead('<link rel="canonical" href="' . href_to_profile($profile, false, true) . '"/>');

            array_unshift($this->current_params, $this->profile);

            return 'profile';
        }

        // Ищем экшен внутри профиля
        if ($this->isActionExists('profile_' . $this->cms_core->uri_params[0])) {

            $this->cms_template->addHead('<link rel="canonical" href="' . href_to_profile($profile, [$this->cms_core->uri_params[0], $this->cms_core->uri_params[1] ?? null], true) . '"/>');

            $this->current_params[0] = $this->profile;

            return 'profile_' . $this->cms_core->uri_params[0];
        }

        // Если дошли сюда, значит это неизвестный экшен, возможно вкладка
        // от другого контроллера, тогда первым параметром добавляем
        // сам профиль
        array_unshift($this->current_params, $this->profile);

        return 'profile_tab';
    }

    public function setCurrentProfile($profile) {

        // Статус
        if ($this->options['is_status']) {
            $profile['status'] = $this->model->getUserStatus($profile['status_id']);
        }

        // Репутация
        $profile['is_can_vote_karma'] = $this->cms_user->is_logged &&
                cmsUser::isAllowed('users', 'vote_karma') &&
                ($this->cms_user->id != $profile['id']) &&
                $this->model->isUserCanVoteKarma($this->cms_user->id, $profile['id'], $this->options['karma_time']);

        $this->profile = $profile;

        $this->is_own_profile       = $this->cms_user->id == $profile['id'];
        $this->is_friend_profile    = $this->cms_user->isFriend($profile['id']);
        $this->is_subscribe_profile = $this->cms_user->isSubscribe($profile['id']);

        return $this;
    }

    public function getProfileMenu($profile) {

        $menu = [
            [
                'title'    => LANG_USERS_PROFILE_INDEX,
                'url'      => href_to_profile($profile),
                'url_mask' => href_to_profile($profile)
            ]
        ];

        if ($profile['is_deleted']) {
            return $menu;
        }

        $this->tabs = $this->model->getUsersProfilesTabs(true, 'name');

        $this->tabs_controllers = ['users' => $this];

        if ($this->tabs) {
            foreach ($this->tabs as $tab) {

                // включен ли контроллер
                if (!$this->isControllerEnabled($tab['controller'])) {
                    continue;
                }

                // права доступа
                if (($tab['groups_view'] && !$this->cms_user->isInGroups($tab['groups_view'])) ||
                        ($tab['groups_hide'] && $this->cms_user->isInGroups($tab['groups_hide']))) {
                    continue;
                }

                // опция "показывать только владельцу профиля"
                if ($tab['show_only_owner'] && $profile['id'] != $this->cms_user->id) {
                    continue;
                }

                $default_tab_info = [
                    'title' => $tab['title'],
                    'url'   => href_to_profile($profile, [$tab['name']])
                ];

                if (empty($this->tabs_controllers[$tab['controller']])) {
                    $controller = cmsCore::getController($tab['controller'], $this->request);
                } else {
                    $controller = $this->tabs_controllers[$tab['controller']];
                }

                $tab_info = $controller->runHook('user_tab_info', ['profile' => $profile, 'tab_name' => $tab['name']]);

                if ($tab_info === false) {
                    unset($this->tabs[$tab['name']]);
                    continue;
                } else if ($tab_info === true) {
                    $tab_info = $default_tab_info;
                } else {
                    $tab_info = array_merge($default_tab_info, $tab_info);
                }

                $menu[] = $tab_info;

                $this->tabs_controllers[$tab['controller']] = $controller;
            }
        }

        return $menu;
    }

    public function getProfileEditMenu($profile) {

        $menu = [];

        $menu[] = [
            'title' => LANG_USERS_EDIT_PROFILE_MAIN,
            'url'   => href_to_profile($profile, ['edit'])
        ];

        if ($this->cms_template->hasProfileThemesOptions() && $this->options['is_themes_on']) {

            $menu[] = [
                'title' => LANG_USERS_EDIT_PROFILE_THEME,
                'url'   => href_to_profile($profile, ['edit', 'theme'])
            ];
        }

        if (cmsEventsManager::getEventListeners('user_notify_types')) {

            $menu[] = [
                'title' => LANG_USERS_EDIT_PROFILE_NOTICES,
                'url'   => href_to_profile($profile, ['edit', 'notices'])
            ];
        }

        if (!empty($this->options['is_friends_on'])) {

            $menu[] = [
                'title' => LANG_USERS_EDIT_PROFILE_PRIVACY,
                'url'   => href_to_profile($profile, ['edit', 'privacy'])
            ];
        }

        if ($this->is_own_profile || (!$profile['is_admin'] && $this->cms_user->is_admin)) {

            $menu[] = [
                'title' => LANG_SECURITY,
                'url'   => href_to_profile($profile, ['edit', 'password'])
            ];

            $menu[] = [
                'title' => LANG_USERS_SESSIONS,
                'url'   => href_to_profile($profile, ['edit', 'sessions'])
            ];
        }

        list($menu, $profile) = cmsEventsManager::hook('profile_edit_menu', [$menu, $profile]);

        return $menu;
    }

    public function renderProfilesList($page_url, $dataset_name = false, $actions = false) {

        $page = $this->request->get('page', 1);
        $perpage = (empty($this->options['limit']) ? 15 : $this->options['limit']);

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('{users}');

        $this->model->filterEqual('is_locked', null);

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        list($fields, $this->model) = cmsEventsManager::hook('profiles_list_filter', array($fields, $this->model));

        $filters = [];

        // проверяем запросы фильтрации по полям
        foreach ($fields as $name => $field) {

            if (!$field['is_in_filter']) {
                continue;
            }

            $field['handler']->setItem(['ctype_name' => 'users', 'id' => null])->setContext('filter');

            $fields[$name] = $field;

            if (!$this->request->has($name)) {
                continue;
            }

            $value = $this->request->get($name, false, $field['handler']->getDefaultVarType(true));
            if (!$value) {
                continue;
            }

            $value = $field['handler']->storeFilter($value);
            if (!$value) {
                continue;
            }

            if ($field['handler']->applyFilter($this->model, $value) !== false) {
                $filters[$name] = $value;
            }
        }

        // Фильтр по IP только для администраторов
        if($this->cms_user->is_admin && $this->request->has('ip')){
            $this->model->filterEqual('ip', $this->request->get('ip', ''));
        }

        // Получаем количество и список записей
        $total = $this->model->getUsersCount();

        if ($this->request->has('show_count')) {

            $hint = LANG_SHOW . ' ' . html_spellcount($total, LANG_USERS_SPELL, false, false, 0);

            return $this->cms_template->renderJSON([
                'count'       => $total,
                'filter_link' => false,
                'hint'        => $hint
            ]);
        }

        $profiles = $this->model->getUsers($actions);

        if ($this->request->isStandard()) {
            if (!$profiles && $page > 1) {
                cmsCore::error404();
            }
        }

        $this->model->makeProfileFields($fields, $profiles, $this->cms_user);

        list($profiles, $fields) = cmsEventsManager::hook('profiles_before_list', [$profiles, $fields]);

        return $this->cms_template->renderInternal($this, 'list', [
            'page_url'     => $page_url,
            'fields'       => $fields,
            'filters'      => $filters,
            'page'         => $page,
            'perpage'      => $perpage,
            'total'        => $total,
            'profiles'     => $profiles,
            'dataset_name' => $dataset_name,
            'user'         => $this->cms_user
        ]);
    }

    public function getDatasets() {

        $datasets = [];

        // Все (новые)
        $datasets['all'] = [
            'name'  => 'all',
            'title' => LANG_USERS_DS_LATEST,
            'order' => ['date_reg', 'desc']
        ];

        // Онлайн
        if ($this->options['is_ds_online']) {
            $datasets['online'] = [
                'name'   => 'online',
                'title'  => LANG_USERS_DS_ONLINE,
                'order'  => ['date_log', 'desc'],
                'filter' => function ($model, $dset) {
                    return $model->joinSessionsOnline('i')->filterOnlineUsers();
                }
            ];
        }

        // Рейтинг
        if ($this->options['is_ds_rating']) {
            $datasets['rating'] = [
                'name'  => 'rating',
                'title' => LANG_USERS_DS_RATED,
                'order' => ['karma desc, rating desc', '']
            ];
        }

        // Популярные
        if ($this->options['is_ds_popular']) {
            $datasets['popular'] = [
                'name'  => 'popular',
                'title' => LANG_USERS_DS_POPULAR,
                'order' => ['friends_count', 'desc']
            ];
        }

        // Группы
        $f_groups = $this->model->getFilteredGroups();

        if ($f_groups) {
            foreach ($f_groups as $group) {
                $datasets[$group['name']] = [
                    'name'   => $group['name'],
                    'title'  => $group['title'],
                    'order'  => ['date_reg', 'desc'],
                    'filter' => function ($model, $dset) {
                        return $model->filterGroupByName($dset['name']);
                    }
                ];
            }
        }

        return cmsEventsManager::hook('profiles_datasets', $datasets);
    }

    public function logoutLockedUser($user) {

        $now        = time();
        $lock_until = !empty($user['lock_until']) ? strtotime($user['lock_until']) : false;

        if ($lock_until && ($lock_until <= $now)) {

            $this->model->unlockUser($user['id']);

            return $user;
        }

        $notice_text = [sprintf(LANG_USERS_LOCKED_NOTICE)];

        if ($user['lock_until']) {
            $notice_text[] = sprintf(LANG_USERS_LOCKED_NOTICE_UNTIL, html_date($user['lock_until']));
        }

        if ($user['lock_reason']) {
            $notice_text[] = sprintf(LANG_USERS_LOCKED_NOTICE_REASON, $user['lock_reason']);
        }

        if ($user['lock_reason']) {
            $this->model->update('{users}', $user['id'], [
                'ip' => null
            ], true);
        }

        cmsUser::addSessionMessage(implode('<br>', $notice_text), 'error');

        cmsUser::logout();

        return [];
    }

    public function listIsAllowed() {

        if(empty($this->options['list_allowed'])){
            return true;
        }

        return $this->cms_user->isInGroups($this->options['list_allowed']);

    }

    public function isSlugAllowed($value) {

        if ($this->cms_user->is_admin || !isset($this->options['restricted_slugs'])) { return true; }

        return !string_in_mask_list($value, $this->options['restricted_slugs']);
    }

}
