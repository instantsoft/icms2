<?php

class actionContentItemView extends cmsAction {

    private $viewed_moderators = false;

    public function run(){

        list($ctype, $item) = $this->getItemAndCtype();

        $props = $props_values = false;
        $props_fieldsets = $props_fields = false;

        // добавляем Last-Modified
        if(!$this->cms_user->is_logged){
            cmsCore::respondIfModifiedSince($item['date_last_modified']);
        }

        // Проверяем прохождение модерации
        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $item);

        // на модерации или в черновиках
        if (!$item['is_approved']){

            $item_view_notice = $item['is_draft'] ? LANG_CONTENT_DRAFT_NOTICE : LANG_MODERATION_NOTICE;

            if (!$is_moderator && $this->cms_user->id != $item['user_id']){

                return cmsCore::errorForbidden($item_view_notice, true);

            }

            // если запись на модерации и смотрим автором, проверяем кем просмотрена запись уже
            if (!$item['is_draft'] && ($this->cms_user->id == $item['user_id'] || $is_moderator)){

                // ставим флаг, что модератор уже смотрит, после этого изъять из модерации нельзя
                if ($is_moderator){

                    cmsUser::setUPS($this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]), time());

                    $item_view_notice = LANG_MODERATION_NOTICE_MODER;

                }

                $this->viewed_moderators = cmsUser::getSetUPS($this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]));

                if(isset($this->viewed_moderators[$this->cms_user->id])){ unset($this->viewed_moderators[$this->cms_user->id]); }

                if($this->viewed_moderators){

                    $viewed_moderators = $this->model_users->filterIn('id', array_keys($this->viewed_moderators))->getUsers();

                    $moderator_links = array();

                    foreach ($viewed_moderators as $viewed_moderator) {
                        $moderator_links[] = '<a href="'.href_to_profile($viewed_moderator).'">'.$viewed_moderator['nickname'].'</a>';
                    }

                    $item_view_notice .= sprintf(
                        LANG_MODERATION_NOTICE_VIEW,
                        (count($moderator_links) > 1 ? LANG_MODERATORS : LANG_MODERATOR),
                        implode(', ', $moderator_links),
                        (count($moderator_links) > 1 ? LANG_MODERATION_VIEWS : LANG_MODERATION_VIEW),
                        (count($moderator_links) == 1 ? ' '.mb_strtolower(string_date_format($this->viewed_moderators[$viewed_moderator['id']], true)) : '')
                    );

                }

            }

            cmsUser::addSessionMessage($item_view_notice, 'info');

        }

        // общие права доступа на просмотр
        if(!$is_moderator && $this->cms_user->id != $item['user_id']){

            if(!$this->checkListPerm($ctype['name'])){
                return cmsCore::errorForbidden();
            }

        }

        // Проверяем публикацию
        if ($item['is_pub'] < 1) {
            if (!$is_moderator && $this->cms_user->id != $item['user_id']) {
                return cmsCore::error404();
            }
        }

        // Проверяем, что не удалено
        if ($item['is_deleted']){

            $allow_restore = (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $item['user_id'] == $this->cms_user->id));

            if (!$is_moderator && !$allow_restore){ return cmsCore::error404(); }

            cmsUser::addSessionMessage(LANG_CONTENT_ITEM_IN_TRASH, 'info');
        }

        // Проверяем ограничения доступа из других контроллеров
        if ($item['is_parent_hidden'] || $item['is_private']){

            $is_parent_viewable_result = cmsEventsManager::hook('content_view_hidden', [
                'viewable'     => true,
                'item'         => $item,
                'is_moderator' => $is_moderator,
                'ctype'        => $ctype
            ]);

            if (!$is_parent_viewable_result['viewable']){

                if(isset($is_parent_viewable_result['access_text'])){

                    cmsUser::addSessionMessage($is_parent_viewable_result['access_text'], 'error');

                    if(isset($is_parent_viewable_result['access_redirect_url'])){
                        $this->redirect($is_parent_viewable_result['access_redirect_url']);
                    } else {
                        $this->redirect(href_to($ctype['name']));
                    }

                }

                cmsUser::goLogin();
            }
        }

        $item['ctype_name'] = $ctype['name'];
        $item['ctype'] = $ctype;

        if ($ctype['is_cats'] && $item['category_id'] > 1){

            $item['category'] = $this->model->getCategory($ctype['name'], $item['category_id']);

            if(!empty($ctype['options']['is_cats_multi'])){
                $item['categories'] = $this->model->getContentItemCategoriesList($ctype['name'], $item['id']);
            }

        }

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        // Запоминаем копию записи для заполнения отпарсенных полей
        $item_parsed = $item;

        // Парсим значения полей
        foreach ($fields as $name => $field) {
            $fields[$name]['html'] = $field['handler']->setItem($item)->parse(isset($item[$name]) ? $item[$name] : '');
            $item_parsed[$name] = $fields[$name]['html'];
        }

        // Для каких необходимо, обрабатываем дополнительно
        foreach ($fields as $name => $field) {
            $fields[$name]['string_value'] = $field['handler']->setItem($item_parsed)->getStringValue(isset($item[$name]) ? $item[$name] : '');
            $fields[$name]['html'] = $field['handler']->afterParse($fields[$name]['html'], $item_parsed);
        }

        // формируем связи (дочерние списки)
        $childs = [
            'relations' => $this->model->getContentTypeChilds($ctype['id']),
            'to_add'    => [],
            'to_bind'   => [],
            'to_unbind' => [],
            'tabs'      => [],
            'items'     => []
        ];

        if ($childs['relations']){

            foreach($childs['relations'] as $relation){

                // пропускаем все не контентные связи
                // их должен обработать хук content_before_childs
                if($relation['target_controller'] !== 'content'){
                    continue;
                }

                $perm = cmsUser::getPermissionValue($relation['child_ctype_name'], 'add_to_parent');
                $is_allowed_to_add = ($perm &&
                        ($perm === 'to_all' ||
                        ($perm === 'to_own' && $item['user_id'] == $this->cms_user->id) ||
                        ($perm === 'to_other' && $item['user_id'] != $this->cms_user->id)
                )) || $this->cms_user->is_admin;

                $perm = cmsUser::getPermissionValue($relation['child_ctype_name'], 'bind_to_parent');
                $is_allowed_to_bind = ($perm && (
                    ($perm === 'all_to_all' || $perm === 'own_to_all' || $perm === 'other_to_all') ||
                    (($perm === 'all_to_own' || $perm === 'own_to_own' || $perm === 'other_to_own') && ($item['user_id'] == $this->cms_user->id)) ||
                    (($perm === 'all_to_other' || $perm === 'own_to_other' || $perm === 'other_to_other') && ($item['user_id'] != $this->cms_user->id))
                )) || $this->cms_user->is_admin;

                $is_allowed_to_unbind = cmsUser::isAllowed($relation['child_ctype_name'], 'bind_off_parent');

                if ($is_allowed_to_add && $item['is_approved']) {
                    $childs['to_add'][] = $relation;
                }
                if ($is_allowed_to_bind && $item['is_approved']) {
                    $childs['to_bind'][] = $relation;
                }
                if ($is_allowed_to_unbind && $item['is_approved']) {
                    $childs['to_unbind'][] = $relation;
                }

                $child_ctype = $this->model->getContentTypeByName($relation['child_ctype_name']);

                $filter =   "r.parent_ctype_id = {$ctype['id']} AND ".
                            "r.parent_item_id = {$item['id']} AND ".
                            "r.child_ctype_id = {$relation['child_ctype_id']} AND ".
                            "r.child_item_id = i.id";

                $this->model->joinInner('content_relations_bind', 'r', $filter);

                // применяем приватность
                $this->model->applyPrivacyFilter($child_ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

                $count = $this->model->getContentItemsCount($relation['child_ctype_name']);

                $is_hide_empty = $relation['options']['is_hide_empty'];

                if (($count || !$is_hide_empty) && $relation['layout'] === 'tab'){

                    $childs['tabs'][$relation['child_ctype_name']] = [
                        'title'       => $relation['title'],
                        'url'         => href_to($ctype['name'], $item['slug'].'/view-'.$relation['child_ctype_name']),
                        'counter'     => $count,
                        'relation_id' => $relation['id'],
                        'ordering'    => $relation['ordering']
                    ];
                }

                if (!$this->request->has('child_ctype_name') && ($count || !$is_hide_empty) && $relation['layout'] === 'list'){

                    if (!empty($relation['options']['limit'])){
                        $child_ctype['options']['limit'] = $relation['options']['limit'];
                    }

                    if (!empty($relation['options']['is_hide_filter'])){
                        $child_ctype['options']['list_show_filter'] = false;
                    }

                    if (!empty($relation['options']['dataset_id'])){

                        $dataset = cmsCore::getModel('content')->getContentDataset($relation['options']['dataset_id']);

                        if ($dataset){
                            $this->model->applyDatasetFilters($dataset);
                        }
                    }

                    $childs['lists'][] = [
                        'title'       => empty($relation['options']['is_hide_title']) ? $relation['title'] : false,
                        'ctype_name'  => $relation['child_ctype_name'],
                        'html'        => $this->setListContext('item_view_relation_list')->
                            renderItemsList($child_ctype, href_to($ctype['name'], $item['slug'] . '.html')),
                        'relation_id' => $relation['id'],
                        'ordering'    => $relation['ordering']
                    ];
                }

                $this->model->resetFilters();
            }

            list($ctype, $childs, $item) = cmsEventsManager::hook('content_before_childs', [$ctype, $childs, $item]);
        }

        array_order_by($childs['tabs'], 'ordering');
        array_order_by($childs['lists'], 'ordering');

        // показываем вкладку связи, если передана
        if ($this->request->has('child_ctype_name')){

            $child_ctype_name = $this->request->get('child_ctype_name', '');

            $parts = explode('-', $child_ctype_name);
            $child_controller_name = $parts[0];
            $child_target = isset($parts[1]) ? $parts[1] : '';

            // если связь с контроллером, а не с типом контента
            if ($this->isControllerInstalled($child_controller_name) && $this->isControllerEnabled($child_controller_name)){

                $child_controller = cmsCore::getController($child_controller_name, $this->request);

                if($child_controller->isActionExists('item_childs_view')){

                    // разблокируем вызов
                    $child_controller->lock_explicit_call = false;

                    return $child_controller->runAction('item_childs_view', [
                        'ctype'              => $ctype,
                        'item'               => $item,
                        'childs'             => $childs,
                        'content_controller' => $this,
                        'fields'             => $fields,
                        'child_target'       => $child_target
                    ]);
                }
            }

            // разблокируем вызов
            $this->lock_explicit_call = false;

            return $this->runExternalAction('item_childs_view', [
                'ctype'            => $ctype,
                'item'             => $item,
                'child_ctype_name' => $child_ctype_name,
                'childs'           => $childs,
                'fields'           => $fields
            ]);
        }

        // Получаем поля-свойства
        if ($ctype['is_cats'] && $item['category_id'] > 1){

            $prop_cats = [$item['category_id']];
            if(!empty($item['categories'])){
                $prop_cats = array_keys($item['categories']);
            }

            $props = $this->model->getContentProps($ctype['name'], $prop_cats);

            $props_values = array_filter((array)$this->model->getPropsValues($ctype['name'], $item['id']));

            if ($props && $props_values) {

                $props_fields = $this->getPropsFields($props);

                foreach ($props as $key => $prop) {

                    if (!isset($props_values[$prop['id']])) {
                        continue;
                    }

                    $prop_field = $props_fields[$prop['id']];

                    $props[$key]['html'] = $prop_field->setItem($item)->parse($props_values[$prop['id']]);
                }

                $props_fieldsets = cmsForm::mapFieldsToFieldsets($props, function($field, $user) use($props_values) {
                    if (!isset($props_values[$field['id']])) {
                        return false;
                    }
                    return true;
                });
            }

        }

        // Информация о модераторе для админа и владельца записи
        if ($item['approved_by'] && ($this->cms_user->is_admin || $this->cms_user->id == $item['user_id'])){
            $item['approved_by'] = cmsCore::getModel('users')->getUser($item['approved_by']);
        }

        // формируем инфобар
        $item['info_bar'] = $this->getItemInfoBar($ctype, $item, $fields);

        list($ctype, $item, $fields) = cmsEventsManager::hook('content_before_item', [$ctype, $item, $fields]);
        list($ctype, $item, $fields) = cmsEventsManager::hook("content_{$ctype['name']}_before_item", [$ctype, $item, $fields]);

        // счетчик просмотров увеличивается, если включен в настройках,
        // не запрещён в записи (флаг disable_increment_hits, который может быть определён ранее в хуках)
        // и если смотрит не автор
		if (!empty($ctype['options']['hits_on']) && empty($item['disable_increment_hits']) && $this->cms_user->id != $item['user_id']){
			$this->model->incrementHitsCounter($ctype['name'], $item['id']);
		}

        // строим поля записи
        $item['fields'] = [];

        foreach($fields as $field){

            if (!$field['is_in_item'] || $field['is_system'] || $field['name'] === 'title') {
                continue;
            }

            // Позиция поля "На позиции в специальном виджете"
            if (!empty($field['options']['is_in_item_pos']) && !in_array('page', $field['options']['is_in_item_pos'])) {
                continue;
            }

            if (is_empty_value($field['html'])) {
                continue;
            }

            // проверяем что группа пользователя имеет доступ к чтению этого поля
            if ($field['groups_read'] && !$this->cms_user->isInGroups($field['groups_read'])) {
                // если группа пользователя не имеет доступ к чтению этого поля,
                // проверяем на доступ к нему для авторов
                if (empty($item['user_id']) || empty($field['options']['author_access'])){ continue; }
                if (!in_array('is_read', $field['options']['author_access'])){ continue; }
                if ($item['user_id'] != $this->cms_user->id){ continue; }
            }

            $item['fields'][$field['name']] = $field;
        }

        foreach($item['fields'] as $name => $field){
            $item = $fields[$name]['handler']->hookItem($item, $item['fields']);
        }

        $fields_fieldsets = cmsForm::mapFieldsToFieldsets($item['fields']);

        // кешируем запись для получения ее в виджетах
        cmsModel::cacheResult('current_ctype', $ctype);
        cmsModel::cacheResult('current_ctype_item', $item);
        cmsModel::cacheResult('current_ctype_fields', $fields);
        cmsModel::cacheResult('current_ctype_fields_fieldsets', $fields_fieldsets);
        cmsModel::cacheResult('current_ctype_props', $props);
        cmsModel::cacheResult('current_ctype_props_fields', $props_fields);
        cmsModel::cacheResult('current_ctype_props_props_fieldsets', $props_fieldsets);

        // После кэширования, если инфобар отключен для вывода на странице, убираем его
        // Например, он может быть выведен в виджете "Поля контента"
        if(!empty($ctype['options']['disable_info_block'])){
            $item['info_bar'] = [];
        }

        // SEO параметры
        $item_seo = $this->applyItemSeo($ctype, $item, $fields);

        // глубиномер
        if (empty($ctype['options']['item_off_breadcrumb']) && empty($item['off_breadcrumb'])){

            if ($item['parent_id'] && !empty($ctype['is_in_groups'])){

                $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
                $this->cms_template->addBreadcrumb($item['parent_title'], rel_to_href(str_replace('/content/'.$ctype['name'], '', $item['parent_url'])));
                if ($ctype['options']['list_on']){
                    $this->cms_template->addBreadcrumb((empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']), rel_to_href($item['parent_url']));
                }

            } else {

                if ($ctype['options']['list_on']){

                    $base_url = ($this->cms_config->ctype_default && in_array($ctype['name'], $this->cms_config->ctype_default)) ? '' : $ctype['name'];

                    $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];

                    if(!empty($base_url)){
                        $this->cms_template->addBreadcrumb($list_header, href_to($ctype['name']));
                    }

                    if (isset($item['category'])){

                        if(!empty($item['category']['path'])){
                            foreach($item['category']['path'] as $c){
                                if(empty($c['is_hidden'])){
                                    $this->cms_template->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
                                }
                            }
                        }

                    }
                }

            }

            $this->cms_template->addBreadcrumb($item['title']);
        }

        $tool_buttons = $this->getToolButtons($ctype, $item, $is_moderator, $childs);

        if($tool_buttons){
            $this->cms_template->addMenuItems('toolbar', $tool_buttons);
        }

        if (!empty($childs['tabs'])){

            $this->cms_template->addMenuItem('item-menu', [
                'title' => !empty($ctype['labels']['relations_tab_title']) ? $ctype['labels']['relations_tab_title'] : string_ucfirst($ctype['labels']['one']),
                'url'   => href_to($ctype['name'], $item['slug'] . '.html')
            ]);

            $this->cms_template->addMenuItems('item-menu', $childs['tabs']);
        }

        $tpl_file = $this->cms_template->getTemplateFileName('controllers/content/item_view_' . $ctype['name'], true) ?
                'item_view_' . $ctype['name'] : 'item_view';

        return $this->cms_template->render($tpl_file, [
            'item_seo'         => $item_seo,
            'ctype'            => $ctype,
            'fields'           => $fields,
            'fields_fieldsets' => $fields_fieldsets,
            'props'            => $props,
            'props_values'     => $props_values,
            'props_fields'     => $props_fields,
            'props_fieldsets'  => $props_fieldsets,
            'item'             => $item,
            'is_moderator'     => $is_moderator,
            'user'             => $this->cms_user,
            'childs'           => $childs
        ]);
    }

    private function getToolButtons($ctype, $item, $is_moderator, $childs) {

        $tool_buttons = [];

        if (!$item['is_approved'] && !$item['is_draft'] && $is_moderator){
            $tool_buttons['accept'] = [
                'title'   => LANG_MODERATION_APPROVE,
                'options' => ['class' => 'accept', 'icon' => 'check-double'],
                'url'     => href_to($ctype['name'], 'approve', $item['id'])
            ];
            $tool_buttons['return_for_revision'] = [
                'title'   => LANG_MODERATION_RETURN_FOR_REVISION,
                'options' => ['class' => 'return_for_revision ajax-modal', 'icon' => 'retweet'],
                'url'     => href_to($ctype['name'], 'return_for_revision', $item['id'])
            ];
        }

        if (!$item['is_approved'] && !$item['is_draft'] && !$this->viewed_moderators && $item['user_id'] == $this->cms_user->id){
            $tool_buttons['return'] = [
                'title'   => LANG_MODERATION_RETURN,
                'options' => ['class' => 'return', 'confirm' => LANG_CONTENT_RETURN_CONFIRM, 'icon' => 'undo'],
                'url'     => href_to($ctype['name'], 'return', $item['id'])
            ];
        }

        if ($item['is_approved'] || $item['is_draft'] || $is_moderator){

            if (!empty($childs['to_add'])){
                foreach($childs['to_add'] as $relation){

                    $tool_buttons['add_'.$relation['child_ctype_name']] = [
                        'title'   => sprintf(LANG_CONTENT_ADD_ITEM, $relation['child_labels']['create']),
                        'options' => ['class' => 'add', 'icon' => 'plus-circle'],
                        'url'     => href_to($relation['child_ctype_name'], 'add') . "?parent_{$ctype['name']}_id={$item['id']}".($item['parent_type']=='group' ? '&group_id='.$item['parent_id'] : '')
                    ];

                }
            }

            if (!empty($childs['to_bind'])){
                foreach($childs['to_bind'] as $relation){

                    $tool_buttons['bind_'.$relation['child_ctype_name']] = [
                        'title'   => sprintf(LANG_CONTENT_BIND_ITEM, $relation['child_labels']['create']),
                        'options' => ['class' => 'newspaper_add ajax-modal', 'icon' => 'link'],
                        'url'     => href_to($ctype['name'], 'bind_form', [$relation['child_ctype_name'], $item['id']])
                    ];

                }
            }

            if (!empty($childs['to_unbind'])){
                foreach($childs['to_unbind'] as $relation){

                    $tool_buttons['unbind_'.$relation['child_ctype_name']] = [
                        'title'   => sprintf(LANG_CONTENT_UNBIND_ITEM, $relation['child_labels']['create']),
                        'options' => ['class' => 'newspaper_delete ajax-modal', 'icon' => 'unlink'],
                        'url'     => href_to($ctype['name'], 'bind_form', [$relation['child_ctype_name'], $item['id'], 'unbind'])
                    ];

                }
            }

            $allow_edit = cmsUser::isAllowed($ctype['name'], 'edit', 'all') || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_all');
            if($item['user_id'] == $this->cms_user->id && !$allow_edit){
                $allow_edit = cmsUser::isAllowed($ctype['name'], 'edit', 'own') || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_own');
            }

            if ($allow_edit){
                if(!cmsUser::isPermittedLimitReached($ctype['name'], 'edit_times', ((time() - strtotime($item['date_pub']))/60))){
                    $tool_buttons['edit'] = [
                        'title'   => sprintf(LANG_CONTENT_EDIT_ITEM, $ctype['labels']['create']),
                        'options' => ['class' => 'edit', 'icon' => 'pencil-alt'],
                        'url'     => href_to($ctype['name'], 'edit', $item['id'])
                    ];
                }
            }

            $allow_delete = (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $item['user_id'] == $this->cms_user->id));
            $delete_limit_reached = cmsUser::isPermittedLimitReached($ctype['name'], 'delete_times', ((time() - strtotime($item['date_pub']))/60));

            if ($allow_delete){
                if ($item['is_approved'] || $item['is_draft']){

                    $back_url = str_replace($this->cms_config->host, '', $this->getBackURL());
                    if($back_url == '/' ||
                            strpos($back_url, '/upload') !== false ||
                            strpos($back_url, '/add') !== false ||
                            strpos($back_url, '/edit/') !== false){
                        $back_url = '';
                    }

                    if(!$delete_limit_reached){
                        $tool_buttons['delete'] = [
                            'title'   => sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create']),
                            'options' => ['class' => 'delete', 'icon' => 'minus-circle', 'confirm' => sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])],
                            'url'     => href_to($ctype['name'], 'delete', $item['id']).'?csrf_token='.cmsForm::getCSRFToken().'&back='.$back_url
                        ];
                    }

                } else {

                    $tool_buttons['refuse'] = [
                        'title'   => sprintf(LANG_MODERATION_REFUSE, $ctype['labels']['create']),
                        'options' => ['class' => 'delete ajax-modal', 'icon' => 'minus-square'],
                        'url'     => href_to($ctype['name'], 'delete', $item['id']).'?csrf_token='.cmsForm::getCSRFToken()
                    ];

                }

            }

        }

        if ($item['is_approved'] && !$item['is_deleted'] && !$delete_limit_reached){

            if (cmsUser::isAllowed($ctype['name'], 'move_to_trash', 'all') ||
            (cmsUser::isAllowed($ctype['name'], 'move_to_trash', 'own') && $item['user_id'] == $this->cms_user->id)){

                $tool_buttons['basket_put'] = [
                    'title'   => ($allow_delete ? LANG_BASKET_DELETE : sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create'])),
                    'options' => ['class' => 'basket_put', 'icon' => 'trash-alt', 'confirm' => sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])],
                    'url'     => href_to($ctype['name'], 'trash_put', $item['id'])
                ];

            }

        }

        if ($item['is_approved'] && $item['is_deleted']){

            if (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
            (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $item['user_id'] == $this->cms_user->id)){

                $tool_buttons['basket_remove'] = [
                    'title'   => LANG_RESTORE,
                    'options' => ['class' => 'basket_remove', 'icon' => 'trash-restore'],
                    'url'     => href_to($ctype['name'], 'trash_remove', $item['id'])
                ];

            }

        }

        $buttons_hook = cmsEventsManager::hook('ctype_item_tool_buttons', [
            'params'  => [$ctype, $item, $is_moderator, $childs],
            'buttons' => $tool_buttons
        ]);

        $buttons_hook = cmsEventsManager::hook($ctype['name'].'_ctype_item_tool_buttons', [
            'params'  => [$ctype, $item, $is_moderator, $childs],
            'buttons' => $buttons_hook['buttons']
        ]);

        return $buttons_hook['buttons'];
    }

    private function getItemAndCtype() {

        $slug = $this->request->get('slug', '');

        $_ctype_name = $this->request->get('ctype_name');

        if(!$_ctype_name){ return cmsCore::error404(); }

        $ctype_names = is_array($_ctype_name) ? $_ctype_name : [$_ctype_name];

        // есть типы контента по умолчанию
        if ($this->cms_config->ctype_default){
            foreach ($this->cms_config->ctype_default as $ctype_default) {
                if(!in_array($ctype_default, $ctype_names)){
                    $ctype_names[] = $ctype_default;
                }
            }
        }

        // переопределение названий типов контента
        $mapping = cmsConfig::getControllersMapping();

        foreach ($ctype_names as $ctype_name) {

            $ctype = $this->model->getContentTypeByName($ctype_name);
            // типы контента тут должны быть известные
            if (!$ctype) { return cmsCore::error404(); }

            $this->model->joinModerationsTasks($ctype['name']);

            list($ctype, $this->model) = cmsEventsManager::hook(['content_item_filter', "content_{$ctype['name']}_item_filter"], array($ctype, $this->model));

            // Получаем запись
            $item = $this->model->getContentItemBySLUG($ctype['name'], $slug);
            if (!$item) {

                // если тип контента не входит в список умолчаний, сразу 404
                if(!$this->cms_config->ctype_default || !in_array($ctype['name'], $this->cms_config->ctype_default)){
                    return cmsCore::error404();
                }

                continue;
            }

            // редиректы на новые урлы
			if ($this->cms_config->ctype_default &&
                    empty($this->cms_core->no_uri_change_redirect) &&
                    in_array($this->cms_core->uri_action, $this->cms_config->ctype_default)){

				$this->redirect(href_to($item['slug'] . '.html'), 301);

			} elseif(!$this->cms_config->ctype_default || !in_array($ctype['name'], $this->cms_config->ctype_default)) {

                // если название переопределено, то редиректим со оригинального на переопределенный
                if($mapping){
                    foreach($mapping as $name => $alias){
                        if ($name == $ctype['name'] && !$this->cms_core->uri_controller_before_remap) {
                            $this->redirect(href_to($alias.'/'. $item['slug'].'.html'), 301);
                        }
                    }
                }

            }

            // должно быть точное совпадение
            if ($slug !== $item['slug']){
                $this->redirect(href_to($ctype['name'], $item['slug'] . '.html'), 301);
            }

            if (!$ctype['options']['item_on']) { return cmsCore::error404(); }

            // если тип контента сменился
            if($ctype['name'] !== $_ctype_name){

                // новый uri
                $this->cms_core->uri = preg_replace("#^{$_ctype_name}/#", $ctype['name'].'/', $this->cms_core->uri);
                $this->cms_core->uri_before_remap = preg_replace("#^{$_ctype_name}/#", $ctype['name'].'/', $this->cms_core->uri_before_remap);

                // обновляем страницы и маски
                $this->cms_core->setMatchedPages(null)->loadMatchedPages();

            }

            list(
                $slug,
                $ctype,
                $item
            ) = cmsEventsManager::hook(['content_item_by_slug', "content_{$ctype['name']}_item_by_slug"], [
                $slug,
                $ctype,
                $item
            ], null, $this->request);

            return [$ctype, $item];
        }

        // ничего не нашли
        return cmsCore::error404();
    }

    public function applyItemSeo($ctype, $item, $fields) {

        $seo_desc = $seo_keys = ''; $seo_title = $item['title'];

        $meta_item = $this->prepareItemSeo($item, $fields, $ctype);

        if(!empty($ctype['options']['seo_title_pattern'])){

            $seo_title = $ctype['options']['seo_title_pattern'];

            $this->cms_template->setPageTitleItem($meta_item);
        }

        if(!empty($ctype['options']['seo_keys_pattern'])){

            $seo_keys = $ctype['options']['seo_keys_pattern'];

            $this->cms_template->setPageKeywordsItem($meta_item);
        }

        if(!empty($ctype['options']['seo_desc_pattern'])){

            $seo_desc = $ctype['options']['seo_desc_pattern'];

            $this->cms_template->setPageDescriptionItem($meta_item);
        }

        // приоритет за заданными в записи
        if (!empty($item['seo_title'])){ $seo_title = $item['seo_title']; }
        if (!empty($item['seo_keys'])){ $seo_keys = $item['seo_keys']; }
        if (!empty($item['seo_desc'])){ $seo_desc = $item['seo_desc']; }

        $this->cms_template->setPageTitle($seo_title);
        $this->cms_template->setPageKeywords($seo_keys);
        $this->cms_template->setPageDescription($seo_desc);

        return array(
            'meta_item' => $meta_item,
            'title_str' => $seo_title,
            'keys_str'  => $seo_keys,
            'desc_str'  => $seo_desc
        );

    }

}
