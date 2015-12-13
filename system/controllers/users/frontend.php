<?php

class users extends cmsFrontend {

    protected $useOptions = true;

    public $tabs = array();
    public $tabs_controllers = array();

    public function routeAction($action_name){

        if (!is_numeric($action_name)){ return $action_name; }

        $core = cmsCore::getInstance();
        $user = cmsUser::getInstance();

        $user_id = $action_name;

        $profile = $this->model->getUser($user_id);
        if (!$profile) { cmsCore::error404(); }

        if (!$user->is_logged && $this->options['is_auth_only']){
            cmsUser::goLogin();
        }

        $template = cmsTemplate::getInstance();
        $template->applyProfileStyle($profile);

        $this->current_params = $core->uri_params;

        // Статус
        if ($this->options['is_status']) {
            $profile['status'] = $this->model->getUserStatus($profile['status_id']);
        }

        // Репутация
        $profile['is_can_vote_karma'] = $user->is_logged &&
                                        cmsUser::isAllowed('users', 'vote_karma') &&
                                        ($user->id != $profile['id']) &&
                                        $this->model->isUserCanVoteKarma($user->id, $profile['id'], $this->options['karma_time']);

        // Нет параметров после названия экшена (/users/id) - значит
        // это главная страница профиля, первым параметром добавляем
        // сам профиль
        if (!$core->uri_params){
            array_unshift($this->current_params, $profile);
            return 'profile';
        }

        // Ищем экшен внутри профиля
        if ($this->isActionExists('profile_'.$core->uri_params[0])){
            $this->current_params[0] = $profile;
            return 'profile_'.$core->uri_params[0];
        }

        // Если дошли сюда, значит это неизвестный экшен, возможно вкладка
        // от другого контроллера, тогда первым параметром добавляем
        // сам профиль
        array_unshift($this->current_params, $profile);
        return 'profile_tab';

    }

    public function getProfileMenu($profile){

        $menu = array(
            array(
                'title' => LANG_USERS_PROFILE_INDEX,
                'url' => href_to($this->name, $profile['id']),
                'url_mask' => href_to($this->name, $profile['id']),
            )
        );

        $this->tabs = $this->model->getUsersProfilesTabs(true, 'name');

        $this->tabs_controllers = array();

		if ($this->tabs){
			foreach($this->tabs as $tab){

				$default_tab_info = array(
					'title' => $tab['title'],
					'url' => href_to($this->name, $profile['id'], $tab['name'])
				);

                if(!$this->isControllerEnabled($tab['controller'])){ continue; }

				if (empty($this->tabs_controllers[$tab['controller']])){
					$controller = cmsCore::getController($tab['controller'], $this->request);
				} else {
					$controller = $this->tabs_controllers[$tab['controller']];
				}

				$tab_info = $controller->runHook('user_tab_info', array('profile'=>$profile, 'tab_name'=>$tab['name']));

				if ($tab_info == false) {
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

    public function getProfileEditMenu($profile){

        $template = cmsTemplate::getInstance();

        $menu = array();

        $menu[] = array(
            'title' => LANG_USERS_EDIT_PROFILE_MAIN,
            'controller' => $this->name,
            'action' => $profile['id'],
            'params' => 'edit',
        );

        if ($template->hasProfileThemesOptions() && $this->options['is_themes_on']){
            $menu[] = array(
                'title' => LANG_USERS_EDIT_PROFILE_THEME,
                'controller' => $this->name,
                'action' => $profile['id'],
                'params' => array('edit', 'theme'),
            );
        }

        $menu[] = array(
            'title' => LANG_USERS_EDIT_PROFILE_NOTICES,
            'controller' => $this->name,
            'action' => $profile['id'],
            'params' => array('edit', 'notices'),
        );

        $menu[] = array(
            'title' => LANG_USERS_EDIT_PROFILE_PRIVACY,
            'controller' => $this->name,
            'action' => $profile['id'],
            'params' => array('edit', 'privacy'),
        );

        $menu[] = array(
            'title' => LANG_PASSWORD,
            'controller' => $this->name,
            'action' => $profile['id'],
            'params' => array('edit', 'password'),
        );

        return $menu;

    }

    public function renderProfilesList($page_url, $dataset_name=false){

        if ($this->request->isInternal()){
            if ($this->useOptions){
               $this->options = $this->getOptions();
            }
        }

        $user = cmsUser::getInstance();

        $page = $this->request->get('page', 1);
        $perpage = 15;

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('users');

        $filters = array();

        // проверяем запросы фильтрации по полям
        foreach($fields as $name => $field){
            if (!$field['is_in_filter']) { continue; }
            if (!$this->request->has($name)){ continue; }
            $value = $this->request->get($name);
            if (!$value) { continue; }
            $this->model = $field['handler']->applyFilter($this->model, $value);
            $filters[$name] = $value;
        }

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        // Получаем количество и список записей
        $total = $this->model->getUsersCount();
        $profiles = $this->model->getUsers();

        return cmsTemplate::getInstance()->renderInternal($this, 'list', array(
            'page_url' => $page_url,
            'fields' => $fields,
            'filters' => $filters,
            'page' => $page,
            'perpage' => $perpage,
            'total' => $total,
            'profiles' => $profiles,
            'dataset_name' => $dataset_name,
            'user' => $user
        ));

    }

    public function getDatasets(){

        $datasets = array();

        // Все (новые)
        $datasets['all'] = array(
                'name' => 'all',
                'title' => LANG_USERS_DS_LATEST,
                'order' => array('date_reg', 'desc')
        );

        // Онлайн
        if ($this->options['is_ds_online']){
            $datasets['online'] = array(
                'name' => 'online',
                'title' => LANG_USERS_DS_ONLINE,
                'order' => array('date_log', 'desc'),
                'filter' => function($model, $dset){
                    return $model->joinInner('sessions_online', 'online', 'i.id = online.user_id');
                }
            );
        }

        // Рейтинг
        if ($this->options['is_ds_rating']){
            $datasets['rating'] = array(
                'name' => 'rating',
                'title' => LANG_USERS_DS_RATED,
                'order' => array('karma desc, rating desc', '')
            );
        }

        // Популярные
        if ($this->options['is_ds_popular']){
            $datasets['popular'] = array(
                'name' => 'popular',
                'title' => LANG_USERS_DS_POPULAR,
                'order' => array('friends_count', 'desc')
            );
        }

        // Группы
        $f_groups = $this->model->getFilteredGroups();

        if ($f_groups){
            foreach($f_groups as $group){
                $datasets[ $group['name'] ] = array(
                    'name' => $group['name'],
                    'title' => $group['title'],
                    'order' => array('date_reg', 'desc'),
                    'filter' => function($model, $dset){
                        return $model->filterGroupByName($dset['name']);
                    }
                );
            }
        }

        return $datasets;

    }

    public function logoutLockedUser($user){

        $now = time();
        $lock_until = !empty($user['lock_until']) ? strtotime($user['lock_until']) : false;

        if ($lock_until && ($lock_until <= $now)){
            $this->model->unlockUser($user['id']);
            return;
        }

        $notice_text = array();

        $notice_text[] = sprintf(LANG_USERS_LOCKED_NOTICE);

        if($user['lock_until']) {
            $notice_text[] = sprintf(LANG_USERS_LOCKED_NOTICE_UNTIL, html_date($user['lock_until']));
        }

        if($user['lock_reason']) {
            $notice_text[] = sprintf(LANG_USERS_LOCKED_NOTICE_REASON, $user['lock_reason']);
        }

        $notice_text = implode('<br>', $notice_text);

        cmsUser::addSessionMessage($notice_text, 'error');

        cmsUser::logout();

        return;

    }

}