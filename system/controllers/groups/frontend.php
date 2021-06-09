<?php
/**
 * Контроллер сообществ
 *
 * @property \modelGroups $model
 */
class groups extends cmsFrontend {

    const JOIN_POLICY_FREE = 0;
    const JOIN_POLICY_PUBLIC = 1;
    const JOIN_POLICY_PRIVATE = 2;

    const EDIT_POLICY_OWNER = 0;
    const EDIT_POLICY_STAFF = 1;

    const WALL_POLICY_MEMBERS = 0;
    const WALL_POLICY_STAFF = 1;
    const WALL_POLICY_OWNER = 2;

    const CTYPE_POLICY_MEMBERS = 0;
    const CTYPE_POLICY_STAFF = 1;
    const CTYPE_POLICY_OWNER = 2;
    const CTYPE_POLICY_GROUPS = 3;
    const CTYPE_POLICY_ROLES = 4;

    const ROLE_NONE = 0;
    const ROLE_MEMBER = 1;
    const ROLE_STAFF = 2;

    protected $useOptions = true;
    public $useSeoOptions = true;

    private static $groups_fields = null;

    public $max_items_count = 0;

    public function routeAction($action_name){

        if (is_numeric($action_name)) {

            $group = $this->model->getGroup($action_name);
            if (!$group) { cmsCore::error404(); }

            if($group['slug'] != $action_name){
                return $this->redirect(href_to('groups', $group['slug'], $this->current_params), 301);
            }

        } else {

            if($action_name === 'index' && $this->current_params){
                return $this->redirect(href_to('groups', $this->current_params[0]), 301);
            }

            if ($this->isActionExists($action_name)){
                return $action_name;
            }

            $group = $this->model->getGroupBySlug($action_name);
            if (!$group) {

                array_unshift($this->current_params, $action_name);

                return 'index';
            }

        }

        $this->lock_explicit_call = false;

        $group['access'] = $this->getGroupAccess($group);

        if(empty($this->current_params[0]) || $this->current_params[0] != 'edit'){

            $this->loadGroupsFields();

            $group['fields'] = $this->getGroupsFields();

            $group['content_counts'] = $this->getGroupContentCounts($group);

            $group['content_count'] = 0;
            $group['first_ctype_name'] = false;

            if ($group['content_counts']){
                foreach($group['content_counts'] as $ctype_name => $count){
                    if (!$count['is_in_list'] || !$count['count']) { continue; }
                    if (!$group['first_ctype_name']) { $group['first_ctype_name'] = $ctype_name; }
                    $group['content_count'] += $count['count'];
                }
            }

        }

        // кешируем запись для получения ее в виджетах
        cmsModel::cacheResult('current_group', $group);
        cmsModel::cacheResult('group_model', $this->model);

        $this->current_params = $this->cms_core->uri_params;
        $this->current_params[0] = $group;

        if ($group['is_closed'] && !$group['access']['is_member'] && !$this->cms_user->is_admin &&
                (empty($this->cms_core->uri_params[0]) || !in_array($this->cms_core->uri_params[0], array('join', 'enter')))){
            return 'group_closed';
        }

        if (!$this->cms_core->uri_params){ return 'group'; }

        if ($this->cms_core->uri_action !== $group['slug']) {
            $this->redirect(href_to('groups', $group['slug'], $this->cms_core->uri_params), 301);
        }

        return 'group_' . $this->cms_core->uri_params[0];
    }

    public function getGroupAccess($group) {

        $membership = $this->model->getMembership($group['id'], $this->cms_user->id);

        $is_member = ($membership !== false);
        $is_owner = ($this->cms_user->id == $group['owner_id']);

        $access = array(
            'is_moderator'  => $this->cms_user->is_admin,
            'is_can_edit'   => false,
            'is_can_join'   => false,
            'is_can_delete' => false,
            'is_owner'      => $is_owner,
            'is_member'     => $is_member,
            'member_role'   => ($is_member ? $membership['role'] : groups::ROLE_NONE),
            'invite'        => $this->model->getInvite($group['id'], $this->cms_user->id),
            'is_can_invite' => (($is_member && ($group['join_policy'] != groups::JOIN_POLICY_PRIVATE)) || $is_owner),
            'is_can_invite_users' => false,
            'wall' => array(
                'add'    => false,
                'reply'  => false,
                'delete' => false
            )
        );

        if ($this->cms_user->is_admin || (
                $access['is_member'] && (
                    ($group['wall_policy'] == groups::WALL_POLICY_MEMBERS) ||
                    ($group['wall_policy'] == groups::WALL_POLICY_STAFF && $access['member_role']==groups::ROLE_STAFF) ||
                    $access['is_owner']
                )
            )) {
            $access['wall']['add'] = (bool)$group['is_approved'];
        }
        if ($this->cms_user->is_admin || (
                $access['is_member'] && (
                    ($group['wall_reply_policy'] == groups::WALL_POLICY_MEMBERS) ||
                    ($group['wall_reply_policy'] == groups::WALL_POLICY_STAFF && $access['member_role']==groups::ROLE_STAFF) ||
                    $access['is_owner']
                )
            )) {
            $access['wall']['reply'] = true;
        }
        if ($this->cms_user->is_admin || $access['is_owner']) {
            $access['wall']['delete'] = true;
        }

        if ($access['member_role'] == groups::ROLE_STAFF) {
            $access['is_can_invite'] = (bool)$group['is_approved'];
        }

        if (cmsUser::isAllowed('groups', 'invite_users') && $access['is_can_invite']) {
            $access['is_can_invite_users'] = (bool)$group['is_approved'];
        }
        if (cmsUser::isAllowed('groups', 'edit', 'all')) {

            $access['is_can_edit'] = true;

        } else if (cmsUser::isAllowed('groups', 'edit', 'own')) {
            if (($access['member_role'] == groups::ROLE_STAFF && $group['edit_policy'] == groups::EDIT_POLICY_STAFF) ||
                    $access['is_owner'] || cmsUser::isAllowed('groups', 'edit', 'all')){

                $access['is_can_edit'] = (bool)$group['is_approved'];

            }
        }

        if ($this->cms_user->id && !$access['is_member'] && ($group['join_policy'] == groups::JOIN_POLICY_FREE || $access['invite'])){
            $access['is_can_join'] = (bool)$group['is_approved'];
        }

        if (cmsUser::isAllowed('groups', 'delete', 'all') || (cmsUser::isAllowed('groups', 'delete', 'own') && $access['is_owner'])){
            $access['is_can_delete'] = $group['is_approved'] ? (bool)$group['is_approved'] : cmsUser::isAllowed('groups', 'delete', 'all');
        }

        return cmsEventsManager::hook('group_item_access', $access);

    }

    public function getGroupsFields() {
        return self::$groups_fields;
    }

    public function loadGroupsFields() {
        if(self::$groups_fields === null){
            self::$groups_fields = cmsCore::getModel('content')->setTablePrefix('')->orderBy('ordering')->getContentFields('groups');
        }
        return $this;
    }

    public function getGroupForm($group = false, $action = 'add'){

        if($group === false){

            $group_id = false;

        } else {

            $group_id = $group['id'];

            $group['content_counts'] = $this->getGroupContentCounts($group);

        }

        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');

        $fields = $content_model->getContentFields('groups', $group_id);

        if(self::$groups_fields === null){
            self::$groups_fields = $fields;
        }

        $form = new cmsForm();

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($group, $action){

            if($action === 'add'){
                // проверяем что группа пользователя имеет доступ к созданию этого поля
                // на автора не надо проверять, ибо это и есть автор
                if ($field['groups_add'] && !$user->isInGroups($field['groups_add'])) {
                    return false;
                }
            } else {
                // проверяем что группа пользователя имеет доступ к редактированию этого поля
                if ($field['groups_edit'] && !$user->isInGroups($field['groups_edit'])) {
                    // если группа пользователя не имеет доступ к редактированию этого поля,
                    // проверяем на доступ к нему для авторов
                    if (!empty($group['owner_id']) && !empty($field['options']['author_access'])){
                        if (!in_array('is_edit', $field['options']['author_access'])){ return false; }
                        if ($group['owner_id'] == $user->id){ return true; }
                    }
                    return false;
                }
            }

            return true;

        });

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid);

            foreach($fieldset['fields'] as $field){

                $form->addField($fieldset_id, $field['handler']);

            }

        }

        // настройки группы
        $fieldset_id = $form->addFieldset(LANG_OPTIONS, 'group_options');

        $form->addField($fieldset_id, new fieldList('join_policy', array(
            'title' => LANG_GROUPS_GROUP_JOIN_POLICY,
            'items' => array(
                groups::JOIN_POLICY_FREE => LANG_GROUPS_GROUP_PUBLIC,
                groups::JOIN_POLICY_PUBLIC => LANG_GROUPS_GROUP_PRIVATE_SOFT,
                groups::JOIN_POLICY_PRIVATE => LANG_GROUPS_GROUP_PRIVATE_HARD,
            )
        )));
        if(!empty($group['roles'])){
            $form->addField($fieldset_id, new fieldList('join_roles', array(
                'title'              => LANG_GROUPS_JOIN_ROLES,
                'items'              => (array(null => '') + $group['roles']),
                'is_chosen_multiple' => true
            )));
        }
        $form->addField($fieldset_id, new fieldList('is_closed', array(
            'title' => LANG_GROUPS_GROUP_IS_CLOSED,
            'items' => array(
                0 => LANG_GROUPS_GROUP_OPENED,
                1 => LANG_GROUPS_GROUP_CLOSED,
            )
        )));
        $form->addField($fieldset_id, new fieldList('edit_policy', array(
            'title' => LANG_GROUPS_GROUP_EDIT_POLICY,
            'items' => array(
                groups::EDIT_POLICY_OWNER => LANG_GROUPS_GROUP_EDIT_OWNER,
                groups::EDIT_POLICY_STAFF => LANG_GROUPS_GROUP_EDIT_STAFF,
            )
        )));
        if ($this->options['is_wall'] && cmsController::enabled('wall')){
            $form->addField($fieldset_id, new fieldList('wall_policy', array(
                'title' => LANG_GROUPS_GROUP_WALL_POLICY,
                'items' => array(
                    groups::WALL_POLICY_MEMBERS => LANG_GROUPS_GROUP_WALL_MEMBERS,
                    groups::WALL_POLICY_STAFF => LANG_GROUPS_GROUP_WALL_STAFF,
                    groups::WALL_POLICY_OWNER => LANG_GROUPS_GROUP_WALL_OWNER,
                )
            )));
            $form->addField($fieldset_id, new fieldList('wall_reply_policy', array(
                'title' => LANG_GROUPS_GROUP_WALL_REPLY_POLICY,
                'items' => array(
                    groups::WALL_POLICY_MEMBERS => LANG_GROUPS_GROUP_WALL_MEMBERS,
                    groups::WALL_POLICY_STAFF => LANG_GROUPS_GROUP_WALL_STAFF,
                    groups::WALL_POLICY_OWNER => LANG_GROUPS_GROUP_WALL_OWNER,
                )
            )));
        }

        if(cmsUser::isAllowed('groups', 'content_access') && !empty($group['content_counts'])){
            $roles = !empty($group['roles']) ? $group['roles'] : array();
            foreach($group['content_counts'] as $ctype_name => $count){
                $form->addField($fieldset_id, new fieldList('content_policy:'.$ctype_name, array(
                    'title' => sprintf(LANG_GROUPS_GROUP_CTYPE_POLICY, $count['title_add']),
                    'items' => array(
                        groups::CTYPE_POLICY_MEMBERS => LANG_GROUPS_GROUP_CTYPE_MEMBERS,
                        groups::CTYPE_POLICY_STAFF   => LANG_GROUPS_GROUP_WALL_STAFF,
                        groups::CTYPE_POLICY_OWNER   => LANG_GROUPS_GROUP_WALL_OWNER,
                        groups::CTYPE_POLICY_GROUPS  => LANG_GROUPS_GROUP_CTYPE_GROUPS,
                        groups::CTYPE_POLICY_ROLES   => LANG_GROUPS_GROUP_CTYPE_ROLES
                    )
                )));
                $form->addField($fieldset_id, new fieldList('content_groups:'.$ctype_name, array(
                    'is_chosen_multiple' => true,
                    'is_visible' => false,
                    'generator' => function ($item){
                        $users_model = cmsCore::getModel('users');
                        $groups = $users_model->getGroups();
                        $items = array(0 => '');
                        foreach($groups as $group){
                            $items[$group['id']] = $group['title'];
                        }
                        return $items;
                    }
                )));
                $form->addField($fieldset_id, new fieldList('content_roles:'.$ctype_name, array(
                    'is_chosen_multiple' => true,
                    'is_visible' => false,
                    'generator' => function ($group) use ($roles){
                        $items = array(0 => '');
                        if(!empty($roles)){
                            foreach($roles as $role_id => $role){
                                $items[$role_id] = $role;
                            }
                        }
                        return $items;
                    }
                )));
            }
        }

        // ручной ввод SLUG, добавляем поле для этого
        $slug_field_rules = array( array('slug_segment') );

        if (!$group_id){ $slug_field_rules[] = array('unique', 'groups', 'slug'); }
        if ($group_id){ $slug_field_rules[] = array('unique_exclude', 'groups', 'slug', $group_id); }
        // Чтобы не накладывались наборы
        $slug_field_rules[] = array('unique_ctype_dataset', 'groups', false);

        $form->addField($fieldset_id, new fieldString('slug', array(
            'title'  => LANG_SLUG,
            'prefix' => href_to('groups').'/',
            'options'=>array(
                'min_length'=> 2,
                'max_length'=> 100
            ),
            'rules' => $slug_field_rules
        )));

        return cmsEventsManager::hook('group_item_form', $form);

    }

    public function renderGroupsList($page_url, $dataset_name = false){

        $page = $this->request->get('page', 1);
        $perpage = (empty($this->options['limit']) ? 10 : $this->options['limit']);

        $fields = $this->loadGroupsFields()->getGroupsFields();

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        list($fields, $this->model) = cmsEventsManager::hook('groups_list_filter', array($fields, $this->model));

        $filters = array();

        // проверяем запросы фильтрации по полям
        foreach($fields as $name => $field){

            if (!$field['is_in_filter']) { continue; }

            $field['handler']->setItem(['ctype_name' => 'groups', 'id' => null])->setContext('filter');

            $fields[$name] = $field;

            if (!$this->request->has($name)){ continue; }

            $value = $this->request->get($name, false, $field['handler']->getDefaultVarType(true));
            if (!$value) { continue; }

            $value = $field['handler']->storeFilter($value);
			if (!$value) { continue; }

            if($field['handler']->applyFilter($this->model, $value) !== false){
                $filters[$name] = $value;
            }

        }

        // Получаем количество и список записей
        $total  = $this->model->getGroupsCount();

        if($this->request->has('show_count')){

            $hint = LANG_SHOW.' '.html_spellcount($total, LANG_GROUPS_GROUP_SPELLCOUNT, false, false, 0);

            return $this->cms_template->renderJSON([
                'count'       => $total,
                'filter_link' => false,
                'hint'        => $hint
            ]);

        }

        $groups = $this->model->getGroups();

        // если задано максимальное кол-во, ограничиваем им
        if($this->max_items_count){
            $total = min($total, $this->max_items_count);
            $pages = ceil($total / $perpage);
            if($page > $pages){
                $groups = false;
            }
        }

        if($this->request->isStandard()){
            if(!$groups && $page > 1){ cmsCore::error404(); }
        }

        list($groups, $fields) = cmsEventsManager::hook('groups_before_list', array($groups, $fields));

        // строим массив полей для списка
        if($groups){
            foreach ($groups as $key => $group) {
                foreach($fields as $name => $field){

                    if ($field['is_system'] || !$field['is_in_list'] || !isset($group[$field['name']])) { continue; }
                    if ($field['groups_read'] && !$this->cms_user->isInGroups($field['groups_read'])) { continue; }
                    if (!$group[$field['name']] && $group[$field['name']] !== '0') { continue; }

                    if (!isset($field['options']['label_in_list'])) {
                        $label_pos = 'none';
                    } else {
                        $label_pos = $field['options']['label_in_list'];
                    }

                    $field_html = $field['handler']->setItem($group)->parseTeaser($group[$field['name']]);
                    if (!$field_html) { continue; }

                    $groups[$key]['fields'][$field['name']] = array(
                        'label_pos' => $label_pos,
                        'type'      => $field['type'],
                        'name'      => $field['name'],
                        'title'     => $field['title'],
                        'html'      => $field_html
                    );

                }
            }
        }

        return $this->cms_template->renderInternal($this, 'list', array(
            'page_url'     => $page_url,
            'fields'       => $fields,
            'filters'      => $filters,
            'page'         => $page,
            'perpage'      => $perpage,
            'total'        => $total,
            'groups'       => $groups,
            'dataset_name' => $dataset_name,
            'user'         => $this->cms_user
        ));

    }

    public function getGroupContentCounts($group) {
        return $this->model->getGroupContentCounts($group,
                ($this->cms_user->is_admin || ($this->cms_user->id == $group['owner_id'])),
                array($this, 'filterPrivacyGroupsContent'));
    }


    public function filterPrivacyGroupsContent($ctype, $content_model, $group) {

        if(empty($ctype['is_in_groups'])){ return false; }

        $content_model->enablePrivacyFilter();

        if (!empty($ctype['options']['privacy_type']) &&
                in_array($ctype['options']['privacy_type'], array('show_title', 'show_all'), true)) {

            $content_model->disablePrivacyFilter();

        }
        if($group['access']['member_role'] == groups::ROLE_STAFF){

            $content_model->disablePrivacyFilter();

        }
        if (cmsUser::isAllowed($ctype['name'], 'view_all')) {

            $content_model->disablePrivacyFilter();

        }
        if($group['access']['is_member'] && $content_model->isEnablePrivacyFilter()){

            $content_model->disablePrivacyFilter();

            $privacy = array(0, 3, 5);

            if(cmsUser::isAllowed($ctype['name'], 'add')){
                $privacy[] = 4;
            }

            $content_model->filterIn('i.is_private', $privacy);

        }

        return true;

    }

    public function getGroupTabs($group){

        $menu = array();

        $menu[] = array(
            'title'      => LANG_GROUPS_PROFILE_INDEX,
            'controller' => $this->name,
            'action'     => $group['slug'],
        );

        if ($group['content_counts']){
            foreach($group['content_counts'] as $ctype_name => $count){
                if (!$count['is_in_list'] || !$count['count']) { continue; }
                $menu[] = array(
                    'title'      => $count['title'],
                    'controller' => $this->name,
                    'action'     => $group['slug'],
                    'params'     => array('content', $ctype_name),
                    'counter'    => $count['count']
                );
            }
        }

        if ($this->isControllerEnabled('activity')) {
            $menu[] = array(
                'title'      => LANG_GROUPS_PROFILE_ACTIVITY,
                'controller' => $this->name,
                'action'     => $group['slug'],
                'params'     => 'activity',
            );
        }

        $menu[] = array(
            'title'      => LANG_GROUPS_PROFILE_MEMBERS,
            'controller' => $this->name,
            'action'     => $group['slug'],
            'params'     => 'members',
            'counter'    => $group['members_count']
        );

        list($menu, $group) = cmsEventsManager::hook('group_tabs', array($menu, $group));

        return $menu;
    }

    public function getDatasets(){

        $list_type = $this->getListContext();

        $datasets = cmsCore::getModel('content')->getContentDatasets($this->name, true, function ($item, $model) use ($list_type) {

            $is_view = empty($item['list']['show']) || in_array($list_type, $item['list']['show']);
            $is_user_hide = !empty($item['list']['hide']) && in_array($list_type, $item['list']['hide']);

            if (!$is_view || $is_user_hide) { return false; }

            return $item;

        });

        if ($this->cms_user->is_logged){

            $logged_user_id = $this->cms_user->id;

            if(!$datasets && $list_type == 'category_view'){
                $datasets = array('all' => array(
                        'name' => 'all',
                        'title' => LANG_ALL
                ));
            }
            if($list_type == 'category_view'){
                $datasets['memberships'] = array(
                    'name'    => 'memberships',
                    'title'   => LANG_GROUPS_DS_MEMBER,
                    'filters' => array(
                        array(
                            'callback' => function($model, $dataset) use ($logged_user_id){
                                return $model->filterByMember($logged_user_id);
                            }
                        )
                    )
                );
                $datasets['my'] = array(
                    'name'    => 'my',
                    'title'   => LANG_GROUPS_DS_MY,
                    'sorting' => array(
                        array(
                            'by' => 'members_count',
                            'to' => 'desc'
                        )
                    ),
                    'filters' => array(
                        array(
                            'field'     => 'owner_id',
                            'condition' => 'eq',
                            'value'     => $logged_user_id
                        )
                    )
                );
            }

        }

        return cmsEventsManager::hook('group_datasets', $datasets);
    }

    public function getGroupEditMenu($group){

        $menu = array();

        $menu[] = array(
            'title'      => LANG_GROUPS_EDIT_MAIN,
            'controller' => $this->name,
            'action'     => $group['slug'],
            'params'     => 'edit'
        );

        if ($this->cms_user->id == $group['owner_id'] || $this->cms_user->is_admin){
            $menu[] = array(
                'title'      => LANG_GROUPS_EDIT_STAFF,
                'controller' => $this->name,
                'action'     => $group['slug'],
                'params'     => array('edit', 'staff')
            );
            $menu[] = array(
                'title'      => LANG_GROUPS_EDIT_ROLES,
                'controller' => $this->name,
                'action'     => $group['slug'],
                'params'     => array('edit', 'roles')
            );
        }

        if ($group['join_policy'] != groups::JOIN_POLICY_FREE && ($group['access']['member_role'] == groups::ROLE_STAFF || $this->cms_user->is_admin)){

            $users_model = cmsCore::getModel('users');

            $rcount = $this->model->filterUsersRequests($group['id'], $users_model)->getUsersCount();

            if($rcount){

                $menu[] = array(
                    'title'      => LANG_GROUPS_REQUESTS,
                    'counter'    => $rcount,
                    'controller' => $this->name,
                    'action'     => $group['slug'],
                    'params'     => array('edit', 'requests')
                );

            }

        }

        list($menu, $group) = cmsEventsManager::hook('group_edit_menu', array($menu, $group));

        return $menu;

    }

    public function getToolButtons($group) {

        $users_options = cmsController::loadOptions('users');

        $tool_buttons = array();

        if (!$group['is_approved'] && $group['access']['is_moderator']){
            $tool_buttons['groups_accept'] = array(
                'title'   => LANG_MODERATION_APPROVE,
                'options' => ['class' => 'accept', 'icon' => 'check-double'],
                'url'     => href_to('groups', $group['slug'], array('approve'))
            );
        }

        if ($group['access']['is_can_edit']){
            $tool_buttons['groups_edit'] = array(
                'title'   => LANG_GROUPS_EDIT,
                'options' => ['class' => 'settings', 'icon' => 'cogs'],
                'url'     => href_to('groups', $group['slug'], array('edit'))
            );
        }

        if ($group['access']['is_owner'] || $group['access']['is_moderator']){
            $tool_buttons['change_owner'] = array(
                'title'   => LANG_GROUPS_CHANGE_OWNER,
                'options' => ['class' => 'transfer ajax-modal', 'icon' => 'people-arrows'],
                'url'     => href_to('groups', $group['slug'], 'change_owner'),
            );
        }

        if ($group['access']['is_can_invite'] && !empty($users_options['is_friends_on'])){
            $tool_buttons['groups_invite'] = array(
                'title'   => LANG_GROUPS_INVITE_FR,
                'options' => ['class' => 'group_add ajax-modal', 'icon' => 'user-friends'],
                'url'     => href_to('groups', 'invite_friends', $group['id'])
            );
        }

        if ($group['access']['is_can_invite_users']){
            $tool_buttons['groups_invite_users'] = array(
                'title'   => LANG_GROUPS_INVITE,
                'options' => array('class' => 'group_add', 'icon' => 'user-plus'),
                'url'     => href_to('groups', 'invite_users', $group['id'])
            );
        }

        if ($group['access']['is_can_join']){
            $tool_buttons['groups_join'] = array(
                'title'   => LANG_GROUPS_JOIN,
                'options' => ['class' => 'user_add', 'confirm' => LANG_GROUPS_JOIN . '?', 'icon' => 'sign-in-alt'],
                'url'     => href_to('groups', $group['slug'], 'join'),
            );
        } elseif($this->cms_user->is_logged && !$group['access']['is_member'] && $group['join_policy'] != groups::JOIN_POLICY_FREE){
            $tool_buttons['groups_enter'] = array(
                'title'   => LANG_GROUPS_ENTER,
                'options' => ['class' => 'invites', 'confirm' => LANG_GROUPS_ENTER_CONFIRM, 'icon' => 'sign-in-alt'],
                'url'     => href_to('groups', $group['slug'], 'enter'),
            );
        }

        if ($group['access']['is_member'] && !$group['access']['is_owner']){
            $tool_buttons['groups_leave'] = array(
                'title'   => LANG_GROUPS_LEAVE,
                'options' => ['class' => 'user_delete', 'confirm' => LANG_GROUPS_LEAVE . '?', 'icon' => 'sign-out-alt'],
                'url'     => href_to('groups', $group['slug'], 'leave'),
            );
        }

        if ($group['access']['is_can_delete']){
            if ($group['is_approved']){
                $tool_buttons['groups_delete'] = array(
                    'title'   => LANG_GROUPS_DELETE,
                    'options' => ['class' => 'delete ajax-modal', 'icon' => 'minus-circle'],
                    'url'     => href_to('groups', $group['slug'], 'delete')
                );
            } else {
                $tool_buttons['groups_delete'] = array(
                    'title'   => LANG_GROUPS_REFUSE,
                    'options' => ['class' => 'delete ajax-modal', 'icon' => 'minus-square'],
                    'url'     => href_to('groups', $group['slug'], 'delete')
                );
            }
        }

        if ($group['content_counts'] && $group['access']['is_member']){
            foreach($group['content_counts'] as $ctype_name => $count){
                if (!$count['is_in_list']) { continue; }
                if (!$this->isContentAddAllowed($ctype_name, $group)) { continue; }
                $tool_buttons['groups_add_'.$ctype_name] = array(
                    'options' => ['class' => 'add', 'icon' => 'plus-circle'],
                    'title'   => sprintf(LANG_CONTENT_ADD_ITEM, $count['title_add']),
                    'url'     => href_to($ctype_name, 'add') . "?group_id={$group['id']}"
                );
            }
        }

        $buttons_hook = cmsEventsManager::hook('group_view_buttons', array(
            'profile' => $group,
            'buttons' => $tool_buttons
        ));

        return $buttons_hook['buttons'];

    }

    public function isContentAddAllowed($ctype_name, $group) {

        // пока группа не промодерирована, контент нельзя добавлять никому
        if(!$group['is_approved']){ return false; }

        if($this->cms_user->is_admin || ($group['access']['is_owner'] && cmsUser::isAllowed('groups', 'content_access'))){ return true; }

        // всем, кому разрешено общими правами
        if(empty($group['content_policy'][$ctype_name])){
            return cmsUser::isAllowed($ctype_name, 'add');
        }

        // только администраторам
        if($group['content_policy'][$ctype_name] == self::CTYPE_POLICY_STAFF){
            return $group['access']['member_role'] == self::ROLE_STAFF;
        }

        // только владельцам группы
        if($group['content_policy'][$ctype_name] == self::CTYPE_POLICY_OWNER){
            return $group['access']['is_owner'];
        }

        // только заданным группам пользователей
        if($group['content_policy'][$ctype_name] == self::CTYPE_POLICY_GROUPS){
            if(empty($group['content_groups'][$ctype_name])){
                return cmsUser::isAllowed($ctype_name, 'add');
            }
            return $this->cms_user->isInGroups($group['content_groups'][$ctype_name]);
        }

        // только заданным ролям
        if($group['content_policy'][$ctype_name] == self::CTYPE_POLICY_ROLES){
            if(empty($group['content_roles'][$ctype_name])){
                return cmsUser::isAllowed($ctype_name, 'add');
            }

            $roles = $this->model->getUserRoles($group['id'], $this->cms_user->id);

            return $roles && $this->cms_user->isUserInGroups($roles, $group['content_roles'][$ctype_name]);

        }

    }

    public function sendInvite($invited_users_list, $group_id){

        $group = $this->model->getGroup($group_id);

        if (!$group){ cmsCore::error404(); }

        if (!is_array($invited_users_list)) { $invited_users_list = array($invited_users_list); }

        $messenger = cmsCore::getController('messages');

        foreach($invited_users_list as $invited_id){

            $messenger->clearRecipients()->addRecipient($invited_id);

            //
            // Личное сообщение
            //
            $sender_link = '<a href="'.href_to_profile($this->cms_user).'">'.$this->cms_user->nickname.'</a>';
            $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';

            $notice = array(
                'content' => sprintf(LANG_GROUPS_INVITE_NOTICE, $sender_link, $group_link),
                'options' => array(
                    'is_closeable' => true
                ),
                'actions' => array(
                    'accept' => array(
                        'title' => LANG_ACCEPT,
                        'href'  => href_to('groups', $group['slug'], 'join')
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
                'user_url'      => href_to_profile($this->cms_user, false, true),
                'group_title'   => $group['title'],
                'group_url'     => href_to_abs('groups', $group['id'])
            ));

            $this->model->addInvite(array(
                'group_id'   => $group['id'],
                'user_id'    => $this->cms_user->id,
                'invited_id' => $invited_id
            ));

        }

        if ($this->request->isAjax()){

            return $this->cms_template->renderJSON(array(
                'errors'   => false,
                'text'     => LANG_GROUPS_INVITE_SENT,
                'callback' => 'inviteFormSuccess'
            ));

        } else {

            cmsUser::addSessionMessage(LANG_GROUPS_INVITE_SENT, 'success');

            $this->redirectBack();

        }

    }

    public function validate_slug($value){
        if (empty($value)) { return true; }
        if (!is_string($value)){ return ERR_VALIDATE_GROUP_SLUG; }
        if (is_numeric($value)){ return ERR_VALIDATE_GROUP_SLUG_NUM; }
        if (!preg_match("/^([a-z0-9\-]*)$/", $value)){ return ERR_VALIDATE_GROUP_SLUG; }
        if ($this->isActionExists($value)){ return ERR_VALIDATE_UNIQUE; }
        return true;
    }

}
