<?php
/**
 * @property \moderation $controller_moderation
 */
class actionContentItemView extends cmsAction {

    use icms\traits\services\fieldsParseable;

    private $viewed_moderators = false;

    public function run() {

        list($ctype, $item) = $this->getItemAndCtype();

        $props = $props_values = false;
        $props_fieldsets = $props_fields = false;

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

                return $this->redirectToLogin();
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

        // формируем связи (дочерние списки)
        $childs = [
            'relations' => $this->model->getContentTypeChilds($ctype['id']),
            'to_add'    => [],
            'to_bind'   => [],
            'to_unbind' => [],
            'fields'    => [],
            'tabs'      => [],
            'lists'     => []
        ];

        if ($childs['relations']) {

            foreach ($childs['relations'] as $relation) {

                // пропускаем все не контентные связи
                // их должен обработать хук content_before_childs
                if ($relation['target_controller'] !== 'content') {
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

                $filter = "r.parent_ctype_id = {$ctype['id']} AND " .
                        "r.parent_item_id = {$item['id']} AND " .
                        "r.child_ctype_id = {$relation['child_ctype_id']} AND " .
                        "r.child_item_id = i.id";

                $this->model->joinInner('content_relations_bind', 'r', $filter);

                // применяем приватность
                $this->model->applyPrivacyFilter($child_ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

                $count = $this->model->getContentItemsCount($relation['child_ctype_name']);

                if (!$count && $relation['options']['is_hide_empty']) {

                    $this->model->resetFilters();

                    continue;
                }

                if ($relation['layout'] === 'tab') {

                    $childs['tabs'][$relation['child_ctype_name']] = [
                        'title'       => $relation['title'],
                        'url'         => href_to($ctype['name'], $item['slug'] . '/view-' . $relation['child_ctype_name']),
                        'counter'     => $count,
                        'relation_id' => $relation['id'],
                        'ordering'    => $relation['ordering']
                    ];
                }

                if (!$this->request->has('child_ctype_name') && in_array($relation['layout'], ['list', 'field'])) {

                    if (!empty($relation['options']['limit'])) {
                        $child_ctype['options']['limit'] = $relation['options']['limit'];
                    }

                    if (!empty($relation['options']['is_hide_filter'])) {
                        $child_ctype['options']['list_show_filter'] = false;
                    }

                    if (!empty($relation['options']['dataset_id'])) {

                        $dataset = cmsCore::getModel('content')->getContentDataset($relation['options']['dataset_id']);

                        if ($dataset) {
                            $this->model->applyDatasetFilters($dataset);
                        }
                    }

                    $list_item = [
                        'title'       => empty($relation['options']['is_hide_title']) ? $relation['title'] : false,
                        'ctype_name'  => $relation['child_ctype_name'],
                        'html'        => $this->setListContext('form_field')->
                                renderItemsList($child_ctype, href_to($ctype['name'], $item['slug'] . '.html')),
                        'relation_id' => $relation['id'],
                        'ordering'    => $relation['ordering']
                    ];

                    if ($relation['layout'] === 'list') {
                        $childs['lists'][] = $list_item;
                    }

                    if ($relation['layout'] === 'field') {
                        $item['rel_' . $relation['child_ctype_name'] . '_id'] = $list_item;
                    }
                }

                $this->model->resetFilters();
            }

            list($ctype, $childs, $item) = cmsEventsManager::hook('content_before_childs', [$ctype, $childs, $item]);

            array_order_by($childs['tabs'], 'ordering');
            array_order_by($childs['lists'], 'ordering');
        }

        // Получаем поля для данного типа контента
        // И парсим их, получая HTML полей
        $fields = $this->parseContentFields(
                $this->model->resetFilters()->getContentFields($ctype['name']),
                $item
            );

        // показываем вкладку связи, если передана
        if ($this->request->has('child_ctype_name')){

            $child_ctype_name = $this->request->get('child_ctype_name', '');

            $parts = explode('-', $child_ctype_name);
            $child_controller_name = $parts[0];
            $child_target = $parts[1] ?? '';

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

        // Строим поля, которые выведем в шаблоне
        $item['fields'] = $this->getViewableItemFields($fields, $item, 'user_id', function($field, $item) {
            return $field['name'] !== 'title' && empty($field['is_system']);
        });

        // Применяем хуки полей к записи
        $item = $this->applyFieldHooksToItem($item['fields'], $item);

        // Группируем
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

                    if(!empty($base_url) && empty($ctype['options']['item_off_breadcrumb_ctype'])){
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

        // добавляем заголовок Last-Modified
        $this->cms_core->response->setLastModified($item['date_last_modified']);

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

        $is_owner = $item['user_id'] == $this->cms_user->id;

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

        if (!$item['is_approved'] && !$item['is_draft'] && !$this->viewed_moderators && $is_owner){
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

            if ($is_owner && cmsUser::isAllowed($ctype['name'], 'change_owner')) {
                $tool_buttons['change_owner'] = [
                    'title'   => sprintf(LANG_CONTENT_OWNER_ITEM, $ctype['labels']['create']),
                    'options' => ['class' => 'change_owner ajax-modal', 'icon' => 'people-arrows'],
                    'url'     => href_to($ctype['name'], 'owner', $item['id'])
                ];
            }

            $allow_edit = cmsUser::isAllowed($ctype['name'], 'edit', 'all') || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_all');
            if($is_owner && !$allow_edit){
                $allow_edit = cmsUser::isAllowed($ctype['name'], 'edit', 'own') || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_own');
            }

            if ($allow_edit) {
                if (!cmsUser::isPermittedLimitReached($ctype['name'], 'edit_times', ((time() - strtotime($item['date_pub'])) / 60))) {
                    $tool_buttons['edit'] = [
                        'title'   => sprintf(LANG_CONTENT_EDIT_ITEM, $ctype['labels']['create']),
                        'options' => ['class' => 'edit', 'icon' => 'pencil-alt'],
                        'url'     => href_to($ctype['name'], 'edit', $item['id'])
                    ];
                }
            }

            $allow_delete = (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $is_owner));
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
            (cmsUser::isAllowed($ctype['name'], 'move_to_trash', 'own') && $is_owner)){

                $tool_buttons['basket_put'] = [
                    'title'   => ($allow_delete ? LANG_BASKET_DELETE : sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create'])),
                    'options' => ['class' => 'basket_put', 'icon' => 'trash-alt', 'confirm' => sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])],
                    'url'     => href_to($ctype['name'], 'trash_put', $item['id'])
                ];

            }

        }

        if ($item['is_approved'] && $item['is_deleted']){

            if (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
            (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $is_owner)){

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

        if (!$_ctype_name) {
            return cmsCore::error404();
        }

        $ctype_names = is_array($_ctype_name) ? $_ctype_name : [$_ctype_name];

        // есть типы контента по умолчанию
        if ($this->cms_config->ctype_default) {
            $ctype_names = array_unique(array_merge($ctype_names, $this->cms_config->ctype_default));
        }

        // переопределение названий типов контента
        $mapping = cmsConfig::getControllersMapping();

        $first_ctype_name = $ctype_names[0];

        foreach ($ctype_names as $ctype_name) {

            $ctype = $this->model->getContentTypeByName($ctype_name);
            // типы контента тут должны быть известные
            if (!$ctype) {
                return cmsCore::error404();
            }

            $this->model->joinModerationsTasks($ctype['name']);

            list($ctype, $this->model) = cmsEventsManager::hook(['content_item_filter', "content_{$ctype['name']}_item_filter"], [$ctype, $this->model]);

            $has_out_ctype_default = !$this->cms_config->ctype_default || !in_array($ctype['name'], $this->cms_config->ctype_default);

            // Получаем запись
            $item = $this->model->getContentItemBySLUG($ctype['name'], $slug);
            if (!$item) {

                // если тип контента не входит в список умолчаний, сразу 404
                if ($has_out_ctype_default) {
                    return cmsCore::error404();
                }

                continue;
            }

            // редиректы на новые урлы
            if ($this->cms_config->ctype_default &&
                    empty($this->cms_core->no_uri_change_redirect) &&
                    in_array($this->cms_core->uri_action, $this->cms_config->ctype_default)) {

                return $this->redirect(href_to($item['slug'] . '.html'), 301);

            } elseif ($has_out_ctype_default) {

                // если название переопределено, то редиректим со оригинального на переопределенный
                if ($mapping) {
                    foreach ($mapping as $name => $alias) {
                        if ($name === $ctype['name'] && !$this->cms_core->uri_controller_before_remap) {
                            return $this->redirect(href_to($alias . '/' . $item['slug'] . '.html'), 301);
                        }
                    }
                }
            }

            // должно быть точное совпадение
            if ($slug !== $item['slug']) {
                return $this->redirect(href_to($ctype['name'], $item['slug'] . '.html'), 301);
            }

            if (!$ctype['options']['item_on']) {
                return cmsCore::error404();
            }

            // если тип контента сменился
            if ($ctype['name'] !== $first_ctype_name) {

                // новый uri
                $this->cms_core->uri              = preg_replace("#^{$first_ctype_name}/#", $ctype['name'] . '/', $this->cms_core->uri);
                $this->cms_core->uri_before_remap = preg_replace("#^{$first_ctype_name}/#", $ctype['name'] . '/', $this->cms_core->uri_before_remap);

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

        $seo_desc  = $seo_keys  = '';
        $seo_title = $item['title'];

        $meta_item = $this->prepareItemSeo($item, $fields, $ctype);

        $this->cms_template->
                setPageKeywordsItem($meta_item)->
                setPageDescriptionItem($meta_item)->
                setPageTitleItem($meta_item);

        if (!empty($ctype['options']['seo_title_pattern'])) {
            $seo_title = $ctype['options']['seo_title_pattern'];
        }

        if (!empty($ctype['options']['seo_keys_pattern'])) {
            $seo_keys = $ctype['options']['seo_keys_pattern'];
        }

        if (!empty($ctype['options']['seo_desc_pattern'])) {
            $seo_desc = $ctype['options']['seo_desc_pattern'];
        }

        // приоритет за заданными в записи
        if (!empty($item['seo_title'])) {
            $seo_title = $item['seo_title'];
        }
        if (!empty($item['seo_keys'])) {
            $seo_keys = $item['seo_keys'];
        }
        if (!empty($item['seo_desc'])) {
            $seo_desc = $item['seo_desc'];
        }

        $this->cms_template->setPageTitle($seo_title);
        $this->cms_template->setPageKeywords($seo_keys);
        $this->cms_template->setPageDescription($seo_desc);

        return [
            'meta_item' => $meta_item,
            'title_str' => $seo_title,
            'keys_str'  => $seo_keys,
            'desc_str'  => $seo_desc
        ];
    }

}
