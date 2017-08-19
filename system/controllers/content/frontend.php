<?php
class content extends cmsFrontend {

    const perpage = 15;

    public $max_items_count = 0;

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
			$action_name = parent::parseRoute($this->cms_config->ctype_default . '/' . $uri);
		}

		return $action_name;

	}

//============================================================================//
//============================================================================//

    public function getMenuAddItems($menu_item_id){

        $result = array('url' => '#', 'items' => false);

        $ctypes = $this->model->getContentTypes();

        if (!$ctypes) { return $result; }

        foreach($ctypes as $id=>$ctype){

            if (!cmsUser::isAllowed($ctype['name'], 'add')) { continue; }

            if (!empty($ctype['labels']['create'])){

                $result['items'][] = array(
                    'id' => 'content_add' . $ctype['id'],
                    'parent_id' =>  $menu_item_id,
                    'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                    'childs_count' => 0,
                    'url' => href_to($ctype['name'], 'add')
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
                'id' => 'private_list'.$ctype['id'],
                'parent_id' =>  $menu_item_id,
                'title' => sprintf(LANG_CONTENT_PRIVATE_FRIEND_ITEMS, mb_strtolower($ctype['title'])),
                'childs_count' => 0,
                'url' => href_to($ctype['name'], 'from_friends')
            );

        }

        return $result;

    }


//============================================================================//

    public function getMenuCategoriesItems($menu_item_id, $ctype){

        $result = array('url' => href_to($ctype['name']), 'items' => false);

        if (!$ctype['is_cats']) { return $result; }

        $tree = $this->model->getCategoriesTree($ctype['name']);

        if (!$tree) { return $result; }

        foreach($tree as $id=>$cat){

            if ($cat['id']==1) { continue; }

            $item_id = "content.{$ctype['name']}.{$cat['id']}";
            $parent_id = "content.{$ctype['name']}.{$cat['parent_id']}";

            $result['items'][] = array(
                'id' => $item_id,
                'parent_id' =>  $cat['parent_id']==1 ?
                                $menu_item_id :
                                $parent_id,
                'title' => $cat['title'],
                'childs_count' => ($cat['ns_right'] - $cat['ns_left']) -1,
                'url' => href_to($ctype['name'], $cat['slug'])
            );

        }

        return $result;

    }

//============================================================================//
//============================================================================//

    public function renderItemsList($ctype, $page_url, $hide_filter=false, $category_id=0, $filters = array(), $dataset=false, $ext_hidden_params=array()){

        $props = $props_fields = false;

        // Получаем поля для данного типа контента
        $fields = cmsCore::getModel('content')->getContentFields($ctype['name']);

        $page = $this->request->get('page', 1);

        $perpage = (empty($ctype['options']['limit']) ? self::perpage : $ctype['options']['limit']);

        if ($hide_filter) { $ctype['options']['list_show_filter'] = false; }

        if ($category_id && $category_id>1){
            // Получаем поля-свойства
            $props = cmsCore::getModel('content')->getContentProps($ctype['name'], $category_id);
            $props_fields = $this->getPropsFields($props);
        }

		// проверяем запросы фильтрации по полям
		foreach($fields as $name => $field){

			if (!$field['is_in_filter']) { continue; }
			if (!$this->request->has($name)){ continue; }

			$value = $this->request->get($name, false, $field['handler']->getDefaultVarType(true));
			if (!$value) { continue; }

			if($field['handler']->applyFilter($this->model, $value) !== false){
                $filters[$name] = $value;
            }

		}

		// проверяем запросы фильтрации по свойствам
		if (isset($props) && is_array($props)){
			foreach($props as $prop){

				$name = "p{$prop['id']}";

				if (!$prop['is_in_filter']) { continue; }
				if (!$this->request->has($name)){ continue; }

                $prop['handler'] = $props_fields[$prop['id']];

				$value = $this->request->get($name, false, $prop['handler']->getDefaultVarType(true));
				if (!$value) { continue; }

				if($this->model->filterPropValue($ctype['name'], $prop, $value) !== false){

                    $filters[$name] = $value;

                }

			}
		}

        // применяем приватность
        // флаг показа только названий
        $hide_except_title = $this->model->applyPrivacyFilter($ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

		list($ctype, $this->model) = cmsEventsManager::hook('content_list_filter', array($ctype, $this->model));
		list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", array($ctype, $this->model));

        // Получаем количество и список записей
        $total = $this->model->getContentItemsCount($ctype['name']);
        $items = $this->model->getContentItems($ctype['name'], function ($item, $model, $ctype_name, $user)
                use ($ctype, $hide_except_title, $fields){

            $item['ctype'] = $ctype;
            $item['ctype_name'] = $ctype['name'];
            $item['is_private_item'] = $item['is_private'] && $hide_except_title;
            $item['private_item_hint'] = LANG_PRIVACY_HINT;

            // для приватности друзей
            // другие проверки приватности (например для групп) в хуках content_before_list
            if($item['is_private'] == 1){
                $item['is_private_item'] = $item['is_private_item'] && !$item['user']['is_friend'];
                $item['private_item_hint'] = LANG_PRIVACY_PRIVATE_HINT;
            }

            // строим поля списка
            foreach($fields as $field){

                if ($field['is_system'] || !$field['is_in_list'] || !isset($item[$field['name']])) { continue; }
                if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { continue; }
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
                    'title'     => $field['title'],
                    'html'      => $field_html
                );

            }

            return $item;

        });
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

        list($ctype, $items) = cmsEventsManager::hook('content_before_list', array($ctype, $items));
        list($ctype, $items) = cmsEventsManager::hook("content_{$ctype['name']}_before_list", array($ctype, $items));

        cmsModel::cacheResult('current_ctype_fields', $fields);
        cmsModel::cacheResult('current_ctype_props', $props);
        cmsModel::cacheResult('current_ctype_props_fields', $props_fields);

        $this->cms_template->setContext($this);

        $html = $this->cms_template->renderContentList($ctype, array(
			'category_id'       => $category_id,
            'page_url'          => $page_url,
            'ctype'             => $ctype,
            'fields'            => $fields,
            'props'             => $props,
            'props_fields'      => $props_fields,
            'filters'           => $filters,
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

    public function addWidgetsPages($ctype){

        $widgets_model = cmsCore::getModel('widgets');

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.all",
            'title_const' => 'LANG_WP_CONTENT_ALL_PAGES',
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            )
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.list",
            'title_const' => 'LANG_WP_CONTENT_LIST',
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            ),
            'url_mask_not' => array(
                "{$ctype['name']}/*/view-*",
                "{$ctype['name']}/*.html",
                "{$ctype['name']}/add",
                "{$ctype['name']}/add/%",
                "{$ctype['name']}/addcat",
                "{$ctype['name']}/addcat/%",
                "{$ctype['name']}/editcat/%",
                "{$ctype['name']}/edit/*",
            )
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.item",
            'title_const' => 'LANG_WP_CONTENT_ITEM',
            'url_mask' => "{$ctype['name']}/*.html"
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.edit",
            'title_const' => 'LANG_WP_CONTENT_ITEM_EDIT',
            'url_mask' => array(
                "{$ctype['name']}/add",
                "{$ctype['name']}/add/%",
                "{$ctype['name']}/edit/*"
            )
        ));

        return true;

    }

//============================================================================//
//============================================================================//

    public function requestModeration($ctype_name, $item, $is_new_item=true){

        $moderator_id = $this->model->getNextModeratorId($ctype_name);

        $users_model = cmsCore::getModel('users');

        $moderator = $users_model->getUser($moderator_id);
        $author = $users_model->getUser($item['user_id']);

        // добавляем задачу модератору
        $this->model->addModeratorTask($ctype_name, $moderator_id, $is_new_item, $item);

        // отправляем письмо модератору
        $messenger = cmsCore::getController('messages');

        // личное сообщение
        if($moderator['is_online']){
            $messenger->addRecipient($moderator['id'])->sendNoticePM(array(
                'content' => LANG_MODERATION_NOTIFY,
                'actions' => array(
                    'view' => array(
                        'title' => LANG_SHOW,
                        'href'  => href_to($ctype_name, $item['slug'] . '.html')
                    )
                )
            ));
        }

        // EMAIL уведомление, если не онлайн
        if(!$moderator['is_online']){

            $to = array('email' => $moderator['email'], 'name' => $moderator['nickname']);
            $letter = array('name' => 'moderation');

            $messenger->sendEmail($to, $letter, array(
                'moderator'  => $moderator['nickname'],
                'author'     => $author['nickname'],
                'author_url' => href_to_abs('users', $author['id']),
                'page_title' => $item['title'],
                'page_url'   => href_to_abs($ctype_name, $item['slug'] . '.html'),
                'date'       => html_date_time()
            ));

        }

        cmsUser::addSessionMessage(sprintf(LANG_MODERATION_IDLE, $moderator['nickname']), 'info');

    }

//============================================================================//
//============================================================================//

    public function getCategoryForm($ctype, $action){

        $form = $this->getForm('category');

        // Если ручной ввод ключевых слов или описания, то добавляем поля для этого
        if (!empty($ctype['options']['is_cats_title']) || $ctype['options']['is_cats_keys'] || $ctype['options']['is_cats_desc']){
            $fieldset_id = $form->addFieldset( LANG_SEO );
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
            $fieldset_id = $form->addFieldset(LANG_CATEGORY, 'category');
            $form->addField($fieldset_id,
                new fieldList('category_id', array(
                        'rules' => array(
                            array('required')
                        ),
                        'generator' => function($item){

                            $user = cmsUser::getInstance();

                            $content_model = cmsCore::getModel('content');
                            $ctype = $content_model->getContentTypeByName($item['ctype_name']);
                            $tree = $content_model->limit(0)->getCategoriesTree($item['ctype_name']);
                            $level_offset = 0;
                            $last_header_id = false;
                            $items = array('' => '' );

                            if ($tree){
                                foreach($tree as $c){

                                    if(!empty($c['allow_add']) &&  !$user->isInGroups($c['allow_add'])){
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
                            }

                            return $items;

                        }
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
            if ($folders_list) { $folders = $folders + $folders_list; }
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
        if ($ctype['props']){
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
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user){

            // пропускаем системные поля
            if ($field['is_system']) { return false; }

            // проверяем что группа пользователя имеет доступ к редактированию этого поля
            if ($field['groups_edit'] && !$user->isInGroups($field['groups_edit'])) { return false; }

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

        //
        // Если включены теги, то добавляем поле для них
        //
        if ($ctype['is_tags']){
            $fieldset_id = $form->addFieldset(LANG_TAGS, 'tags_wrap', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('tags_wrap', $ctype['options']['is_collapsed'])));
            $form->addField($fieldset_id, new fieldString('tags', array(
                'hint' => LANG_TAGS_HINT,
                'options'=>array(
                    'max_length'=> 1000,
                    'show_symbol_count'=>true
                ),
                'autocomplete' => array(
                    'multiple' => true,
                    'url' => href_to('tags', 'autocomplete')
                )
            )));
        }

        // Если ручной ввод SLUG, то добавляем поле для этого
        if (!$ctype['is_auto_url']){

			$slug_field_rules = array( array('required'), array('slug') );

			if ($action == 'add'){ $slug_field_rules[] = array('unique', $this->model->table_prefix . $ctype['name'], 'slug'); }
			if ($action == 'edit'){ $slug_field_rules[] = array('unique_exclude', $this->model->table_prefix . $ctype['name'], 'slug', $item_id); }

            $fieldset_id = $form->addFieldset( LANG_SLUG );
            $form->addField($fieldset_id, new fieldString('slug', array(
                'prefix' => '/'.$ctype['name'].'/',
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

        // если разрешено отключать комментарии к записи
        if(cmsUser::isAllowed($ctype['name'], 'disable_comments') && $ctype['is_comments']){

            $fieldset_id = $form->addFieldset(LANG_RULE_CONTENT_COMMENT, 'is_comment', array('is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('is_comment', $ctype['options']['is_collapsed'])));
            $form->addField($fieldset_id, new fieldList('is_comments_on', array(
				'default' => 1,
				'items' => array(
					1 => LANG_YES,
					0 => LANG_NO
				)
			)));

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
                    for($d=$min; $d<=$pub_max_days; $d++) { $days[$d] = $d; }
					$form->addField($pub_fieldset_id, new fieldList('pub_days', array(
						'title' => $title,
						'hint' => $hint,
						'items' => $days,
						'rules' => $rules
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

        list($form, $item) = cmsEventsManager::hook('content_item_form', array($form, $item));

        return $form;

    }

    public function getPropsFields($props){

        $fields = array();

        if (!is_array($props)) { return $fields; }

        foreach($props as $prop) {

            $field_name = "props:{$prop['id']}";

            $rules = array();

            if (!empty($prop['options']['is_required'])) { $rules[] = array('required'); }

            switch($prop['type']){

                case 'list':

                    $rules[] = array('digits');

                    $field = new fieldList($field_name, array(
                        'title' => $prop['title'],
                        'items' => string_explode_list($prop['values']),
                        'rules' => $rules
                    ));

                    if (!empty($prop['options']['is_filter_multi'])){ $field->setOption('filter_multiple', true); }

                    break;

                case 'list_multiple':

                    $field = new fieldListBitmask($field_name, array(
                        'title' => $prop['title'],
                        'items' => string_explode_list($prop['values']),
                        'rules' => $rules
                    ));

                    break;

                case 'color':

                    $field = new fieldColor($field_name, array(
                        'title' => $prop['title'],
                        'rules' => $rules
                    ));

                    break;

                case 'checkbox':

                    $field = new fieldCheckbox($field_name, array(
                        'title' => $prop['title'],
                        'rules' => $rules
                    ));

                    break;

                case 'string':

                    $field = new fieldString($field_name, array(
                        'title' => $prop['title'],
                        'rules' => $rules
                    ));

                    break;

                case 'number':

                    $field = new fieldNumber($field_name, array(
                        'title' => $prop['title'],
                        'units' => !empty($prop['options']['units']) ? $prop['options']['units'] : false,
                        'rules' => $rules
                    ));

                    if (!empty($prop['options']['is_filter_range'])){ $field->setOption('filter_range', true); }

                    break;

            }

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

            $this->model->setTablePrefix('con_');

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
                $this->model->setTablePrefix('con_');
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

        $_item = $item;

        foreach ($fields as $field) {

            if ($field['groups_read'] && !$this->cms_user->isInGroups($field['groups_read'])) { $_item[$field['name']] = ''; continue; }

            if (!$field['is_in_item'] || !isset($item[$field['name']])) { $_item[$field['name']] = '';  continue; }

            if (empty($item[$field['name']]) && $item[$field['name']] !== '0') {
                $_item[$field['name']] = ''; continue;
            }

            $_item[$field['name']] = $field['handler']->setItem($item)->getStringValue($item[$field['name']]);

        }

        if(!isset($item['category']) && !empty($item['category_id'])){
            $item['category'] = $this->model->getCategory($ctype['name'], $item['category_id']);
        }

        if(!empty($item['category']['title'])){
            $_item['category'] = $item['category']['title'];
        } else {
            $_item['category'] = '';
        }

        return $_item;

    }

}
