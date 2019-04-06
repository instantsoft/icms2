<?php

class actionContentItemAdd extends cmsAction {

    public function run(){

        $back_url = $this->request->get('back', '');

        // Получаем название типа контента
        $ctype_name = $this->request->get('ctype_name', '');
        if (!$ctype_name) { cmsCore::error404(); }

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $permissions = cmsEventsManager::hook('content_add_permissions', array(
            'can_add' => false,
            'ctype'   => $ctype
        ));

        $is_check_parent_perm = false;

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'add') && !$permissions['can_add']) {
            if (!cmsUser::isAllowed($ctype['name'], 'add_to_parent')) {
                if(!$this->cms_user->is_logged){
                    cmsUser::goLogin();
                }
                cmsCore::error404();
            }
            $is_check_parent_perm = true;
        }

        // проверяем что не превышен лимит на число записей
        $user_items_count = $this->model->getUserContentItemsCount($ctype['name'], $this->cms_user->id, false);

        if (cmsUser::isPermittedLimitReached($ctype['name'], 'limit', $user_items_count)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_COUNT_LIMIT, $ctype['labels']['many']), 'error');
            $this->redirectBack();
        }

        // проверяем что не превышен лимит на число записей в сутки
        $user_items_24count = $this->model->getUserContentItemsCount24($ctype['name'], $this->cms_user->id, false);

        if (cmsUser::isPermittedLimitReached($ctype['name'], 'limit24', $user_items_24count)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_COUNT_LIMIT24, $ctype['labels']['many'], $ctype['labels']['two']), 'error');
            $this->redirectBack();
        }

        // Проверяем ограничение по карме
        if (cmsUser::isPermittedLimitHigher($ctype['name'], 'karma', $this->cms_user->karma)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_KARMA_LIMIT, cmsUser::getPermissionValue($ctype['name'], 'karma')), 'error');
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

            $groups_list = ($ctype['is_in_groups_only']) ? array() : array('0'=>'');
            $groups_list = $groups_list + array_collection_to_list($groups, 'id', 'title');

            $group_id = $this->request->get('group_id', 0);
            // если вне групп добавление записей запрещено, даём выбор только одной группы
            if(!cmsUser::isAllowed($ctype['name'], 'add') && isset($groups_list[$group_id])){
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

                if (!cmsUser::isAllowed($ctype['name'], 'add_to_parent') && !cmsUser::isAllowed($ctype['name'], 'bind_to_parent')) {
                    $form->hideField($parent['id_param_name']);
                    continue;
                }

                $parent_id = $this->request->get($parent['id_param_name'], 0);
                $parent_item = $parent_id ? $this->model->getContentItem($parent['ctype_name'], $parent_id) : false;

                if($parent_item){

                    if (!empty($is_check_parent_perm) && !$this->cms_user->is_admin){
                        if (cmsUser::isAllowed($ctype['name'], 'add_to_parent', 'to_own') && $parent_item['user_id'] != $this->cms_user->id){
                            cmsCore::error404();
                        }
                        if (cmsUser::isAllowed($ctype['name'], 'add_to_parent', 'to_other') && $parent_item['user_id'] == $this->cms_user->id){
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

        // Заполняем поля значениями по умолчанию, взятыми из профиля пользователя
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

        $is_moderator = $this->cms_user->is_admin || cmsCore::getModel('moderation')->userIsContentModerator($ctype['name'], $this->cms_user->id);
        $is_premoderation = cmsUser::isAllowed($ctype['name'], 'add', 'premod', true);

		$ctype = cmsEventsManager::hook('content_add', $ctype);
        list($form, $item) = cmsEventsManager::hook("content_{$ctype['name']}_form", array($form, $item));

        // Форма отправлена?
        $is_submitted = $this->request->has('submit') || $this->request->has('to_draft');

        // форма отправлена к контексте черновика
        $is_draf_submitted = $this->request->has('to_draft');

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

                $perm = cmsUser::getPermissionValue($ctype['name'], 'add_to_parent');

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

			list($item, $errors) = cmsEventsManager::hook('content_validate', array($item, $errors), null, $this->request);
            list($item, $errors, $ctype, $fields) = cmsEventsManager::hook("content_{$ctype['name']}_validate", array($item, $errors, $ctype, $fields), null, $this->request);

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

                if($is_draf_submitted){
                    $item['is_approved'] = 0;
                } else {
                    $item['is_approved'] = !$is_premoderation || $is_moderator;
                }

				$is_pub_control = cmsUser::isAllowed($ctype['name'], 'pub_on');
				$is_date_pub_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_late');
				$is_date_pub_end_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'any');
				$is_date_pub_days_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');

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

                $item = cmsEventsManager::hook('content_before_add', $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_before_add", $item);

                $item = $this->model->addContentItem($ctype, $item, $fields);

                $item['ctype_name'] = $ctype['name'];
                $item['ctype_id']   = $ctype['id'];
                $item['ctype_data'] = $ctype;

                $this->bindItemToParents($ctype, $item, $parents);

                $item = cmsEventsManager::hook('content_after_add', $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_after_add", $item);

                if(!$is_draf_submitted){

                    if ($item['is_approved']){
                        cmsEventsManager::hook('content_after_add_approve', array('ctype_name' => $ctype['name'], 'item' => $item));
                        cmsEventsManager::hook("content_{$ctype['name']}_after_add_approve", $item);
                    } else {

                        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

                        $succes_text = cmsCore::getController('moderation')->requestModeration($ctype['name'], $item);

                        if($succes_text){
                            cmsUser::addSessionMessage($succes_text, 'info');
                        }

                    }

                }

                if ($back_url){
                    $this->redirect($back_url);
                } else {

                    if($is_draf_submitted){
                        $this->redirectTo('moderation', 'draft');
                    }

					if ($ctype['options']['item_on']){
						$this->redirectTo($ctype['name'], $item['slug'] . '.html');
					} else {
						$this->redirectTo($ctype['name']);
					}

                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('item_form', array(
            'do'               => 'add',
            'page_title'       => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
            'cancel_url'       => ($back_url ? $back_url : ($ctype['options']['list_on'] ? href_to($ctype['name']) : false)),
            'parent'           => isset($parent) ? $parent : false,
            'ctype'            => $ctype,
            'item'             => $item,
            'form'             => $form,
            'props'            => $props,
            'group'            => ((!empty($item['parent_id']) && !empty($groups[$item['parent_id']])) ? $groups[$item['parent_id']] : array()),
            'is_moderator'     => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'button_save_text' => (($is_premoderation && !$is_moderator) ? LANG_MODERATION_SEND : LANG_SAVE),
            'button_draft_text' => LANG_CONTENT_SAVE_DRAFT,
            'hide_draft_btn'   => !empty($ctype['options']['disable_drafts']),
            'is_multi_cats'    => !empty($ctype['options']['is_cats_multi']),
            'is_load_props'    => !isset($errors),
            'add_cats'         => isset($add_cats) ? $add_cats : array(),
            'errors'           => isset($errors) ? $errors : false
        ));

    }

}
