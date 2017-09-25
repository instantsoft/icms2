<?php

class actionContentItemAdd extends cmsAction {

    public function run(){

        $back_url = $this->request->get('back', '');

        // Получаем название типа контента
        $ctype_name = $this->request->get('ctype_name', '');

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $permissions = cmsEventsManager::hook('content_add_permissions', array(
            'can_add' => false,
            'ctype'   => $ctype
        ));

        $is_check_parent_perm = false;

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype_name, 'add') && !$permissions['can_add']) {
            if (!cmsUser::isAllowed($ctype_name, 'add_to_parent')) {
                if(!$this->cms_user->is_logged){
                    cmsUser::goLogin();
                }
                cmsCore::error404();
            }
            $is_check_parent_perm = true;
        }

        // проверяем что не превышен лимит на число записей
        $user_items_count = $this->model->getUserContentItemsCount($ctype_name, $this->cms_user->id, false);

        if (cmsUser::isPermittedLimitReached($ctype_name, 'limit', $user_items_count)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_COUNT_LIMIT, $ctype['labels']['many']), 'error');
            $this->redirectBack();
        }

        // Проверяем ограничение по карме
        if (cmsUser::isPermittedLimitHigher($ctype_name, 'karma', $this->cms_user->karma)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_KARMA_LIMIT, cmsUser::getPermissionValue($ctype_name, 'karma')), 'error');
            $this->redirectBack();
        }

		$item = array();

        if ($ctype['is_cats']){
            $category_id = $this->request->get('to_id', 0);
        }

        // Определяем наличие полей-свойств
        $props = $this->model->getContentProps($ctype['name']);
        $ctype['props'] = $props;

        // Если этот контент можно создавать в группах (сообществах) то получаем список групп
        $groups_list = array();

        if ($ctype['is_in_groups'] || $ctype['is_in_groups_only']){

            $groups_model = cmsCore::getModel('groups');
            $groups = $groups_model->getUserGroups($this->cms_user->id);

            if (!$groups && $ctype['is_in_groups_only']){
                cmsUser::addSessionMessage(LANG_CONTENT_IS_IN_GROUPS_ONLY, 'error');
                $this->redirectBack();
            }

            $groups_list = ($ctype['is_in_groups_only']) ? array() : array('0'=>'');
            $groups_list = $groups_list + array_collection_to_list($groups, 'id', 'title');

            $group_id = $this->request->get('group_id', 0);
            // если вне групп добавление записей запрещено, даём выбор только одной группы
            if(!cmsUser::isAllowed($ctype_name, 'add') && isset($groups_list[$group_id])){
                $groups_list = array($group_id => $groups_list[$group_id]);
            }

        }

        // Если включены личные папки - получаем их список
        $folders_list = array();

        if ($ctype['is_folders']){
            $folders_list = $this->model->getContentFolders($ctype['id'], $this->cms_user->id);
            $folders_list = array_collection_to_list($folders_list, 'id', 'title');
            if($this->request->has('folder_id')){
                $item['folder_id'] = $this->request->get('folder_id', 0);
            }
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->orderBy('ordering')->getContentFields($ctype['name']);

        $form = $this->getItemForm($ctype, $fields, 'add', array(
            'groups_list' => $groups_list,
            'folders_list' => $folders_list
        ));

        $parents = $this->model->getContentTypeParents($ctype['id']);

        if ($parents){
            foreach($parents as $parent){

                if (!$this->request->has($parent['id_param_name'])){
                    continue;
                }

                if (!cmsUser::isAllowed($ctype_name, 'add_to_parent') && !cmsUser::isAllowed($ctype_name, 'bind_to_parent')) {
                    $form->hideField($parent['id_param_name']);
                    continue;
                }

                $parent_id = $this->request->get($parent['id_param_name'], 0);
                $parent_item = $parent_id ? $this->model->getContentItem($parent['ctype_name'], $parent_id) : false;

                if($parent_item){

                    if (!empty($is_check_parent_perm) && !$this->cms_user->is_admin){
                        if (cmsUser::isAllowed($ctype_name, 'add_to_parent', 'to_own') && $parent_item['user_id'] != $this->cms_user->id){
                            cmsCore::error404();
                        }
                        if (cmsUser::isAllowed($ctype_name, 'add_to_parent', 'to_other') && $parent_item['user_id'] == $this->cms_user->id){
                            cmsCore::error404();
                        }
                    }

                    $item[$parent['id_param_name']] = $parent_id;
                    $relation_id = $parent['id'];

                }

                break;

            }
        }

        if (!empty($is_check_parent_perm) && empty($relation_id)){
            cmsCore::error404();
        }

        // Заполняем поля значениями по-умолчанию, взятыми из профиля пользователя
        // (для тех полей, в которых это включено)
        foreach($fields as $field){
            if (!empty($field['options']['profile_value'])){
                $item[$field['name']] = $this->cms_user->{$field['options']['profile_value']};
            }
            if (!empty($field['options']['relation_id']) && !empty($relation_id)){
                if ($field['options']['relation_id'] != $relation_id){
                    $form->hideField($field['name']);
                }
            }
        }

        $is_moderator = $this->cms_user->is_admin || $this->model->userIsContentTypeModerator($ctype_name, $this->cms_user->id);
        $is_premoderation = $ctype['is_premod_add'];

		cmsEventsManager::hook('content_add', $ctype);
        list($form, $item) = cmsEventsManager::hook("content_{$ctype['name']}_form", array($form, $item));

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if (!$is_submitted && !empty($category_id)) { $item['category_id'] = $category_id; }

		if ($this->request->has('group_id') && $groups_list && !$is_submitted){
			$item['parent_id'] = $this->request->get('group_id', 0);
		}

        $item['ctype_name'] = $ctype['name'];
		$item['ctype_id']   = $ctype['id'];
        $item['ctype_data'] = $ctype;

        if ($is_submitted){

            if ($ctype['props']){
                $props_cat_id = $this->request->get('category_id', 0);
                if ($props_cat_id){
                    $item_props = $this->model->getContentProps($ctype['name'], $props_cat_id);
                    $item_props_fields = $this->getPropsFields($item_props);
                    foreach($item_props_fields as $field){
                        $form->addField('props', $field);
                    }
                }
            }

            // Парсим форму и получаем поля записи
            $item = array_merge($item, $form->parse($this->request, $is_submitted));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $item);

            if ($parents && $is_check_parent_perm){

                $perm = cmsUser::getPermissionValue($ctype_name, 'add_to_parent');

                foreach($parents as $parent){
                    if (!empty($item[$parent['id_param_name']])){
                        $ids = explode(',', $item[$parent['id_param_name']]);
                        $this->model->filterIn('id', $ids);
                        $parent_items = $this->model->getContentItems($parent['ctype_name']);
                        if ($parent_items){
                            foreach($parent_items as $parent_item){
                                if ($perm == 'to_own' && $parent_item['user']['id'] != $this->cms_user->id) {
                                    $errors[$parent['id_param_name']] = LANG_CONTENT_WRONG_PARENT;
                                    break;
                                }
                                if ($perm == 'to_other' && $parent_item['user']['id'] == $this->cms_user->id) {
                                    $errors[$parent['id_param_name']] = LANG_CONTENT_WRONG_PARENT;
                                    break;
                                }
                            }
                        }
                    }
                }

            }

			if (!$errors){
				list($item, $errors) = cmsEventsManager::hook('content_validate', array($item, $errors));
			}

            // несколько категорий
            if (!empty($ctype['options']['is_cats_multi'])){
                $add_cats = $this->request->get('add_cats', array());
                if (is_array($add_cats)){
                    foreach($add_cats as $index=>$cat_id){
                        if (!is_numeric($cat_id) || !$cat_id){
                            unset($add_cats[$index]);
                        }
                    }
                    if ($add_cats){
                        $item['add_cats'] = $add_cats;
                    }
                }
            }

            if (!$errors){

                $item['is_approved'] = !$ctype['is_premod_add'] || $is_moderator;

				$is_pub_control = cmsUser::isAllowed($ctype['name'], 'pub_on');
				$is_date_pub_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_late');
				$is_date_pub_end_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'any');
				$is_date_pub_days_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');
				$pub_max_days = intval(cmsUser::getPermissionValue($ctype['name'], 'pub_max_days'));

				$date_pub_time = isset($item['date_pub']) ? strtotime($item['date_pub']) : time();
				$now_time = time();
                $now_date = strtotime(date('Y-m-d', $now_time));
				$is_pub = true;

				if ($is_date_pub_allowed){
					$time_to_pub = $date_pub_time - $now_time;
					$is_pub = $is_pub && ($time_to_pub < 0);
				}
				if ($is_date_pub_end_allowed && !empty($item['date_pub_end'])){
					$date_pub_end_time = strtotime($item['date_pub_end']);
					$days_from_pub = floor(($now_date - $date_pub_end_time)/60/60/24);
					$is_pub = $is_pub && ($days_from_pub < 1);
				} else if ($is_date_pub_days_allowed && !$this->cms_user->is_admin) {
					$days = $item['pub_days'];
					$date_pub_end_time = $date_pub_time + 60*60*24*$days;
					$days_from_pub = floor(($now_date - $date_pub_end_time)/60/60/24);
					$is_pub = $is_pub && ($days_from_pub < 1);
					$item['date_pub_end'] = date('Y-m-d', $date_pub_end_time);
				} else {
					$item['date_pub_end'] = false;
				}

				unset($item['pub_days']);
				if (!$is_pub_control) { unset($item['is_pub']); }
				if (!isset($item['is_pub'])) { $item['is_pub'] = $is_pub; }
				if (!empty($item['is_pub'])) { $item['is_pub'] = $is_pub; }

                $item = cmsEventsManager::hook("content_before_add", $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_before_add", $item);

                // SEO параметры
                $item_seo = $this->prepareItemSeo($item, $fields, $ctype);
                if(empty($ctype['options']['is_manual_title']) && !empty($ctype['options']['seo_title_pattern'])){
                    $item['seo_title'] = string_replace_keys_values_extended($ctype['options']['seo_title_pattern'], $item_seo);
                }
                if ($ctype['is_auto_keys']){
                    if(!empty($ctype['options']['seo_keys_pattern'])){
                        $item['seo_keys'] = string_replace_keys_values_extended($ctype['options']['seo_keys_pattern'], $item_seo);
                    } else {
                        $item['seo_keys'] = string_get_meta_keywords($item['content']);
                    }
                }
                if ($ctype['is_auto_desc']){
                    if(!empty($ctype['options']['seo_desc_pattern'])){
                        $item['seo_desc'] = string_get_meta_description(string_replace_keys_values_extended($ctype['options']['seo_desc_pattern'], $item_seo));
                    } else {
                        $item['seo_desc'] = string_get_meta_description($item['content']);
                    }
                }

                $item = $this->model->addContentItem($ctype, $item, $fields);

                $this->bindItemToParents($ctype, $item, $parents);

                if ($ctype['is_tags']){
                    $tags_model = cmsCore::getModel('tags');
                    $tags_model->addTags($item['tags'], $this->name, $ctype['name'], $item['id']);
                    $item['tags'] = $tags_model->getTagsStringForTarget($this->name, $ctype['name'], $item['id']);
                    $this->model->updateContentItemTags($ctype['name'], $item['id'], $item['tags']);
                }

                cmsEventsManager::hook('content_after_add', $item);
                cmsEventsManager::hook("content_{$ctype['name']}_after_add", $item);

                if ($item['is_approved']){
                    cmsEventsManager::hook("content_after_add_approve", array('ctype_name'=>$ctype_name, 'item'=>$item));
                    cmsEventsManager::hook("content_{$ctype['name']}_after_add_approve", $item);
                } else {
                    $this->requestModeration($ctype_name, $item);
                }

                if ($back_url){
                    $this->redirect($back_url);
                } else {
					if ($ctype['options']['item_on']){
						$this->redirectTo($ctype_name, $item['slug'] . '.html');
					} else {
						$this->redirectTo($ctype_name);
					}
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('item_form', array(
            'do'               => 'add',
            'cancel_url'       => ($back_url ? $back_url : ($ctype['options']['list_on'] ? href_to($ctype['name']) : false)),
            'parent'           => isset($parent) ? $parent : false,
            'ctype'            => $ctype,
            'item'             => $item,
            'form'             => $form,
            'props'            => $props,
            'group'            => ((!empty($item['parent_id']) && !empty($groups[$item['parent_id']])) ? $groups[$item['parent_id']] : array()),
            'is_moderator'     => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'is_load_props'    => !isset($errors),
            'add_cats'         => isset($add_cats) ? $add_cats : array(),
            'errors'           => isset($errors) ? $errors : false
        ));

    }

}
