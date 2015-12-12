<?php
class content extends cmsFrontend {

    const perpage = 15;

//============================================================================//
//============================================================================//

    public function before($action_name) {

        parent::before($action_name);

        $core = cmsCore::getInstance();

    }

//============================================================================//
//============================================================================//

    public function route($uri){

        $core = cmsCore::getInstance();

        $action_name = $this->parseRoute($core->uri);

        if (!$action_name) { cmsCore::error404(); }

        $this->runAction($action_name);

    }

	public function parseRoute($uri){

		$config = cmsConfig::getInstance();

		$action_name = parent::parseRoute($uri);

		if (!$action_name && $config->ctype_default){
			$action_name = parent::parseRoute($config->ctype_default . '/' . $uri);
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

    public function renderItemsList($ctype, $page_url, $hide_filter=false, $category_id=0, $filters = array(), $dataset=false){

        $user = cmsUser::getInstance();

        // Получаем поля для данного типа контента
        $fields = cmsCore::getModel('content')->getContentFields($ctype['name']);

        $page = $this->request->get('page', 1);

        $perpage = self::perpage;

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

			$value = $this->request->get($name);
			if (!$value) { continue; }

			$this->model = $field['handler']->applyFilter($this->model, $value);
			$filters[$name] = $value;

		}

		// проверяем запросы фильтрации по свойствам
		if (isset($props) && is_array($props)){
			foreach($props as $prop){

				$name = "p{$prop['id']}";

				if (!$prop['is_in_filter']) { continue; }
				if (!$this->request->has($name)){ continue; }

				$value = $this->request->get($name);
				if (!$value) { continue; }

				$this->model->filterPropValue($ctype['name'], $prop, $value);
				$filters[$name] = $value;

			}
		}

        // Приватность
        // флаг показа только названий
        $hide_except_title = (!empty($ctype['options']['privacy_type']) && $ctype['options']['privacy_type'] == 'show_title');

        // Сначала проверяем настройки типа контента
        if (!empty($ctype['options']['privacy_type']) && in_array($ctype['options']['privacy_type'], array('show_title', 'show_all'), true)) {
            $this->model->disablePrivacyFilter();
            if($ctype['options']['privacy_type'] != 'show_title'){
                $hide_except_title = false;
            }
        }

        // А потом, если разрешено правами доступа, отключаем фильтр приватности
        if (cmsUser::isAllowed($ctype['name'], 'view_all')) {
            $this->model->disablePrivacyFilter(); $hide_except_title = false;
        }

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

		list($ctype, $this->model) = cmsEventsManager::hook('content_list_filter', array($ctype, $this->model));
		list($ctype, $this->model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", array($ctype, $this->model));

        // Получаем количество и список записей
        $total = $this->model->getContentItemsCount($ctype['name']);
        $items = $this->model->getContentItems($ctype['name']);

        // если запрос через URL
        if($this->request->isStandard()){
            if(!$items && $page > 1){ cmsCore::error404(); }
        }

        // Рейтинг
        if ($ctype['is_rating'] && $items &&  $this->isControllerEnabled('rating')){

            $rating_controller = cmsCore::getController('rating', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => $ctype['name']
            ), cmsRequest::CTX_INTERNAL));

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate');

            foreach($items as $id=>$item){
                $is_rating_enabled = $is_rating_allowed && ($item['user_id'] != $user->id);
                $items[$id]['rating_widget'] = $rating_controller->getWidget($item['id'], $item['rating'], $is_rating_enabled);
            }

        }

        list($ctype, $items) = cmsEventsManager::hook("content_before_list", array($ctype, $items));
        list($ctype, $items) = cmsEventsManager::hook("content_{$ctype['name']}_before_list", array($ctype, $items));

        $template = cmsTemplate::getInstance();

        $template->setContext($this);

        $html = $template->renderContentList($ctype, array(
			'category_id'       => $category_id,
            'page_url'          => $page_url,
            'ctype'             => $ctype,
            'fields'            => $fields,
            'props'             => isset($props) ? $props : false,
            'props_fields'      => isset($props_fields) ? $props_fields : false,
            'filters'           => $filters,
            'page'              => $page,
            'perpage'           => $perpage,
            'total'             => $total,
            'items'             => $items,
            'user'              => $user,
            'dataset'           => $dataset,
            'hide_except_title' => $hide_except_title
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $template->restoreContext();

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
            'title_subject' => $ctype['title'],
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
            'title_subject' => $ctype['title'],
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            ),
            'url_mask_not' => array(
                "{$ctype['name']}/*.html",
                "{$ctype['name']}/add",
                "{$ctype['name']}/edit/*",
            )
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.item",
            'title_const' => 'LANG_WP_CONTENT_ITEM',
            'title_subject' => $ctype['title'],
            'url_mask' => "{$ctype['name']}/*.html"
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.edit",
            'title_const' => 'LANG_WP_CONTENT_ITEM_EDIT',
            'title_subject' => $ctype['title'],
            'url_mask' => array(
                "{$ctype['name']}/add",
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
        $to = array('email' => $moderator['email'], 'name' => $moderator['nickname']);
        $letter = array('name' => 'moderation');

        $messenger->sendEmail($to, $letter, array(
            'moderator' => $moderator['nickname'],
            'author' => $author['nickname'],
            'author_url' => href_to_abs('users', $author['id']),
            'page_title' => $item['title'],
            'page_url' => href_to_abs($ctype_name, $item['slug'] . ".html"),
            'date' => html_date_time(),
        ));

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
					'rules' => array(
						array('max_length', 256)
					)
                )));
            }
            if (!empty($ctype['options']['is_cats_keys'])){
                $form->addField($fieldset_id, new fieldString('seo_keys', array(
                    'title' => LANG_SEO_KEYS,
                    'hint' => LANG_SEO_KEYS_HINT,
					'rules' => array(
						array('max_length', 256)
					)
                )));
            }
            if (!empty($ctype['options']['is_cats_desc'])){
                $form->addField($fieldset_id, new fieldText('seo_desc', array(
                    'title' => LANG_SEO_DESC,
                    'hint' => LANG_SEO_DESC_HINT,
					'rules' => array(
						array('max_length', 256)
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

        return $form;

    }

//============================================================================//
//============================================================================//

    public function getItemForm($ctype, $fields, $action, $data=array(), $item_id=false, $item=false){

        $user = cmsUser::getInstance();

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

                            $content_model = cmsCore::getModel('content');
                            $ctype = $content_model->getContentTypeByName($item['ctype_name']);
                            $tree = $content_model->getCategoriesTree($item['ctype_name']);
                            $level_offset = 0;
                            $last_header_id = false;
                            $items = array('' => LANG_CONTENT_SELECT_CATEGORY );

                            if ($tree){
                                foreach($tree as $c){

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
            $fieldset_id = $form->addFieldset(LANG_FOLDER, 'folder');
            $folders = array('0'=>LANG_CONTENT_SELECT_FOLDER);
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
        if ($action == 'add' && $groups_list && $groups_list != array('0'=>'')){

            $fieldset_id = $form->addFieldset(LANG_GROUP);
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

            $fieldset_id = $form->addFieldset($fieldset['title']);

            foreach($fieldset['fields'] as $field){

                // добавляем поле в форму
                $form->addField($fieldset_id, $field['handler']);

            }

        }

        //
        // Если включены теги, то добавляем поле для них
        //
        if ($ctype['is_tags']){
            $fieldset_id = $form->addFieldset(LANG_TAGS);
            $form->addField($fieldset_id, new fieldString('tags', array(
                'hint' => LANG_TAGS_HINT,
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

            $fieldset_id = $form->addFieldset( LANG_PRIVACY );
            $form->addField($fieldset_id, new fieldList('is_private', array(
                'items' => array(
                    0 => LANG_PRIVACY_PUBLIC,
                    1 => LANG_PRIVACY_PRIVATE,
                ),
                'rules' => array( array('number') )
            )));

        }

        // если разрешено отключать комментарии к записи
        if(cmsUser::isAllowed($ctype['name'], 'disable_comments') && $ctype['is_comments']){

            $fieldset_id = $form->addFieldset(LANG_RULE_CONTENT_COMMENT, 'is_comment');
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
            $fieldset_id = $form->addFieldset( LANG_SEO );
            if ($ctype['options']['is_manual_title']){
                $form->addField($fieldset_id, new fieldString('seo_title', array(
                    'title' => LANG_SEO_TITLE,
					'rules' => array(
						array('max_length', 256)
					)
                )));
            }
            if (!$ctype['is_auto_keys']){
                $form->addField($fieldset_id, new fieldString('seo_keys', array(
                    'title' => LANG_SEO_KEYS,
                    'hint' => LANG_SEO_KEYS_HINT,
					'rules' => array(
						array('max_length', 256)
					)
                )));
            }
            if (!$ctype['is_auto_desc']){
                $form->addField($fieldset_id, new fieldText('seo_desc', array(
                    'title' => LANG_SEO_DESC,
                    'hint' => LANG_SEO_DESC_HINT,
					'rules' => array(
						array('max_length', 256)
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

        if ($user->is_admin){ $is_pub_end_days = false; }

		if ($is_pub_control){
			$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB );
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
				$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB );
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
				$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB );
				$form->addField($pub_fieldset_id, new fieldDate('date_pub_end', array(
					'title' => LANG_CONTENT_DATE_PUB_END,
					'hint' => LANG_CONTENT_DATE_PUB_END_HINT,
				)));
			}
			if (($action=='add' && $is_pub_end_days) || ($action=='edit' && $is_pub_ext && $is_pub_end_days)){
				$pub_fieldset_id = $pub_fieldset_id ? $pub_fieldset_id : $form->addFieldset( LANG_CONTENT_PUB );
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
                    if ($action == 'add'){ $rules[] = array('required'); $min = 1; }
                    if ($action == 'edit'){ $min = 0; }
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

                    break;

            }

            $fields[$prop['id']] = $field;

        }

        return $fields;

    }

//============================================================================//
//============================================================================//

}
