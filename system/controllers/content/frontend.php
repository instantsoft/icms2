<?php
/**
 * @property \modelContent $model
 */
class content extends cmsFrontend {

    const perpage = 15;

    public $max_items_count = 0;
    public $request_page_name = 'page';

    public $list_filter = false;

    private $check_list_perm = true;

    private $filter_titles = [];

//============================================================================//
//============================================================================//

    public function route($uri) {

        $action_name = $this->parseRoute($this->cms_core->uri);

        if (!$action_name) {
            return cmsCore::error404();
        }

        $this->runAction($action_name);
    }

    public function parseRoute($uri) {

        $action_name = parent::parseRoute($uri);

        if (!$action_name && $this->cms_config->ctype_default) {
            $action_name = parent::parseRoute($this->cms_config->ctype_default[0] . '/' . $uri);
        }

        return $action_name;
    }

//============================================================================//
//============================================================================//

    public function getMenuAddItems($menu_item_id, $full_string = false) {

        $result = ['url' => '#', 'items' => []];

        $ctypes = $this->model->getContentTypes();
        if (!$ctypes) { return $result; }

        foreach ($ctypes as $ctype) {

            if (!cmsUser::isAllowed($ctype['name'], 'add')) {
                continue;
            }

            if (!empty($ctype['labels']['create'])) {
                $result['items'][] = [
                    'id'           => 'content_add' . $ctype['id'],
                    'parent_id'    => $menu_item_id,
                    'title'        => $full_string ? sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']) : string_ucfirst($ctype['labels']['create']),
                    'childs_count' => 0,
                    'options'      => ['icon' => 'plus-circle'],
                    'url'          => href_to($ctype['name'], 'add')
                ];
            }
        }

        return $result;
    }

    public function getMenuPrivateItems($menu_item_id) {

        $result = ['url' => '#', 'items' => []];

        $ctypes = $this->model->getContentTypes();
        if (!$ctypes) { return false; }

        foreach ($ctypes as $ctype) {

            if (!$ctype['options']['list_on']) {
                continue;
            }

            $result['items'][] = [
                'id'           => 'private_list' . $ctype['id'],
                'parent_id'    => $menu_item_id,
                'title'        => sprintf(LANG_CONTENT_PRIVATE_FRIEND_ITEMS, mb_strtolower($ctype['title'])),
                'childs_count' => 0,
                'url'          => href_to($ctype['name'], 'from_friends')
            ];
        }

        return $result;
    }

    public function getMenuCategoriesItems($menu_item_id, $ctype){

        $result = ['url' => href_to($ctype['name']), 'items' => []];

        if (!$ctype['is_cats']) { return $result; }

        $this->model->filterIsNull('is_hidden');

        $tree = $this->model->getCategoriesTree($ctype['name'], false);
        if (!$tree) { return $result; }

        $base_url = ($this->cms_config->ctype_default && in_array($ctype['name'], $this->cms_config->ctype_default)) ? '' : $ctype['name'];

        // считаем вручную кол-во вложенных
        // т.к. у нас могут быть скрытые категории
        // не используем ($cat['ns_right'] - $cat['ns_left']) - 1
        $childs_count = []; $result['items'] = [];

        foreach($tree as $cat){

            $item_id   = 'content.'.$ctype['name'].'.'.$cat['id'].'.'.$menu_item_id;
            $parent_id = 'content.'.$ctype['name'].'.'.$cat['parent_id'].'.'.$menu_item_id;

            if($cat['parent_id'] > 1){
                if(!isset($childs_count[$cat['parent_id']])){
                    $childs_count[$cat['parent_id']] = 1;
                } else {
                    $childs_count[$cat['parent_id']] += 1;
                }
            }

            $result['items'][$cat['id']] = [
                'id'           => $item_id,
                'parent_id'    => ($cat['parent_id'] == 1 ? $menu_item_id : $parent_id),
                'title'        => $cat['title'],
                'childs_count' => 0,
                'url'          => href_to($base_url, $cat['slug'])
            ];

        }

        if($childs_count){
            foreach ($childs_count as $id => $count) {
                if(isset($result['items'][$id])){
                    $result['items'][$id]['childs_count'] = $count;
                }
            }
        }

        return $result;
    }

//============================================================================//
//============================================================================//

    public function enableCheckListPerm() {
        $this->check_list_perm = true; return $this;
    }

    public function disableCheckListPerm() {
        $this->check_list_perm = false; return $this;
    }

    public function checkListPerm($ctype_name) {

        $result = true;

        if($this->check_list_perm){

            if(!$this->cms_user->is_logged){
                if(cmsPermissions::getRuleSubjectPermissions('content', $ctype_name, 'view_list')){
                    $result = false;
                }
            } else {

                if (cmsUser::isDenied($ctype_name, 'view_list')) {

                    if (cmsUser::isDenied($ctype_name, 'view_list', 'allow')) {
                        $result = true;
                    } else if (cmsUser::isDenied($ctype_name, 'view_list', 'other')) {
                        $result = false;
                    } else {
                        $result = null;
                    }

                }

            }

        }

        return $result;

    }

    public function getFilterTitles() {
        return $this->filter_titles;
    }

    public function getActiveFiltersQuery() {

        $filter_query_params = [];

        if($this->list_filter && $this->active_filters){

            foreach ($this->active_filters as $fname => $fvalue) {
                if(!isset($this->list_filter['filters'][$fname])){
                    $filter_query_params[$fname] = $fvalue;
                }
            }

        } else {
            $filter_query_params = $this->active_filters;
        }

        return $filter_query_params ? http_build_query($filter_query_params) : '';
    }

    public function renderItemsList($ctype, $page_url, $hide_filter = false, $category_id = 0, $filters = [], $dataset = false, $ext_hidden_params = []){

        // Обнуляем активные фильтры для их заполнения
        $this->active_filters = $filters;

        $props = $props_fields = false;

        // Получаем поля для данного типа контента
        $fields = cmsCore::getModel('content')->getContentFields($ctype['name']);

		list($ctype, $fields) = cmsEventsManager::hook(
            ['content_list_fields', 'content_'.$ctype['name'].'_list_fields'],
            [$ctype, $fields], null, $this->request
        );

        $page = $this->request->get($this->request_page_name, 1);

        $perpage = (empty($ctype['options']['limit']) ? self::perpage : $ctype['options']['limit']);

        if ($hide_filter) { $ctype['options']['list_show_filter'] = false; }

        if ($category_id && $category_id>1){

            // для фильтров свойств, привязанных к категории
            if(!empty($this->list_filter['filters']['category_id'])){
                $this->active_filters['category_id'] = $category_id;
            }

            // Получаем поля-свойства
            $props = cmsCore::getModel('content')->getContentProps($ctype['name'], $category_id);
            $props_fields = $this->getPropsFields($props);
        }

		// проверяем запросы фильтрации по полям
		foreach($fields as $name => $field){

            $field['handler']->setItem(['ctype_name' => $ctype['name'], 'id' => null])->setContext('filter');

            $fields[$name] = $field;

			if (!$this->request->has($name)){ continue; }

			$value = $this->request->get($name, false, $field['handler']->getDefaultVarType());

            $value = $field['handler']->storeFilter($value);
			if (is_empty_value($value)) { continue; }

			if($field['handler']->applyFilter($this->model, $value) !== false){

                $this->active_filters[$name] = $value;

                $filter_title = $field['handler']->getStringValue($value);

                if($filter_title && !isset($this->list_filter['filters'][$name])){
                    $this->filter_titles[] = mb_strtolower($field['title'].' '.$filter_title);
                }

            }

		}

		// проверяем запросы фильтрации по свойствам
		if (isset($props) && is_array($props)){
			foreach($props as $key => $prop){

				$name = "p{$prop['id']}";

                $prop['handler'] = $props_fields[$prop['id']];

                $prop['handler']->setItem(['ctype_name' => $ctype['name'], 'id' => null])->setContext('filter');

                $props[$key] = $prop;

				if (!$this->request->has($name)){ continue; }

				$value = $this->request->get($name, false, $prop['handler']->getDefaultVarType());

                $value = $prop['handler']->storeFilter($value);
				if (is_empty_value($value)) { continue; }

				if($this->model->filterPropValue($ctype['name'], $prop, $value) !== false){

                    $this->active_filters[$name] = $value;

                    $filter_title = $prop['handler']->getStringValue($value);

                    if($filter_title && !isset($this->list_filter['filters'][$name])){
                        $this->filter_titles[] = mb_strtolower($prop['title'].' '.$filter_title);
                    }

                }

			}
		}

        // Активный фильтры из GET параметров
        $filter_query = $this->getActiveFiltersQuery();

        // Если мы в фильтре, дополняем его урл
        // полями, которых в нём нет
        if($this->list_filter){
            if($filter_query){
                $page_url['filter_link'] .= '?'.$filter_query;
            }
        }

        // применяем приватность
        // флаг показа только названий
        $hide_except_title = $this->model->applyPrivacyFilter($ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

        // Постраничный вывод
        if($perpage){
            $this->model->limitPage($page, $perpage);
        }

		list($ctype, $this->model) = cmsEventsManager::hook(
            ['content_list_filter', 'content_'.$ctype['name'].'_list_filter'],
            [$ctype, $this->model], null, $this->request
        );

        // правила доступа на просмотр списка записей
        $check_list_perm_result = $this->checkListPerm($ctype['name']);
        if($check_list_perm_result === false){
            $this->model->filterEqual('user_id', $this->cms_user->id);
        }
        if($check_list_perm_result === null){
            $items = false; $total = 0;
        }

        // контекст списка
        $list_type = $this->getListContext();

        // Получаем количество и список записей
        $total = isset($total) ? $total : $this->model->getContentItemsCount($ctype['name']);

        // Единый порядок массива для вычисления хэша
        array_multisort($this->active_filters);

        if($this->request->has('show_count')){

            if(!empty($ctype['labels']['many'])){
                $hint = LANG_SHOW.' '.html_spellcount($total, $ctype['labels']['one_accusative'], $ctype['labels']['two_accusative'], $ctype['labels']['many_accusative'], 0);
            } else {
                $hint = LANG_SHOW.' '.html_spellcount($total, LANG_CONTENT_SHOW_FILTER_COUNT, false, false, 0);
            }

            // урлы фильтров только для категорий
            if($this->list_type !== 'category_view'){
                return $this->cms_template->renderJSON([
                    'count'       => $total,
                    'filter_link' => false,
                    'hint'        => $hint
                ]);
            }

            // Узнаём совпадение фильтров
            $search_filter = cmsCore::getModel('content')->getContentFilter($ctype, md5(json_encode($this->active_filters)), true, $category_id);

            $filter_link = false;

            if($search_filter){

                $filter_link = (is_array($page_url) ? (!empty($page_url['cancel']) ? $page_url['cancel'] : $page_url['base']) : $page_url).'/'.$search_filter['slug'];
                $this->list_filter = $search_filter;
                $filter_query = $this->getActiveFiltersQuery();

                if($filter_query){
                    $filter_link .= '?'.$filter_query;
                }

            } else if($this->list_filter && !empty($filter_query)){
                $filter_link = $page_url['filter_link'];
            }

            return $this->cms_template->renderJSON([
                'count'       => $total,
                'filter_link' => $filter_link,
                'hint'        => $hint
            ]);

        }

        //$this->model->selectFieldsForList($ctype['name'], $fields);

        $items = isset($items) ? $items : $this->model->getContentItems($ctype['name']);
        // если задано максимальное кол-во, ограничиваем им
        if($this->max_items_count){
            $total = min($total, $this->max_items_count);
            $pages = ceil($total / $perpage);
            if($page > $pages){
                $items = false;
            }
        }

        // если запрос через URL
        if ($this->request->isStandard()) {
            if (!$items && $page > 1) {
                return cmsCore::error404();
            }
        }

        // заполняем поля для шаблона
        if($items){

            list($ctype, $items, $fields) = cmsEventsManager::hook(
                ['content_before_fields_list', 'content_'.$ctype['name'].'_before_fields_list'],
                [$ctype, $items, $fields]
            );

            foreach ($items as $key => $item) {

                $item['ctype'] = $ctype;
                $item['ctype_name'] = $ctype['name'];
                $item['is_private_item'] = $item['is_private'] && $hide_except_title;
                $item['private_item_hint'] = LANG_PRIVACY_HINT;
                $item['fields'] = [];
                // Краткие данные всех полей, разрешенных в списке
                $item['fields_names'] = [];

                // для приватности друзей
                // другие проверки приватности (например для групп) в хуках content_before_list
                if($item['is_private'] == 1){
                    $item['is_private_item'] = $item['is_private_item'] && !$item['user']['is_friend'];
                    $item['private_item_hint'] = LANG_PRIVACY_PRIVATE_HINT;
                }

                // Флаг, что эту запись пользователь не видел с последнего визита
                $item['is_new'] = (strtotime($item['date_pub']) > strtotime($this->cms_user->date_log));

                // формируем инфобар
                $item['info_bar'] = $this->getItemInfoBar($ctype, $item, $fields, 'list');

                // строим поля списка
                foreach($fields as $field){

                    if ($field['is_system'] || !$field['is_in_list']) { continue; }

                    // разрешен показ в списке, проверяем в каких именно
                    if(!empty($field['options']['context_list']) && array_search('0', $field['options']['context_list']) === false){
                        if(!in_array($list_type, $field['options']['context_list'])){
                            continue;
                        }
                    }

                    // проверяем что группа пользователя имеет доступ к чтению этого поля
                    if ($field['groups_read'] && !$this->cms_user->isInGroups($field['groups_read'])) {
                        // если группа пользователя не имеет доступ к чтению этого поля,
                        // проверяем на доступ к нему для авторов
                        if (empty($item['user_id']) || empty($field['options']['author_access'])){ continue; }
                        if (!in_array('is_read', $field['options']['author_access'])){ continue; }
                        if ($item['user_id'] != $this->cms_user->id){ continue; }
                    }

                    if (!isset($field['options']['label_in_list'])) {
                        $label_pos = 'none';
                    } else {
                        $label_pos = $field['options']['label_in_list'];
                    }

                    $current_field_data = [
                        'label_pos' => $label_pos,
                        'type'      => $field['type'],
                        'name'      => $field['name'],
                        'title'     => $field['title']
                    ];

                    $item['fields_names'][] = $current_field_data;

                    if (!array_key_exists($field['name'], $item)) {

                        // Виртуальное поле. В таблице ячейки может не быть.
                        if($field['handler']->is_virtual){
                            $item[$field['name']] = '';
                        } else {
                            continue;
                        }
                    }

                    $field_html = $field['handler']->setItem($item)->parseTeaser($item[$field['name']]);
                    if (is_empty_value($field_html)) { continue; }

                    $current_field_data['html'] = $field_html;
                    $current_field_data['options'] = $field['options'];

                    $item['fields'][$field['name']] = $current_field_data;
                }

                foreach($item['fields'] as $name => $field){
                    $item = $fields[$name]['handler']->hookItem($item, $item['fields']);
                }

                $items[$key] = $item;
            }
        }

        list($ctype, $items) = cmsEventsManager::hook(
            ['content_before_list', 'content_'.$ctype['name'].'_before_list'],
            [$ctype, $items]
        );

        cmsModel::cacheResult('current_ctype_fields', $fields);
        cmsModel::cacheResult('current_ctype_props', $props);
        cmsModel::cacheResult('current_ctype_props_fields', $props_fields);
        cmsModel::cacheResult('current_items_list_total', $total);

        $this->cms_template->setContext($this);

        // $filter_query это активный фильтр GET параметров
        // преобразовываем в массив $filter_active для работы пагинации
        // не используем $this->active_filters, поскольку там в том числе и
        // фильтры от "Фильтров" типа контента, им тут делать нечего
        parse_str($filter_query, $filter_active);

        $html = $this->cms_template->renderContentList($ctype, [
			'category_id'       => $category_id,
            'page_url'          => $page_url,
            'ctype'             => $ctype,
            'fields'            => $fields,
            'props'             => $props,
            'props_fields'      => $props_fields,
            'filter_query'      => array_merge($filter_active, $ext_hidden_params), // Используется в пагинации
            'filters'           => $this->active_filters,
            'ext_hidden_params' => $ext_hidden_params,
            'page'              => $page,
            'perpage'           => $perpage,
            'total'             => $total,
            'items'             => $items,
            'user'              => $this->cms_user,
            'dataset'           => $dataset,
            'hide_except_title' => $hide_except_title
        ], new cmsRequest([], cmsRequest::CTX_INTERNAL));

        $this->cms_template->restoreContext();

        return $html;
    }

    public function getItemInfoBar($ctype, $item, $fields, $subject = 'item') {

        $bar = [];

        if (!empty($fields['date_pub']['is_in_'.$subject]) && $this->cms_user->isInGroups($fields['date_pub']['groups_read'])){
            $bar['date_pub'] = [
                'css'   => 'bi_date_pub'.(!empty($item['is_new']) ? ' highlight_new' : ''),
                'icon'  => 'calendar-alt',
                'html'  => isset($fields['date_pub']['html']) ? $fields['date_pub']['html'] : $fields['date_pub']['handler']->parse($item['date_pub']),
                'title' => $fields['date_pub']['title']
            ];
        }

        if ($item['is_pub'] < 1){
            $bar['is_pub'] = [
                'css'   => 'bi_not_pub',
                'icon'  => 'calendar-alt',
                'html'  => LANG_CONTENT_NOT_IS_PUB
            ];
        }

        if (!empty($ctype['options']['hits_on'])) {

            $ctype['options']['hits_groups_view'] = $ctype['options']['hits_groups_view'] ?? [];

            if ($this->cms_user->isInGroups($ctype['options']['hits_groups_view'])) {
                $bar['hits'] = [
                    'css'   => 'bi_hits',
                    'icon'  => 'eye',
                    'html'  => html_views_format($item['hits_count']),
                    'title' => html_spellcount($item['hits_count'], LANG_HITS_SPELL)
                ];
            }
        }

        if (!empty($fields['user']['is_in_'.$subject]) && $this->cms_user->isInGroups($fields['user']['groups_read'])){
            $bar['user'] = [
                'css'  => 'bi_user',
                'icon' => 'user',
                'avatar' => isset($item['user']['avatar']) ? $item['user']['avatar'] : [],
                'href' => href_to_profile($item['user']),
                'html' => $item['user']['nickname']
            ];
            if (!empty($item['folder_title']) && $this->cms_user->isPrivacyAllowed($item['user'], 'view_user_'.$ctype['name'])){
                $bar['folder'] = [
                    'css'  => 'bi_folder',
                    'icon' => 'folder',
                    'html' => $item['folder_title'],
                    'href' => href_to_profile($item['user'], ['content', $ctype['name'], $item['folder_id']])
                ];
            }
        }

        if (!empty($ctype['options']['share_code']) && $subject === 'item'){
            $bar['share'] = [
                'css'   => 'bi_share',
                'html'  => $ctype['options']['share_code']
            ];
        }

        if (!$item['is_approved']){
            $bar['is_approved'] = [
                'css'   => 'bi_not_approved'.(!empty($item['is_new_item']) ? ' is_edit_item' : ''),
                'icon'  => 'user-clock',
                'html'  => !empty($item['is_draft']) ? LANG_CONTENT_DRAFT_NOTICE : (empty($item['is_new_item']) ? LANG_CONTENT_EDITED.'. ' : '').LANG_CONTENT_NOT_APPROVED
            ];
        }

        return $bar;
    }

//============================================================================//
//============================================================================//

    public function getPermissionsSubjects() {

        $ctypes = $this->model->getContentTypes();

        $subjects = [];

        foreach ($ctypes as $ctype) {
            $subjects[] = [
                'name'  => $ctype['name'],
                'title' => $ctype['title']
            ];
        }

        return $subjects;
    }

//============================================================================//
//============================================================================//

    /**
     * DEPRECATED
     * use cmsCore::getController('moderation')->requestModeration($ctype_name, $item, $is_new_item);
     */
    public function requestModeration($ctype_name, $item, $is_new_item = true){
        return cmsCore::getController('moderation')->requestModeration($ctype_name, $item, $is_new_item);
    }

    public function getCategoryForm($ctype, $action) {

        return cmsEventsManager::hook('content_cat_form', $this->getForm('category', [$ctype, $action]));
    }

//============================================================================//
//============================================================================//

    public function getItemForm($ctype, $fields, $action, $data = [], $item_id = 0, $item = []) {

        // Контейнер для передачи дополнительных списков:
        // $folders_list и т.д.
        extract($data);

        // Строим форму
        $form = new cmsForm();

        // Если включены категории, добавляем в форму поле выбора категории
        if ($ctype['is_cats'] && ($action !== 'edit' || $ctype['options']['is_cats_change'])) {

            $cats = $this->getFormCategories($ctype['name']);

            $fieldset_id = $form->addFieldset(LANG_CATEGORY, 'category');

            $form->addField($fieldset_id,
                new fieldList('category_id', [
                    'rules' => [
                        ['required']
                    ],
                    'items' => $cats
                ])
            );

            if (cmsUser::isAllowed($ctype['name'], 'add_cat')) {
                $form->addField($fieldset_id, new fieldString('new_category', [
                    'title' => LANG_ADD_CATEGORY_QUICK
                ]));
            }

            if (!empty($ctype['options']['is_cats_multi'])) {
                unset($cats['']);
                $form->addField($fieldset_id,
                    new fieldList('add_cats', [
                        'title'              => LANG_ADDITIONAL_CATEGORIES,
                        'is_chosen_multiple' => true,
                        'items'              => $cats
                    ])
                );
            }
        }

        // Если включены личные папки, добавляем в форму поле выбора личной папки
        if ($ctype['is_folders']) {

            $fieldset_id = $form->addFieldset(LANG_FOLDER, 'folder', ['is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('folder', $ctype['options']['is_collapsed'])]);

            $folders = ['0' => ''];

            if (!empty($folders_list)) {
                $folders += $folders_list;
            }

            $form->addField($fieldset_id,
                new fieldList('folder_id', [
                    'items' => $folders
                ])
            );

            $form->addField($fieldset_id, new fieldString('new_folder', [
                'title' => LANG_ADD_FOLDER_QUICK
            ]));
        }

        // Если есть поля-свойства, то добавляем область
        // После этого поля будут добавляться свойства
        if (!empty($ctype['props'])) {
            $form->addFieldset('', 'props', [
                'is_empty'  => true,
                'is_hidden' => true
            ]);
        }

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function ($field, $user) use ($item, $action) {

            // пропускаем системные поля
            if ($field['is_system']) {
                return false;
            }

            if ($action === 'add') {
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
                    if (!empty($item['user_id']) && !empty($field['options']['author_access'])) {
                        if (!in_array('is_edit', $field['options']['author_access'])) {
                            return false;
                        }
                        if ($item['user_id'] == $user->id) {
                            return true;
                        }
                    }
                    return false;
                }
            }

            return true;
        });

        // Добавляем поля в форму
        foreach ($fieldsets as $fieldset) {

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid, [
                'is_collapsed' => !empty($ctype['options']['is_collapsed']) && $fid && in_array($fid, $ctype['options']['is_collapsed'])
            ]);

            foreach ($fieldset['fields'] as $field) {
                // добавляем поле в форму
                $form->addField($fieldset_id, $field['handler']);
            }
        }

        // Если ручной ввод SLUG, то добавляем поле для этого
        if (!$ctype['is_auto_url']) {

            $slug_field_rules = [['required'], ['slug']];

            if ($action === 'add') {
                $slug_field_rules[] = ['unique', $this->model->table_prefix . $ctype['name'], 'slug'];
            }

            if ($action === 'edit') {
                $slug_field_rules[] = ['unique_exclude', $this->model->table_prefix . $ctype['name'], 'slug', $item_id];
            }

            $fieldset_id = $form->addFieldset(LANG_SLUG);

            $form->addField($fieldset_id, new fieldString('slug', [
                'prefix' => '/' . ((!$this->cms_config->ctype_default || !in_array($ctype['name'], $this->cms_config->ctype_default)) ? $ctype['name'] . '/' : ''),
                'suffix' => '.html',
                'rules'  => $slug_field_rules
            ]));
        }

        // Если разрешено управление видимостью, то добавляем поле
        if (cmsUser::isAllowed($ctype['name'], 'privacy')) {

            $fieldset_id = $form->addFieldset(LANG_PRIVACY, 'privacy_wrap', ['is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('privacy_wrap', $ctype['options']['is_collapsed'])]);

            $items = [
                0 => LANG_PRIVACY_PUBLIC
            ];

            $privacy_types = cmsEventsManager::hookAll('content_privacy_types', [$ctype, $fields, $action, $item]);

            if (is_array($privacy_types)) {
                foreach ($privacy_types as $privacy_type) {
                    foreach ($privacy_type['types'] as $name => $title) {
                        $items[$name] = $title;
                    }
                    if (!empty($privacy_type['fields'])) {
                        foreach ($privacy_type['fields'] as $privacy_field) {
                            $form->addField($fieldset_id, $privacy_field);
                        }
                    }
                }
            }

            if (count($items) > 1) {
                $form->addFieldToBeginning($fieldset_id, new fieldList('is_private', [
                    'items' => $items,
                    'rules' => [['number']]
                ]));
            }
        }

        //
        // Если ручной ввод ключевых слов или описания, то добавляем поля для этого
        //
        if (!empty($ctype['options']['is_manual_title']) || !$ctype['is_auto_keys'] || !$ctype['is_auto_desc']) {

            $fieldset_id = $form->addFieldset(LANG_SEO, 'seo_wrap', ['is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('seo_wrap', $ctype['options']['is_collapsed'])]);

            $table_name = $this->model->getContentTypeTableName($ctype['name']);

            if ($ctype['options']['is_manual_title']) {
                $form->addField($fieldset_id, new fieldString('seo_title', [
                    'title'   => LANG_SEO_TITLE,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'options' => [
                        'max_length'        => 256,
                        'show_symbol_count' => true
                    ]
                ]));
            }

            if (!$ctype['is_auto_keys']) {
                $form->addField($fieldset_id, new fieldString('seo_keys', [
                    'title'   => LANG_SEO_KEYS,
                    'hint'    => LANG_SEO_KEYS_HINT,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'options' => [
                        'max_length'        => 256,
                        'show_symbol_count' => true
                    ]
                ]));
            }

            if (!$ctype['is_auto_desc']) {
                $form->addField($fieldset_id, new fieldText('seo_desc', [
                    'title'         => LANG_SEO_DESC,
                    'hint'          => LANG_SEO_DESC_HINT,
                    'is_strip_tags' => true,
                    'can_multilanguage' => true,
                    'multilanguage_params' => [
                        'is_table_field' => true,
                        'table' => $table_name
                    ],
                    'options'       => [
                        'max_length'        => 256,
                        'show_symbol_count' => true
                    ]
                ]));
            }
        }

        //
        // Если включен выбор даты публикации, то добавляем поля
        //
        $pub_fieldset_id   = 0;
        $is_dates          = $ctype['is_date_range'];
        $is_pub_start_date = cmsUser::isAllowed($ctype['name'], 'pub_late');
        $is_pub_end_date   = cmsUser::isAllowed($ctype['name'], 'pub_long', 'any');
        $is_pub_end_days   = cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');
        $is_pub_control    = cmsUser::isAllowed($ctype['name'], 'pub_on');
        $is_pub_ext        = cmsUser::isAllowed($ctype['name'], 'pub_max_ext');
        $pub_max_days      = intval(cmsUser::getPermissionValue($ctype['name'], 'pub_max_days'));

        if ($this->cms_user->is_admin) {
            $is_pub_end_days = false;
        }

        $is_pub_collapsed = !empty($ctype['options']['is_collapsed']) && in_array('pub_wrap', $ctype['options']['is_collapsed']);

        if ($is_pub_control) {

            $pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset(LANG_CONTENT_PUB, 'pub_wrap', ['is_collapsed' => $is_pub_collapsed]);

            $form->addField($pub_fieldset_id, new fieldList('is_pub', [
                'title' => sprintf(LANG_CONTENT_IS_PUB, $ctype['labels']['create']),
                'hint'  => sprintf(LANG_CONTENT_IS_PUB_HINT, $ctype['labels']['create']),
                'default' => 1,
                'items'   => [
                    1  => LANG_YES,
                    0  => LANG_NO,
                    -1 => LANG_NO.', '.mb_strtolower(LANG_HIDE)
                ]
            ]));
        }

        if ($is_dates) {

            if ($is_pub_start_date) {

                $pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset(LANG_CONTENT_PUB, 'pub_wrap', ['is_collapsed' => $is_pub_collapsed]);

                $m = date('i');

                $form->addField($pub_fieldset_id, new fieldDate('date_pub', [
                    'title'   => LANG_CONTENT_DATE_PUB,
                    'default' => date('Y-m-d H:') . ($m - ($m % 5)),
                    'options' => [
                        'show_time' => true
                    ],
                    'rules' => [
                        ['required']
                    ]
                ]));
            }

            if ($is_pub_end_date) {

                $pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset(LANG_CONTENT_PUB, 'pub_wrap', ['is_collapsed' => $is_pub_collapsed]);

                $form->addField($pub_fieldset_id, new fieldDate('date_pub_end', [
                    'title'   => LANG_CONTENT_DATE_PUB_END,
                    'hint'    => LANG_CONTENT_DATE_PUB_END_HINT,
                    'options' => [
                        'show_time' => true
                    ]
                ]));
            }

            if ($action === 'edit') {
                $is_expired = !empty($item['date_pub_end']) && (strtotime($item['date_pub_end']) - time()) <= 0;
            }

            if (($action === 'add' && $is_pub_end_days) || ($action === 'edit' && $is_expired && $is_pub_ext && $is_pub_end_days)) {

                $pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset(LANG_CONTENT_PUB, 'pub_wrap', ['is_collapsed' => $is_pub_collapsed]);

                $title = $action === 'add' ? LANG_CONTENT_PUB_LONG : LANG_CONTENT_PUB_LONG_EXT;

                $rules = [];

                if ($action === 'add') {
                    $rules[] = ['required'];
                    $min = 1;
                }

                if ($action === 'edit') {
                    $min = 0;
                }

                $rules[] = ['min', $min];

                if ($pub_max_days) {

                    $days = [];

                    $rules[] = ['number'];
                    $rules[] = ['max', $pub_max_days];

                    for ($d = $pub_max_days; $d >= $min; $d--) {
                        $days[$d] = $d;
                    }

                    $form->addField($pub_fieldset_id, new fieldList('pub_days', [
                        'title'   => $title,
                        'hint'    => $action === 'add' ? false : sprintf(LANG_CONTENT_PUB_LONG_NOW, html_date($item['date_pub_end'])),
                        'default' => $pub_max_days,
                        'items'   => $days,
                        'rules'   => $rules
                    ]));

                } else {


                    $rules[] = ['max', 65535];

                    $form->addField($pub_fieldset_id, new fieldNumber('pub_days', [
                        'title'   => $title,
                        'default' => 10,
                        'rules'   => $rules
                    ]));
                }
            }
        }

        // выбор шаблона записи
        if ($this->cms_user->is_admin) {

            $styles = $this->cms_template->getAvailableContentItemStyles($ctype['name']);

            if ($styles) {

                $fieldset_id = $form->addFieldset(
                    LANG_CONTENT_TEMPLATE,
                    'template_item',
                    ['is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('template_item', $ctype['options']['is_collapsed'])]
                );

                $form->addField($fieldset_id, new fieldList('template', [
                    'items' => ['' => LANG_BY_DEFAULT] + $styles
                ]));
            }
        }

        list($form, $item, $ctype) = cmsEventsManager::hook('content_item_form', [$form, $item, $ctype], null, $this->request);

        // Хук с контекстом использования формы. Можно было бы скорректировать хук выше, но совместимость :)
        list($form, $item, $ctype, $action, $data) = cmsEventsManager::hook('content_item_form_context',
            [$form, $item, $ctype, $action, $data],
            null,
            $this->request
        );

        return $form;
    }

    public function getFormCategories($ctype_name) {

        $level_offset   = 0;
        $last_header_id = false;
        $items          = ['' => ''];

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { return $items; }

        $tree = $this->model->limit(0)->getCategoriesTree($ctype_name);
        if (!$tree) { return $items; }

        foreach ($tree as $c) {

            if (!empty($c['allow_add']) && !$this->cms_user->isInGroups($c['allow_add'])) {
                continue;
            }

            if ($ctype['options']['is_cats_only_last']) {

                $dash_pad = $c['ns_level'] - 1 >= 0 ? str_repeat('-', $c['ns_level'] - 1) . ' ' : '';

                if ($c['ns_right'] - $c['ns_left'] == 1) {
                    if ($last_header_id !== false && $last_header_id != $c['parent_id']) {
                        $items['opt' . $c['id']] = [str_repeat('-', $c['ns_level'] - 1) . ' ' . $c['title']];
                    }
                    $items[$c['id']] = $dash_pad . $c['title'];
                } else if ($c['parent_id'] > 0) {
                    $items['opt' . $c['id']] = [$dash_pad . $c['title']];
                    $last_header_id = $c['id'];
                }

                continue;
            }

            if (!$ctype['options']['is_cats_only_last']) {

                if ($c['parent_id'] == 0 && !$ctype['options']['is_cats_open_root']) {
                    $level_offset = 1;
                    continue;
                }

                $items[$c['id']] = str_repeat('-- ', $c['ns_level'] - $level_offset) . ' ' . $c['title'];

                continue;
            }
        }

        return $items;
    }

    public function getPropsFields($props) {

        $fields = [];

        if (!is_array($props)) { return $fields; }

        foreach ($props as $prop) {

            $prop['rules']   = [];
            $prop['default'] = $prop['values'];

            if (!empty($prop['options']['is_required'])) {
                $prop['rules'][] = [('required')];
            }
            if (!empty($prop['options']['is_filter_multi'])) {
                $prop['options']['filter_multiple'] = 1;
            }
            if (!empty($prop['options']['is_filter_range'])) {
                $prop['options']['filter_range'] = 1;
            }

            switch ($prop['type']) {
                case 'list_multiple':
                    $prop['type'] = 'listbitmask';
                    break;
            }

            $field_class = 'field' . string_to_camel('_', $prop['type']);

            $field = new $field_class('props:' . $prop['id']);

            $field->setOptions($prop);

            $fields[$prop['id']] = $field;
        }

        return $fields;
    }

    public function addFormPropsFields($form, $ctype, $item_cats, $is_submitted = false){

        if($is_submitted && (!$item_cats || $ctype['options']['is_cats_change'])){
            $item_cats = [];
            if ($this->request->has('add_cats') && !empty($ctype['options']['is_cats_multi'])){
                $item_cats = $this->request->get('add_cats', []);
                foreach($item_cats as $index => $cat_id){
                    if (!is_numeric($cat_id) || !$cat_id){
                        unset($item_cats[$index]);
                    }
                }
            }
            if($this->request->has('category_id')){
                $item_cats[] = $this->request->get('category_id', 0);
            }
        }

        if(!$item_cats){
            return $form;
        }

        $item_props = $this->model->getContentProps($ctype['name'], $item_cats);
        if(!$item_props){ return $form; }

        $item_props_fields = $this->getPropsFields($item_props);

        $props = [];

        foreach ($item_props as $p) {
            $props[$p['cat_id']]['title'] = $p['cat_title'];
            $props[$p['cat_id']]['props'][] = $p['id'];
        }

        $fid = 'props';

        foreach ($props as $cat_id => $p) {

            $fid = $form->addFieldsetAfter($fid, $p['title'], 'props'.$cat_id, ['class' => 'icms-content-props__fieldset highlight bg-light']);

            foreach($p['props'] as $prop_id) {
                $form->addField($fid, $item_props_fields[$prop_id]);
            }
        }

        return $form;
    }

//============================================================================//
//============================================================================//

    public function bindItemToParents($ctype, $item, $parents = false) {

        if (!$parents) {
            $parents = $this->model->filterEqual('c.is_enabled', 1)->getContentTypeParents($ctype['id']);
        }

        foreach ($parents as $parent) {

            $this->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);

            $value = isset($item[$parent['id_param_name']]) ? $item[$parent['id_param_name']] : '';

            $ids = [];

            foreach (explode(',', $value) as $id) {
                if (!trim($id)) {
                    continue;
                }
                $ids[] = trim($id);
            }

            $parent_ctype = $this->model->getContentTypeByName($parent['ctype_name']);
            if (!$parent_ctype) {
                continue;
            }

            $current_parents   = [];
            $new_parents       = [];
            $parents_to_delete = [];
            $parents_to_add    = [];

            if (!empty($item['id'])) {
                $current_parents = $this->model->getContentItemParents($parent_ctype, $ctype, $item['id']);
            }

            if ($ids) {
                $this->model->filterIn('id', $ids);
                $new_parents = $this->model->getContentItems($parent['ctype_name']);
            }

            if ($current_parents) {
                foreach ($current_parents as $id => $current_parent) {
                    if (isset($new_parents[$id])) {
                        continue;
                    }
                    if (!in_array($id, $parents_to_delete)) {
                        $parents_to_delete[] = $id;
                    }
                }
            }

            if ($new_parents) {
                foreach ($new_parents as $id => $new_parent) {
                    if (isset($current_parents[$id])) {
                        continue;
                    }
                    if (!in_array($id, $parents_to_add)) {
                        $parents_to_add[] = $id;
                    }
                }
            }

            if ($parent['target_controller'] != 'content') {
                $this->model->setTablePrefix('');
            } else {
                $this->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);
            }

            if ($parents_to_add) {
                foreach ($parents_to_add as $new_parent_id) {
                    $this->model->bindContentItemRelation([
                        'parent_ctype_name' => $parent_ctype['name'],
                        'parent_ctype_id'   => $parent_ctype['id'],
                        'parent_item_id'    => $new_parent_id,
                        'child_ctype_name'  => $ctype['name'],
                        'child_ctype_id'    => $ctype['id'],
                        'child_item_id'     => $item['id'],
                        'target_controller' => $parent['target_controller']
                    ]);
                }
            }

            if ($parents_to_delete) {
                foreach ($parents_to_delete as $old_parent_id) {
                    $this->model->unbindContentItemRelation([
                        'parent_ctype_name' => $parent_ctype['name'],
                        'parent_ctype_id'   => $parent_ctype['id'],
                        'parent_item_id'    => $old_parent_id,
                        'child_ctype_name'  => $ctype['name'],
                        'child_ctype_id'    => $ctype['id'],
                        'child_item_id'     => $item['id'],
                        'target_controller' => $parent['target_controller']
                    ]);
                }
            }
        }

    }

    public function applyCategorySeo($ctype, $category, $dataset, $add_meta_item = []) {

        // паттерны
        $h1_pattern = $title_pattern = $keys_pattern = $desc_pattern = '';

        $meta_item = array_merge([
            'title'             => null,
            'description'       => null,
            'ds_title'          => null,
            'ds_description'    => null,
            'f_title'           => null,
            'f_description'     => null,
            'ctype_title'       => $ctype['title'],
            'ctype_description' => ($ctype['description'] ? strip_tags($ctype['description']) : null),
            'ctype_label1'      => (!empty($ctype['labels']['one']) ? $ctype['labels']['one'] : null),
            'ctype_label2'      => (!empty($ctype['labels']['two']) ? $ctype['labels']['two'] : null),
            'ctype_label10'     => (!empty($ctype['labels']['many']) ? $ctype['labels']['many'] : null),
            'filter_string'     => null
        ], $add_meta_item);

        $filter_titles = $this->getFilterTitles();

        if (!empty($filter_titles)){
            $meta_item['filter_string'] = implode(', ', $filter_titles);
        }

        if (!empty($dataset['title'])){
            $meta_item['ds_title'] = $dataset['title'];
        }

        if (!empty($dataset['description'])){
            $meta_item['ds_description'] = strip_tags($dataset['description']);
        }

        if (!empty($category['title'])){
            $meta_item['title'] = $category['title'];
        }

        if (!empty($category['description'])){
            $meta_item['description'] = strip_tags($category['description']);
        }

        if (!empty($ctype['options']['seo_ctype_h1_pattern'])){
            $h1_pattern = $ctype['options']['seo_ctype_h1_pattern'];
        }
        if (!empty($ctype['seo_title'])){
            $title_pattern = $ctype['seo_title'];
        }
        if (!empty($ctype['seo_keys'])){
            $keys_pattern = $ctype['seo_keys'];
        }
        if (!empty($ctype['seo_desc'])){
            $desc_pattern = $ctype['seo_desc'];
        }

        if(!empty($category['title']) && (empty($dataset['first_ds']) || !empty($category['id']))){

            if (!empty($ctype['options']['seo_cat_h1_pattern'])){
                $h1_pattern = $ctype['options']['seo_cat_h1_pattern'];
            }
            if (!empty($ctype['options']['seo_cat_title_pattern'])){
                $title_pattern = $ctype['options']['seo_cat_title_pattern'];
            }
            if (!empty($ctype['options']['seo_cat_keys_pattern'])){
                $keys_pattern = $ctype['options']['seo_cat_keys_pattern'];
            }
            if (!empty($ctype['options']['seo_cat_desc_pattern'])){
                $desc_pattern = $ctype['options']['seo_cat_desc_pattern'];
            }

        }
        if (!empty($dataset['seo_h1'])){
            $h1_pattern = $dataset['seo_h1'];
        }
        if (!empty($dataset['seo_title'])){
            $title_pattern = $dataset['seo_title'];
        }
        if (!empty($dataset['seo_keys'])){
            $keys_pattern = $dataset['seo_keys'];
        }
        if (!empty($dataset['seo_desc'])){
            $desc_pattern = $dataset['seo_desc'];
        }

        if($this->list_filter){
            if ($this->list_filter['title']){
                $meta_item['f_title'] = $this->list_filter['title'];
            }
            if ($this->list_filter['description']){
                $meta_item['f_description'] = strip_tags($this->list_filter['description']);
            }
            if ($this->list_filter['seo_h1']){
                $h1_pattern = $this->list_filter['seo_h1'];
            }
            if ($this->list_filter['seo_title']){
                $title_pattern = $this->list_filter['seo_title'];
            }
            if ($this->list_filter['seo_keys']){
                $keys_pattern = $this->list_filter['seo_keys'];
            }
            if ($this->list_filter['seo_desc']){
                $desc_pattern = $this->list_filter['seo_desc'];
            }
        }

        /**
         * формируем h1
         */
        // По умолчанию, это заголовок типа контента
        $h1_str = $title_str = $meta_item['ctype_title'];
        // заголовок категории
        if(!empty($category['title'])){
            $h1_str = $title_str = $category['title'];
        }
        // есть паттерн
        if($h1_pattern){

            $h1_str = $h1_pattern;

            $this->cms_template->setPageH1Item($meta_item);

        }
        // то, что задано вручную для катеории в приоритете
        if(!empty($category['seo_h1']) && empty($dataset['seo_h1'])){

            $h1_str = $category['seo_h1'];

            $this->cms_template->setPageH1Item($meta_item);
        }

        /**
         * Формируем title
         */
        // есть паттерн
        if($title_pattern){

            $title_str = $title_pattern;

            $this->cms_template->setPageTitleItem($meta_item);
        }
        // заданное вручную в приоритете
        if (!empty($category['seo_title']) && empty($dataset['seo_title'])){

            $title_str = $category['seo_title'];

            $this->cms_template->setPageTitleItem($meta_item);
        }

        /**
         * Формируем ключи (хотя по сути они давно устаревшие)
         */
        $keys_str = $ctype['seo_keys'];
        // есть паттерн
        if($keys_pattern){

            $keys_str = $keys_pattern;

            $this->cms_template->setPageKeywordsItem($meta_item);

        }
        // ключи для категории в приоритете
        if (!empty($category['seo_keys']) && empty($dataset['seo_keys'])){

            $keys_str = $category['seo_keys'];

            $this->cms_template->setPageKeywordsItem($meta_item);
        }

        /**
         * Формируем описание
         */
        $desc_str = $ctype['seo_desc'];
        // есть паттерн
        if($desc_pattern){

            $desc_str = $desc_pattern;

            $this->cms_template->setPageDescriptionItem($meta_item);

        }
        // описание для категории в приоритете
        if (!empty($category['seo_desc']) && empty($dataset['seo_desc'])){

            $desc_str = $category['seo_desc'];

            $this->cms_template->setPageDescriptionItem($meta_item);
        }

        $this->cms_template->setPageH1($h1_str);
        $this->cms_template->setPageTitle($title_str);
        $this->cms_template->setPageKeywords($keys_str);
        $this->cms_template->setPageDescription($desc_str);

        return [
            'meta_item' => $meta_item,
            'h1_str'    => $h1_str,
            'title_str' => $title_str,
            'keys_str'  => $keys_str,
            'desc_str'  => $desc_str
        ];
    }

    public function getCtypeDatasets($ctype, $params) {

        $first_ds = false;

        $list_type = $this->getListContext();

        $datasets = $this->model->getContentDatasets($ctype['id'], true, function ($item, $model) use ($params, $list_type, &$first_ds) {

            $is_view      = !$item['cats_view'] || in_array($params['cat_id'], $item['cats_view']);
            $is_user_hide = $item['cats_hide'] && in_array($params['cat_id'], $item['cats_hide']);

            if (!$is_view || $is_user_hide) {
                return false;
            }

            $is_view      = empty($item['list']['show']) || in_array($list_type, $item['list']['show']);
            $is_user_hide = !empty($item['list']['hide']) && in_array($list_type, $item['list']['hide']);

            if (!$is_view || $is_user_hide) {
                return false;
            }

            $item['title'] = string_replace_user_properties($item['title']);

            $item['first_ds'] = $first_ds ? false : true;
            $first_ds = true;

            return $item;
        });

        list($datasets, $ctype) = cmsEventsManager::hook(
            ['content_datasets', 'content_' . $ctype['name'] . '_datasets'],
            [$datasets, $ctype]
        );

        return $datasets;
    }

    public function getContentTypeForModeration($name) {

        if (is_numeric($name)) {
            $ctype = $this->model->getContentType($name);
        } else {
            $ctype = $this->model->getContentTypeByName($name);
        }

        return $ctype;
    }

    public function isAverageRating($ctype_name, $item_id, $score) {

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if(!$ctype){ return true; }

        return !array_key_exists('rating_is_average', $ctype['options']) ? true : boolval($ctype['options']['rating_is_average']);
    }
}
