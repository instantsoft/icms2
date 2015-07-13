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

    public function routeAction($action_name){

        if (!is_numeric($action_name)){ return $action_name; }

        $group_id = $action_name;

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $core = cmsCore::getInstance();
        $user = cmsUser::getInstance();

        $this->current_params = $core->uri_params;
        $this->current_params[0] = $group;

        $membership = $this->model->getMembership($group['id'], $user->id);
        $is_member = ($membership !== false);

        if ($group['is_closed'] && !$is_member && !$user->is_admin && (empty($core->uri_params[0]) || $core->uri_params[0]!='join')){ return 'group_closed'; }

        if (!$core->uri_params){ return 'group'; }

        $action_name = $core->uri_params[0];

        $action_name = 'group_' . $action_name;

        return $action_name;

    }

    public function renderGroupsList($page_url, $dataset_name=false){

        $template = cmsTemplate::getInstance();
        $user = cmsUser::getInstance();

        $page = $this->request->get('page', 1);
        $perpage = 10;

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        // Получаем количество и список записей
        $total = $this->model->getGroupsCount();
        $groups = $this->model->getGroups();

        return $template->renderInternal($this, 'list', array(
            'page_url' => $page_url,
            'page' => $page,
            'perpage' => $perpage,
            'total' => $total,
            'groups' => $groups,
            'dataset_name' => $dataset_name,
            'user' => $user
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

        return $datasets;

    }

    public function getGroupEditMenu($group){

        $user = cmsUser::getInstance();

        $menu = array();

        $menu[] = array(
            'title' => LANG_GROUPS_EDIT_MAIN,
            'controller' => $this->name,
            'action' => $group['id'],
            'params' => 'edit',
        );

        if ($user->id == $group['owner_id'] || $user->is_admin){
            $menu[] = array(
                'title' => LANG_GROUPS_EDIT_STAFF,
                'controller' => $this->name,
                'action' => $group['id'],
                'params' => array('edit', 'staff'),
            );
        }

        return $menu;

    }

    public function sendInvite($invited_users_list, $group_id){

        $user = cmsUser::getInstance();

        $group = $this->model->getGroup($group_id);

        if (!$group){ cmsCore::error404(); }

        if (!is_array($invited_users_list)) { $invited_users_list = array($invited_users_list); }

        $messenger = cmsCore::getController('messages');

        foreach($invited_users_list as $invited_id){

            $messenger->addRecipient($invited_id);

            //
            // Личное сообщение
            //
            $sender_link = '<a href="'.href_to('users', $user->id).'">'.$user->nickname.'</a>';
            $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

            $notice = array(
                'content' => sprintf(LANG_GROUPS_INVITE_NOTICE, $sender_link, $group_link),
                'options' => array(
                    'is_closeable' => true
                ),
                'actions' => array(
                    'accept' => array(
                        'title' => LANG_ACCEPT,
                        'href' => href_to('groups', $group['id'], 'join')
                    ),
                    'decline' => array(
                        'title' => LANG_DECLINE,
                        'controller' => $this->name,
                        'action' => 'invite_delete',
                        'params' => array($group['id'], $invited_id),
                    )
                )
            );

            $messenger->sendNoticePM($notice, 'groups_invite');

            //
            // E-mail
            //
            $messenger->sendNoticeEmail('groups_invite', array(
                'user_nickname' => $user->nickname,
                'user_url' => href_to_abs('users', $user->id),
                'group_title' => $group['title'],
                'group_url' => href_to_abs('groups', $group['id']),
            ));

            $messenger->clearRecipients();

            $this->model->addInvite(array(
                'group_id' => $group['id'],
                'user_id' => $user->id,
                'invited_id' => $invited_id
            ));

        }

        cmsUser::addSessionMessage(LANG_GROUPS_INVITE_SENT, 'success');
        $this->redirectBack();

    }

}
