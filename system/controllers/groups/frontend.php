<?php

class groups extends cmsFrontend{

    const JOIN_POLICY_FREE = 0;
    const JOIN_POLICY_PUBLIC = 1;
    const JOIN_POLICY_PRIVATE = 2;

    const EDIT_POLICY_OWNER = 0;
    const EDIT_POLICY_STAFF = 1;

    const WALL_POLICY_MEMBERS = 0;
    const WALL_POLICY_STAFF = 1;
    const WALL_POLICY_OWNER = 2;

    const ROLE_NONE = 0;
    const ROLE_MEMBER = 1;
    const ROLE_STAFF = 2;

    protected $useOptions = true;
    public $useSeoOptions = true;

    public function routeAction($action_name){

        if (!is_numeric($action_name)){ return $action_name; }

        $this->lock_explicit_call = false;

        $group_id = $action_name;

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        // кешируем запись для получения ее в виджетах
        cmsModel::cacheResult('current_group', $group);
        cmsModel::cacheResult('group_model', $this->model);

        $this->current_params = $this->cms_core->uri_params;
        $this->current_params[0] = $group;

        $membership = $this->model->getMembership($group['id'], $this->cms_user->id);
        $is_member = ($membership !== false);

        if ($group['is_closed'] && !$is_member && !$this->cms_user->is_admin &&
                (empty($this->cms_core->uri_params[0]) || $this->cms_core->uri_params[0]!='join')){
            return 'group_closed';
        }

        if (!$this->cms_core->uri_params){ return 'group'; }

        $action_name = $this->cms_core->uri_params[0];

        $action_name = 'group_' . $action_name;

        return $action_name;

    }

    public function renderGroupsList($page_url, $dataset_name=false){

        $page = $this->request->get('page', 1);
        $perpage = (empty($this->options['limit']) ? 10 : $this->options['limit']);

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        cmsEventsManager::hook('groups_list_filter', $this->model);

        // Получаем количество и список записей
        $total  = $this->model->getGroupsCount();
        $groups = $this->model->getGroups();

        if($this->request->isStandard()){
            if(!$groups && $page > 1){ cmsCore::error404(); }
        }

        $groups = cmsEventsManager::hook('groups_before_list', $groups);

        return $this->cms_template->renderInternal($this, 'list', array(
            'page_url'     => $page_url,
            'page'         => $page,
            'perpage'      => $perpage,
            'total'        => $total,
            'groups'       => $groups,
            'dataset_name' => $dataset_name,
            'user'         => $this->cms_user
        ));

    }

    public function getGroupTabs($group){

        $menu = array();

        $menu[] = array(
            'title' => LANG_GROUPS_PROFILE_INDEX,
            'controller' => $this->name,
            'action' => $group['id'],
        );

        if ($group['content_count']){
            $menu[] = array(
                'title' => LANG_GROUPS_PROFILE_CONTENT,
                'controller' => $this->name,
                'action' => $group['id'],
                'params' => array('content', $group['first_ctype_name']),
                'url_mask' => href_to($this->name, $group['id'], 'content'),
                'counter' => $group['content_count']
            );
        }

        $menu[] = array(
            'title' => LANG_GROUPS_PROFILE_ACTIVITY,
            'controller' => $this->name,
            'action' => $group['id'],
            'params' => 'activity',
        );

        $menu[] = array(
            'title' => LANG_GROUPS_PROFILE_MEMBERS,
            'controller' => $this->name,
            'action' => $group['id'],
            'params' => "members",
            'counter' => $group['members_count']
        );

        list($menu, $group) = cmsEventsManager::hook('group_tabs', array($menu, $group));

        return $menu;

    }

    public function getDatasets(){

        $datasets = array();

        // Популярные
        if ($this->options['is_ds_popular']){
            $datasets['popular'] = array(
                'name' => 'popular',
                'title' => LANG_GROUPS_DS_POPULAR,
                'order' => array('members_count', 'desc')
            );
        }

        // Все (новые)
        $datasets['all'] = array(
                'name' => 'all',
                'title' => LANG_GROUPS_DS_LATEST,
                'order' => array('date_pub', 'desc')
        );

        // Рейтинг
        if ($this->options['is_ds_rating']){
            $datasets['rating'] = array(
                'name' => 'rating',
                'title' => LANG_GROUPS_DS_RATED,
                'order' => array('rating', 'desc')
            );
        }

        // Мои
        if (cmsUser::isLogged()){
            $datasets['my'] = array(
                'name' => 'my',
                'title' => LANG_GROUPS_DS_MY,
                'order' => array('title', 'asc'),
                'filter' => function($model, $dset){
                    $user = cmsUser::getInstance();
                    return $model->filterByMember($user->id);
                }
            );
        }

        return cmsEventsManager::hook('group_datasets', $datasets);

    }

    public function getGroupEditMenu($group){

        $menu = array();

        $menu[] = array(
            'title' => LANG_GROUPS_EDIT_MAIN,
            'controller' => $this->name,
            'action' => $group['id'],
            'params' => 'edit',
        );

        if ($this->cms_user->id == $group['owner_id'] || $this->cms_user->is_admin){
            $menu[] = array(
                'title' => LANG_GROUPS_EDIT_STAFF,
                'controller' => $this->name,
                'action' => $group['id'],
                'params' => array('edit', 'staff'),
            );
        }

        list($menu, $group) = cmsEventsManager::hook('group_edit_menu', array($menu, $group));

        return $menu;

    }

    public function sendInvite($invited_users_list, $group_id){

        $group = $this->model->getGroup($group_id);

        if (!$group){ cmsCore::error404(); }

        if (!is_array($invited_users_list)) { $invited_users_list = array($invited_users_list); }

        $messenger = cmsCore::getController('messages');

        foreach($invited_users_list as $invited_id){

            $messenger->addRecipient($invited_id);

            //
            // Личное сообщение
            //
            $sender_link = '<a href="'.href_to('users', $this->cms_user->id).'">'.$this->cms_user->nickname.'</a>';
            $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

            $notice = array(
                'content' => sprintf(LANG_GROUPS_INVITE_NOTICE, $sender_link, $group_link),
                'options' => array(
                    'is_closeable' => true
                ),
                'actions' => array(
                    'accept' => array(
                        'title' => LANG_ACCEPT,
                        'href'  => href_to('groups', $group['id'], 'join')
                    ),
                    'decline' => array(
                        'title'      => LANG_DECLINE,
                        'controller' => $this->name,
                        'action'     => 'invite_delete',
                        'params'     => array($group['id'], $invited_id)
                    )
                )
            );

            $messenger->sendNoticePM($notice, 'groups_invite');

            //
            // E-mail
            //
            $messenger->sendNoticeEmail('groups_invite', array(
                'user_nickname' => $this->cms_user->nickname,
                'user_url'      => href_to_abs('users', $this->cms_user->id),
                'group_title'   => $group['title'],
                'group_url'     => href_to_abs('groups', $group['id'])
            ));

            $messenger->clearRecipients();

            $this->model->addInvite(array(
                'group_id'   => $group['id'],
                'user_id'    => $this->cms_user->id,
                'invited_id' => $invited_id
            ));

        }

        cmsUser::addSessionMessage(LANG_GROUPS_INVITE_SENT, 'success');

        $this->redirectBack();

    }

}
