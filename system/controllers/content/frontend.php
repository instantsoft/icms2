<?php
class content extends cmsFrontend {

    const perpage = 15;

    public $max_items_count = 0;
    public $request_page_name = 'page';

    public $list_filter = false;

    private $check_list_perm = true;

    private $filter_titles = array();

//============================================================================//
//============================================================================//

    public function route($uri){

        $action_name = $this->parseRoute($this->cms_core->uri);

        if (!$action_name) { cmsCore::error404(); }

        $this->runAction($action_name);

    }

	public function parseRoute($uri){

		$action_name = parent::parseRoute($uri);

		if (!$action_name && $this->cms_config->ctype_default){
			$action_name = parent::parseRoute($this->cms_config->ctype_default[0] . '/' . $uri);
		}

		return $action_name;

	}

//============================================================================//
//============================================================================//

    public function getMenuAddItems($menu_item_id){

        $result = array('url' => '#', 'items' => false);

        $ctypes = $this->model->getContentTypes();
        if (!$ctypes) { return $result; }

        foreach($ctypes as $ctype){

            if (!cmsUser::isAllowed($ctype['name'], 'add')) { continue; }

            if (!empty($ctype['labels']['create'])){

                $result['items'][] = array(
                    'id'           => 'content_add' . $ctype['id'],
                    'parent_id'    => $menu_item_id,
                    'title'        => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                    'childs_count' => 0,
                    'url'          => href_to($ctype['name'], 'add')
                );

            }

        }

        return $result;

    }

    public function getMenuPrivateItems($menu_item_id){

        $result = array('url' => '#', 'items' => false);

        $ctypes = $this->model->getContentTypes();
        if (!$ctypes) { return false; }

        foreach($ctypes as $ctype){

            if (!$ctype['options']['list_on']) { continue; }

            $result['items'][] = array(
                'id'           => 'private_list' . $ctype['id'],
                'parent_id'    => $menu_item_id,
                'title'        => sprintf(LANG_CONTENT_PRIVATE_FRIEND_ITEMS, mb_strtolower($ctype['title'])),
                'childs_count' => 0,
                'url'          => href_to($ctype['name'], 'from_friends')
            );

        }

        return $result;

    }

    public function getMenuCategoriesItems($menu_item_id, $ctype){

        $result = array('url' => href_to($ctype['name']), 'items' => false);

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

            $item_id   = 'content.'.$ctype['name'].'.'.$cat['id'];
            $parent_id = 'content.'.$ctype['name'].'.'.$cat['parent_id'];

            if($cat['parent_id'] > 1){
                if(!isset($childs_count[$cat['parent_id']])){
                    $childs_count[$cat['parent_id']] = 1;
                } else {
                    $childs_count[$cat['parent_id']] += 1;
                }
            }

            $result['items'][$cat['id']] = array(
                'id'           => $item_id,
                'parent_id'    => ($cat['parent_id'] == 1 ? $menu_item_id : $parent_id),
                'title'        => $cat['title'],
                'childs_count' => 0,
                'url'          => href_to($base_url, $cat['slug'])
            );

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
			if (!$value) { continue; }

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
				if (!$value) { continue; }

				if($this->model->filterPropValue($ctype['name'], $prop, $value) !== false){

                    $this->active_filters[$name] = $value;

                    $filter_title = $prop['handler']->getStringValue($value);

                    if($filter_title && !isset($this->list_filter['filters'][$name])){
                        $this->filter_titles[] = mb_strtolower($prop['title'].' '.$filter_title);
                    }

                }

			}
		}

        // Если мы в фильтре, дополняем его урл
        // полями, которых в нём нет
        if($this->list_filter){

            $filter_query = $this->getActiveFiltersQuery();

            if($filter_query){
                $page_url['filter_link'] .= '?'.$filter_query;
            }

        }

        // применяем приватность
        // флаг показа только названий
        $hide_except_title = $this->model->applyPrivacyFilter($ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

		list($ctype, $this->model) = cmsEventsManager::hook('content_list_filter', array($ctype, $this->model));
		list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", array($ctype, $this->model));

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
                $hint = LANG_SHOW.' '.html_spellcount($total, $ctype['labels']['one'], $ctype['labels']['two'], $ctype['labels']['many'], 0);
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
            $search_filter = cmsCore::getModel('content')->getContentFilter($ctype, md5(json_encode($this->active_filters)), true);

            $filter_link = false;

            if($search_filter){
                $filter_link = (is_array($page_url) ? (!empty($page_url['cancel']) ? $page_url['cancel'] : $page_url['base']) : $page_url).'/'.$search_filter['slug'];
            }

            if($this->list_filter && !empty($filter_query)){
                $filter_link = $page_url['filter_link'];
            }

            return $this->cms_template->renderJSON([
                'count'       => $total,
                'filter_link' => $filter_link,
                'hint'        => $hint
            ]);

        }

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
        if($this->request->isStandard()){
            if(!$items && $page > 1){ cmsCore::error404(); }
        }

        // заполняем поля для шаблона
        if($items){
            foreach ($items as $key => $item) {

                $item['ctype'] = $ctype;
                $item['ctype_name'] = $ctype['name'];
                $item['is_private_item'] = $item['is_private'] && $hide_except_title;
                $item['private_item_hint'] = LANG_PRIVACY_HINT;
                $item['fields'] = array();

                // для приватности друзей
                // другие проверки приватности (например для групп) в хуках content_before_list
                if($item['is_private'] == 1){
                    $item['is_private_item'] = $item['is_private_item'] && !$item['user']['is_friend'];
                    $item['private_item_hint'] = LANG_PRIVACY_PRIVATE_HINT;
                }

                // строим поля списка
                foreach($fields as $field){

                    if ($field['is_system'] || !$field['is_in_list'] || !isset($item[$field['name']])) { continue; }

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

                    if (!$item[$field['name']] && $item[$field['name']] !== '0') { continue; }

                    if (!isset($field['options']['label_in_list'])) {
                        $label_pos = 'none';
                    } else {
                        $label_pos = $field['options']['label_in_list'];
                    }

                    $field_html = $field['handler']->setItem($item)->parseTeaser($item[$field['name']]);
                    if (!$field_html) { continue; }

                    $item['fields'][$field['name']] = array(
                        'label_pos' => $label_pos,
                        'type'      => $field['type'],
                        'name'      => $field['name'],
                        'options'   => $field['options'],
                        'title'     => $field['title'],
                        'html'      => $field_html
                    );

                }

                $item['is_new'] = (strtotime($item['date_pub']) > strtotime($this->cms_user->date_log));

                // формируем инфобар
                $item['info_bar'] = $this->getItemInfoBar($ctype, $item, $fields, 'list');

                $items[$key] = $item;

            }
        }

        list($ctype, $items) = cmsEventsManager::hook('content_before_list', array($ctype, $items));
        list($ctype, $items) = cmsEventsManager::hook("content_{$ctype['name']}_before_list", array($ctype, $items));

        cmsModel::cacheResult('current_ctype_fields', $fields);
        cmsModel::cacheResult('current_ctype_props', $props);
        cmsModel::cacheResult('current_ctype_props_fields', $props_fields);
        cmsModel::cacheResult('current_items_list_total', $total);

        $this->cms_template->setContext($this);

        $html = $this->cms_template->renderContentList($ctype, array(
			'category_id'       => $category_id,
            'page_url'          => $page_url,
            'ctype'             => $ctype,
            'fields'            => $fields,
            'props'             => $props,
            'props_fields'      => $props_fields,
            'filters'           => $this->active_filters,
            'ext_hidden_params' => $ext_hidden_params,
            'page'              => $page,
            'perpage'           => $perpage,
            'total'             => $total,
            'items'             => $items,
            'user'              => $this->cms_user,
            'dataset'           => $dataset,
            'hide_except_title' => $hide_except_title
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $this->cms_template->restoreContext();

        return $html;

    }


    public function getItemInfoBar($ctype, $item, $fields, $subject = 'item') {

        $bar = [];

        if (!empty($fields['date_pub']['is_in_'.$subject])){
            $bar[] = [
                'css'   => 'bi_date_pub'.(!empty($item['is_new']) ? ' highlight_new' : ''),
                'html'  => isset($fields['date_pub']['html']) ? $fields['date_pub']['html'] : $fields['date_pub']['handler']->parse($item['date_pub']),
                'title' => $fields['date_pub']['title']
            ];
        }

        if (!$item['is_pub']){
            $bar[] = [
                'css'   => 'bi_not_pub',
                'html'  => LANG_CONTENT_NOT_IS_PUB
            ];
        }

        if (!empty($ctype['options']['hits_on'])){
            $bar[] = [
                'css'   => 'bi_hits',
                'html'  => $item['hits_count'],
                'title' => LANG_HITS
            ];
        }

        if (!empty($fields['user']['is_in_'.$subject])){
            $bar[] = [
                'css'   => 'bi_user',
                'html'  => isset($fields['user']['html']) ? $fields['user']['html'] : $fields['user']['handler']->parse($item['user']),
                'title' => $fields['user']['title']
            ];
            if (!empty($item['folder_title'])){
                $bar[] = [
                    'css'  => 'bi_folder',
                    'html' => $item['folder_title'],
                    'href' => href_to('users', $item['user']['id'], array('content', $ctype['name'], $item['folder_id']))
                ];
            }
        }

        if (!empty($ctype['options']['share_code']) && $subject === 'item'){
            $bar[] = [
                'css'   => 'bi_share',
                'html'  => $ctype['options']['share_code']
            ];
        }

        if (!$item['is_approved']){
            $bar[] = [
                'css'   => 'bi_not_approved'.(!empty($item['is_new_item']) ? ' is_edit_item' : ''),
                'html'  => !empty($item['is_draft']) ? LANG_CONTENT_DRAFT_NOTICE : (empty($item['is_new_item']) ? LANG_CONTENT_EDITED.'. ' : '').LANG_CONTENT_NOT_APPROVED
            ];
        }

        return $bar;

    }

//============================================================================//
//============================================================================//

    public function getPermissionsSubjects(){

        $ctypes = $this->model->getContentTypes();

        $subjects = array();

        foreach($ctypes as $ctype){
            $subjects[] = array(
                'name' => $ctype['name'],
                'title' => $ctype['title']
            );
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

    public function getCategoryForm($ctype, $action){

        $form = $this->getForm('category');

        // Если заданы пресеты
        if (!empty($ctype['options']['cover_sizes'])){

            $fieldset_id = $form->addFieldset(LANG_CATEGORY_COVER);
            $form->addField($fieldset_id, new fieldImage('cover', array(
                'options' => array(
                    'sizes' => $ctype['options']['cover_sizes']
                )
            )));

        }

        // Если ручной ввод ключевых слов или описания, то добавляем поля для этого
        if (!empty($ctype['options']['is_cats_title']) ||
                !empty($ctype['options']['is_cats_h1']) ||
                !empty($ctype['options']['is_cats_keys']) ||
                !empty($ctype['options']['is_cats_desc'])){

            $fieldset_id = $form->addFieldset( LANG_SEO );
            if (!empty($ctype['options']['is_cats_h1'])){
                $form->addField($fieldset_id, new fieldString('seo_h1', array(
                    'title' => LANG_SEO_H1,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
            if (!empty($ctype['options']['is_cats_title'])){
                $form->addField($fieldset_id, new fieldString('seo_title', array(
                    'title' => LANG_SEO_TITLE,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
            if (!empty($ctype['options']['is_cats_keys'])){
                $form->addField($fieldset_id, new fieldString('seo_keys', array(
                    'title' => LANG_SEO_KEYS,
                    'hint' => LANG_SEO_KEYS_HINT,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
            if (!empty($ctype['options']['is_cats_desc'])){
                $form->addField($fieldset_id, new fieldText('seo_desc', array(
                    'title' => LANG_SEO_DESC,
                    'hint' => LANG_SEO_DESC_HINT,
                    'is_strip_tags' => true,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
        }

        // Если ручной ввод SLUG, то добавляем поле для этого
        if (empty($ctype['options']['is_cats_auto_url'])){

            $fieldset_id = $form->addFieldset( LANG_SLUG );
            $form->addField($fieldset_id, new fieldString('slug_key', array(
                'rules' => array( array('required'), array('max_length', 255) )
            )));

        }

        // для администраторов показываем поля доступа
        if($this->cms_user->is_admin){

            $fieldset_id = $form->addFieldset(LANG_PERMISSIONS);
            $form->addField($fieldset_id, new fieldListGroups('allow_add', array(
                'title'       => LANG_CONTENT_CATS_ALLOW_ADD,
                'hint'        => LANG_CONTENT_CATS_ALLOW_ADD_HINT,
                'show_all'    => true,
                'show_guests' => false
            )));

        }

        return cmsEventsManager::hook('content_cat_form', $form);

    }

//============================================================================//
//============================================================================//

    public function getItemForm($ctype, $fields, $action, $data=array(), $item_id=false, $item=false){

        // Контейнер для передачи дополнительных списков:
        // $groups_list, $folders_list и т.д.
        extract($data);

        // Строим форму
        $form = new cmsForm();
        $fieldset_id = $form->addFieldset();

        // Если включены категории, добавляем в форму поле выбора категории
        if ($ctype['is_cats'] && ($action != 'edit' || $ctype['options']['is_cats_change'])){

            $cats = $this->getFormCategories($ctype['name']);

            $fieldset_id = $form->addFieldset(LANG_CATEGORY, 'category');

            $form->addField($fieldset_id,
                new fieldList('category_id', array(
                        'rules' => array(
                            array('required')
                        ),
                        'items' => $cats
                    )
                )
            );

            if (cmsUser::isAllowed($ctype['name'], 'add_cat')){
                $form->addField($fieldset_id, new fieldString('new_category', array(
                    'title' => LANG_ADD_CATEGORY_QUICK
                )));
            }

			if (!empty($ctype['options']['is_cats_multi'])){

				$fieldset_id = $form->addFieldset(LANG_ADDITIONAL_CATEGORIES, 'multi_cats', array(
					'is_empty' => true
				));

			}

        }

        // Если включены личные папки, добавляем в форму поле выбора личной папки
        if ($ctype['is_folders']){
            $fieldset_id = $form->addFieldset(LANG_FOLDER, 'folder', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('folder', $ctype['options']['is_collapsed'])));
            $folders = array('0'=>'');
            if(!empty($folders_list)){ $folders = $folders + $folders_list; }
            $form->addField($fieldset_id,
                new fieldList('folder_id', array(
                    'items' => $folders
                ))
            );

            $form->addField($fieldset_id, new fieldString('new_folder', array(
                'title' => LANG_ADD_FOLDER_QUICK
            )));
        }

        // Если есть поля-свойства, то добавляем область для них
        if (!empty($ctype['props'])){
            $form->addFieldset('', 'props', array(
                'is_empty' => true,
                'class' => 'highlight'
            ));
        }

        // Если этот контент можно создавать в группах (сообществах) то добавляем
        // поле выбора группы
        if (($action == 'add' || $this->cms_user->is_admin) && !empty($groups_list) && $groups_list != array('0'=>'')){

            $fieldset_id = $form->addFieldset(LANG_GROUP, 'group_wrap', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('group_wrap', $ctype['options']['is_collapsed'])));
            $form->addField($fieldset_id,
                new fieldList('parent_id', array(
                        'items' => $groups_list
                    )
                )
            );

        }

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($item, $action){

            // пропускаем системные поля
            if ($field['is_system']) { return false; }

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
                    if (!empty($item['user_id']) && !empty($field['options']['author_access'])){
                        if (!in_array('is_edit', $field['options']['author_access'])){ return false; }
                        if ($item['user_id'] == $user->id){ return true; }
                    }
                    return false;
                }
            }

            return true;

        });

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid, array(
                'is_collapsed' => !empty($ctype['options']['is_collapsed']) && $fid && in_array($fid, $ctype['options']['is_collapsed'])
            ));

            foreach($fieldset['fields'] as $field){

                // добавляем поле в форму
                $form->addField($fieldset_id, $field['handler']);

            }

        }

        // Если ручной ввод SLUG, то добавляем поле для этого
        if (!$ctype['is_auto_url']){

			$slug_field_rules = array( array('required'), array('slug') );

			if ($action == 'add'){ $slug_field_rules[] = array('unique', $this->model->table_prefix . $ctype['name'], 'slug'); }
			if ($action == 'edit'){ $slug_field_rules[] = array('unique_exclude', $this->model->table_prefix . $ctype['name'], 'slug', $item_id); }

            $fieldset_id = $form->addFieldset( LANG_SLUG );
            $form->addField($fieldset_id, new fieldString('slug', array(
                'prefix' => '/'.((!$this->cms_config->ctype_default || !in_array($ctype['name'], $this->cms_config->ctype_default)) ? $ctype['name'].'/' : ''),
                'suffix' => '.html',
                'rules' => $slug_field_rules
            )));

        }

        // Если разрешено управление видимостью, то добавляем поле
        if (cmsUser::isAllowed($ctype['name'], 'privacy')) {

            $fieldset_id = $form->addFieldset( LANG_PRIVACY, 'privacy_wrap', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('privacy_wrap', $ctype['options']['is_collapsed'])));

            $items = array(
                0 => LANG_PRIVACY_PUBLIC
            );

            $privacy_types = cmsEventsManager::hookAll('content_privacy_types', array($ctype, $fields, $action, $item));

            if (is_array($privacy_types)){
                foreach($privacy_types as $privacy_type){
                    foreach($privacy_type['types'] as $name => $title){
                        $items[$name] = $title;
                    }
                    if(!empty($privacy_type['fields'])){
                        foreach ($privacy_type['fields'] as $privacy_field) {
                            $form->addField($fieldset_id, $privacy_field);
                        }
                    }
                }
            }

            if(count($items) > 1){
                $form->addFieldToBeginning($fieldset_id, new fieldList('is_private', array(
                    'items' => $items,
                    'rules' => array(array('number'))
                )));
            }

        }

        //
        // Если ручной ввод ключевых слов или описания, то добавляем поля для этого
        //
        if (!empty($ctype['options']['is_manual_title']) || !$ctype['is_auto_keys'] || !$ctype['is_auto_desc']){
            $fieldset_id = $form->addFieldset( LANG_SEO, 'seo_wrap', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('seo_wrap', $ctype['options']['is_collapsed'])));
            if ($ctype['options']['is_manual_title']){
                $form->addField($fieldset_id, new fieldString('seo_title', array(
                    'title' => LANG_SEO_TITLE,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
            if (!$ctype['is_auto_keys']){
                $form->addField($fieldset_id, new fieldString('seo_keys', array(
                    'title' => LANG_SEO_KEYS,
                    'hint' => LANG_SEO_KEYS_HINT,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
            if (!$ctype['is_auto_desc']){
                $form->addField($fieldset_id, new fieldText('seo_desc', array(
                    'title' => LANG_SEO_DESC,
                    'hint' => LANG_SEO_DESC_HINT,
                    'is_strip_tags' => true,
                    'options'=>array(
                        'max_length'=> 256,
                        'show_symbol_count'=>true
                    )
                )));
            }
        }

        //
        // Если включен выбор даты публикации, то добавляем поля
        //
		$pub_fieldset_id   = false;
        $is_dates          = $ctype['is_date_range'];
        $is_pub_start_date = cmsUser::isAllowed($ctype['name'], 'pub_late');
        $is_pub_end_date   = cmsUser::isAllowed($ctype['name'], 'pub_long', 'any');
        $is_pub_end_days   = cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');
        $is_pub_control    = cmsUser::isAllowed($ctype['name'], 'pub_on');
        $is_pub_ext        = cmsUser::isAllowed($ctype['name'], 'pub_max_ext');
        $pub_max_days      = intval(cmsUser::getPermissionValue($ctype['name'], 'pub_max_days'));

        if ($this->cms_user->is_admin){ $is_pub_end_days = false; }

        $is_pub_collapsed = !empty($ctype['options']['is_collapsed']) && in_array('pub_wrap', $ctype['options']['is_collapsed']);

		if ($is_pub_control){
			$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB, 'pub_wrap', array('is_collapsed' => $is_pub_collapsed));
			$form->addField($pub_fieldset_id, new fieldList('is_pub', array(
				'title' => sprintf(LANG_CONTENT_IS_PUB, $ctype['labels']['create']),
				'default' => 1,
				'items' => array(
					1 => LANG_YES,
					0 => LANG_NO
				)
			)));
		}

        if ($is_dates){
			if ($is_pub_start_date){
				$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB, 'pub_wrap', array('is_collapsed' => $is_pub_collapsed));
                $m = date('i');
				$form->addField($pub_fieldset_id, new fieldDate('date_pub', array(
					'title' => LANG_CONTENT_DATE_PUB,
					'default' => date('Y-m-d H:') . ($m - ($m % 5)),
					'options' => array(
						'show_time' => true
					),
					'rules' => array(
						array('required')
					)
				)));
			}
			if ($is_pub_end_date){
				$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB, 'pub_wrap', array('is_collapsed' => $is_pub_collapsed));
				$form->addField($pub_fieldset_id, new fieldDate('date_pub_end', array(
					'title' => LANG_CONTENT_DATE_PUB_END,
					'hint' => LANG_CONTENT_DATE_PUB_END_HINT,
					'options' => array(
						'show_time' => true
					)
				)));
			}
            if($action=='edit'){
                $is_expired = (strtotime($item['date_pub_end']) - time()) <= 0;
            }
			if (($action=='add' && $is_pub_end_days) || ($action=='edit' && $is_expired && $is_pub_ext && $is_pub_end_days)){
				$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB, 'pub_wrap', array('is_collapsed' => $is_pub_collapsed));
				$title = $action=='add' ? LANG_CONTENT_PUB_LONG : LANG_CONTENT_PUB_LONG_EXT;
				$hint = $action=='add'? false : sprintf(LANG_CONTENT_PUB_LONG_NOW, html_date($item['date_pub_end']));
				if ($pub_max_days){
					$days = array();
                    $rules = array();
                    if ($action == 'add'){ $rules[] = array('required'); $min = 1; }
                    if ($action == 'edit'){ $min = 0; }
                    $rules[] = array('number');
                    $rules[] = array('min', $min);
                    $rules[] = array('max', $pub_max_days);
                    for($d=$pub_max_days; $d>=$min; $d--) { $days[$d] = $d; }
					$form->addField($pub_fieldset_id, new fieldList('pub_days', array(
						'title'   => $title,
                        'hint'    => $hint,
                        'default' => $pub_max_days,
                        'items'   => $days,
                        'rules'   => $rules
                    )));
				} else {
                    $rules = array();
                    if ($action == 'add'){ $rules[] = array('required'); $min = 1; }
                    if ($action == 'edit'){ $min = 0; }
                    $rules[] = array('min', $min);
                    $rules[] = array('max', 65535);
					$form->addField($pub_fieldset_id, new fieldNumber('pub_days', array(
						'title' => $title,
						'default' => 10,
                        'rules' => $rules
					)));
				}
			}
		}

        // выбор шаблона записи
        if($this->cms_user->is_admin){

            $styles = $this->cms_template->getAvailableContentItemStyles($ctype['name']);

            if($styles){

                $fieldset_id = $form->addFieldset(LANG_CONTENT_TEMPLATE, 'template_item', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('template_item', $ctype['options']['is_collapsed'])));

                $form->addField($fieldset_id, new fieldList('template', array(
                    'items' => array('' => LANG_BY_DEFAULT) + $styles
                )));

            }

        }

        list($form, $item, $ctype) = cmsEventsManager::hook('content_item_form', array($form, $item, $ctype), null, $this->request);

        return $form;

    }

    public function getFormCategories($ctype_name) {

        $level_offset   = 0;
        $last_header_id = false;
        $items          = array('' => '');

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if(!$ctype){ return $items; }

        $tree = $this->model->limit(0)->getCategoriesTree($ctype_name);
        if(!$tree){ return $items; }

        foreach($tree as $c){

            if(!empty($c['allow_add']) &&  !$this->cms_user->isInGroups($c['allow_add'])){
                continue;
            }

            if ($ctype['options']['is_cats_only_last']){

                $dash_pad = $c['ns_level']-1 >= 0 ? str_repeat('-', $c['ns_level']-1) . ' ' : '';

                if ($c['ns_right']-$c['ns_left'] == 1){
                    if ($last_header_id !== false && $last_header_id != $c['parent_id']){
                        $items['opt'.$c['id']] = array(str_repeat('-', $c['ns_level']-1).' '.$c['title']);
                    }
                    $items[$c['id']] = $dash_pad . $c['title'];
                } else if ($c['parent_id']>0) {
                    $items['opt'.$c['id']] = array($dash_pad.$c['title']);
                    $last_header_id = $c['id'];
                }

                continue;

            }

            if (!$ctype['options']['is_cats_only_last']){

                if ($c['parent_id']==0 && !$ctype['options']['is_cats_open_root']){ $level_offset = 1; continue; }

                $items[$c['id']] = str_repeat('-- ', $c['ns_level']-$level_offset).' '.$c['title'];

                continue;

            }

        }

        return $items;

    }

    public function getPropsFields($props){

        $fields = array();

        if (!is_array($props)) { return $fields; }

        foreach($props as $prop) {

            $prop['rules'] = [];
            $prop['default'] = $prop['values'];

            if (!empty($prop['options']['is_required'])) { $prop['rules'][] = [('required')]; }
            if (!empty($prop['options']['is_filter_multi'])){ $prop['options']['filter_multiple'] = 1; }
            if (!empty($prop['options']['is_filter_range'])){ $prop['options']['filter_range'] = 1; }

            switch($prop['type']){
                case 'list_multiple':
                    $prop['type'] = 'listbitmask';
                    break;
            }

            $field_class = 'field' . string_to_camel('_', $prop['type']);

            $field = new $field_class('props:'.$prop['id']);

            $field->setOptions($prop);

            $fields[$prop['id']] = $field;

        }

        return $fields;

    }

//============================================================================//
//============================================================================//

    public function bindItemToParents($ctype, $item, $parents = false){

        if (!$parents){
            $parents = $this->model->getContentTypeParents($ctype['id']);
        }

        foreach($parents as $parent){

            $this->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);

            $value = isset($item[$parent['id_param_name']]) ? $item[$parent['id_param_name']] : '';

            $ids = array();

            foreach(explode(',', $value) as $id){
                if (!trim($id)) { continue; }
                $ids[] = trim($id);
            }

            $parent_ctype = $this->model->getContentTypeByName($parent['ctype_name']);

            $current_parents   = array();
            $new_parents       = array();
            $parents_to_delete = array();
            $parents_to_add    = array();

            if (!empty($item['id'])){
                $current_parents = $this->model->getContentItemParents($parent_ctype, $ctype, $item['id']);
            }

            if ($ids){
                $this->model->filterIn('id', $ids);
                $new_parents = $this->model->getContentItems($parent['ctype_name']);
            }

            if ($current_parents){
                foreach($current_parents as $id => $current_parent){
                    if (isset($new_parents[$id])) { continue; }
                    if (!in_array($id, $parents_to_delete)){
                        $parents_to_delete[] = $id;
                    }
                }
            }

            if ($new_parents){
                foreach($new_parents as $id => $new_parent){
                    if (isset($current_parents[$id])) { continue; }
                    if (!in_array($id, $parents_to_add)){
                        $parents_to_add[] = $id;
                    }
                }
            }

            if($parent['target_controller'] != 'content'){
                $this->model->setTablePrefix('');
            } else {
                $this->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);
            }

            if ($parents_to_add){
                foreach ($parents_to_add as $new_parent_id){

                    $this->model->bindContentItemRelation(array(
                        'parent_ctype_name' => $parent_ctype['name'],
                        'parent_ctype_id'   => $parent_ctype['id'],
                        'parent_item_id'    => $new_parent_id,
                        'child_ctype_name'  => $ctype['name'],
                        'child_ctype_id'    => $ctype['id'],
                        'child_item_id'     => $item['id'],
                        'target_controller' => $parent['target_controller']
                    ));

                }
            }

            if ($parents_to_delete){
                foreach ($parents_to_delete as $old_parent_id){

                    $this->model->unbindContentItemRelation(array(
                        'parent_ctype_name' => $parent_ctype['name'],
                        'parent_ctype_id'   => $parent_ctype['id'],
                        'parent_item_id'    => $old_parent_id,
                        'child_ctype_name'  => $ctype['name'],
                        'child_ctype_id'    => $ctype['id'],
                        'child_item_id'     => $item['id'],
                        'target_controller' => $parent['target_controller']
                    ));

                }
            }

        }

    }

    public function prepareItemSeo($item, $fields, $ctype) {

        list($ctype, $fields, $item) = cmsEventsManager::hook('prepare_item_seo', array($ctype, $fields, $item));

        $_item = $item;

        foreach ($fields as $field) {

            if (!isset($item[$field['name']])) { $_item[$field['name']] = null;  continue; }

            if (empty($item[$field['name']]) && $item[$field['name']] !== '0') {
                $_item[$field['name']] = null; continue;
            }

            if(isset($field['string_value'])){
                $_item[$field['name']] = strip_tags($field['string_value']);
            } else {
                $_item[$field['name']] = strip_tags($field['handler']->setItem($item)->getStringValue($item[$field['name']]));
            }

        }

        if(!empty($item['tags']) && is_array($item['tags'])){
            $_item['tags'] = implode(', ', $item['tags']);
        }

        if(!isset($item['category']) && !empty($item['category_id'])){
            $item['category'] = $this->model->getCategory($ctype['name'], $item['category_id']);
        }

        if(!empty($item['category']['title'])){
            $_item['category'] = $item['category']['title'];
        } else {
            $_item['category'] = null;
        }

        return $_item;

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
            'ctype_title'       => (empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list']),
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

        if(!empty($category['id'])){

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
        if(!empty($category['seo_h1'])){

            $h1_str = $category['seo_h1'];

            // задан набор и он не первый
            if (!empty($dataset['title']) && empty($dataset['first_ds'])){
                $h1_str .= ' / '.$dataset['title'];
            }

            // убираем обработку значений
            $this->cms_template->setPageH1Item(null);

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
        if (!empty($category['seo_title'])){

            $title_str = $category['seo_title'];

            // задан набор и он не первый
            if (!empty($dataset['title']) && empty($dataset['first_ds'])){
                $title_str .= ' / '.$dataset['title'];
            }

            // если есть фильтр
            if(!empty($meta_item['filter_string'])){
                $title_str .= ', '.$meta_item['filter_string'];
            }

            // убираем обработку значений
            $this->cms_template->setPageTitleItem(null);

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
        if (!empty($category['seo_keys'])){

            $keys_str = $category['seo_keys'];

            // задан набор и он не первый
            if (!empty($dataset['title']) && empty($dataset['first_ds'])){
                $keys_str .= ', '.$dataset['title'];
            }

            // если есть фильтр
            if(!empty($meta_item['filter_string'])){
                $keys_str .= ', '.$meta_item['filter_string'];
            }

            $this->cms_template->setPageKeywordsItem(null);

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
        if (!empty($category['seo_desc'])){

            $desc_str = $category['seo_desc'];

            // задан набор и он не первый
            if (!empty($dataset['title']) && empty($dataset['first_ds'])){
                $desc_str .= ', '.$dataset['title'];
            }

            // если есть фильтр
            if(!empty($meta_item['filter_string'])){
                $desc_str .= ': '.$meta_item['filter_string'];
            }

            $this->cms_template->setPageDescriptionItem(null);

        }

        $this->cms_template->setPageH1($h1_str);
        $this->cms_template->setPageTitle($title_str);
        $this->cms_template->setPageKeywords($keys_str);
        $this->cms_template->setPageDescription($desc_str);

        return array(
            'meta_item' => $meta_item,
            'h1_str'    => $h1_str,
            'title_str' => $title_str,
            'keys_str'  => $keys_str,
            'desc_str'  => $desc_str
        );

    }

    public function getCtypeDatasets($ctype, $params) {

        $first_ds = false;

        $list_type = $this->getListContext();

        $datasets = $this->model->getContentDatasets($ctype['id'], true, function ($item, $model) use ($params, $list_type, $first_ds) {

            $is_view = !$item['cats_view'] || in_array($params['cat_id'], $item['cats_view']);
            $is_user_hide = $item['cats_hide'] && in_array($params['cat_id'], $item['cats_hide']);

            if (!$is_view || $is_user_hide) { return false; }

            $is_view = empty($item['list']['show']) || in_array($list_type, $item['list']['show']);
            $is_user_hide = !empty($item['list']['hide']) && in_array($list_type, $item['list']['hide']);

            if (!$is_view || $is_user_hide) { return false; }

            $item['title'] = string_replace_user_properties($item['title']);

            $item['first_ds'] = $first_ds ? false : true; $first_ds = true;

            return $item;

        });

        list($datasets, $ctype) = cmsEventsManager::hook('content_datasets', array($datasets, $ctype));
        list($datasets, $ctype) = cmsEventsManager::hook('content_'.$ctype['name'].'_datasets', array($datasets, $ctype));

        return $datasets;

    }

    public function getContentTypeForModeration($name){

        if(is_numeric($name)){
            $ctype = $this->model->getContentType($name);
        } else {
            $ctype = $this->model->getContentTypeByName($name);
        }

        return $ctype;

    }

    public function validate_rating_score($score) {
        return $score >= 1 && $score <= 5;
    }

}
