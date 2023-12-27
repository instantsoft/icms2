<?php

class actionContentItemAdd extends cmsAction {

    public function run(){

        // Получаем название типа контента
        $ctype_name = $this->request->get('ctype_name', '');
        if (!$ctype_name) {
            return cmsCore::error404();
        }

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $permissions = cmsEventsManager::hook('content_add_permissions', [
            'can_add' => false,
            'ctype'   => $ctype
        ]);

        $is_check_parent_perm = false;

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'add') && !$permissions['can_add']) {
            if (!cmsUser::isAllowed($ctype['name'], 'add_to_parent')) {
                if(!$this->cms_user->is_logged){
                    return cmsUser::goLogin();
                }
                return cmsCore::error404();
            }
            $is_check_parent_perm = true;
        }

        // проверяем что не превышен лимит на число записей
        $user_items_count = $this->model->getUserContentItemsCount($ctype['name'], $this->cms_user->id, false);
        if (cmsUser::isPermittedLimitReached($ctype['name'], 'limit', $user_items_count)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_COUNT_LIMIT, $ctype['labels']['many']), 'error');
            return $this->redirectBack();
        }

        // проверяем что не превышен лимит на число записей в сутки
        $user_items_24count = $this->model->getUserContentItemsCount24($ctype['name'], $this->cms_user->id, false);
        if (cmsUser::isPermittedLimitReached($ctype['name'], 'limit24', $user_items_24count)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_COUNT_LIMIT24, $ctype['labels']['many'], $ctype['labels']['two']), 'error');
            return $this->redirectBack();
        }

        // Проверяем ограничение по карме
        if (cmsUser::isPermittedLimitHigher($ctype['name'], 'karma', $this->cms_user->karma)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_KARMA_LIMIT, cmsUser::getPermissionValue($ctype['name'], 'karma')), 'error');
            return $this->redirectBack();
        }

		$item = [];

        if ($ctype['is_cats']){
            $category_id = $this->request->get('to_id', 0);
        }

        // Определяем наличие полей-свойств
        $props = $this->model->getContentProps($ctype['name']);
        $ctype['props'] = $props;

        // Если включены личные папки - получаем их список
        $folders_list = [];

        if ($ctype['is_folders']){
            $folders_list = $this->model->getContentFolders($ctype['id'], $this->cms_user->id);
            $folders_list = array_collection_to_list($folders_list, 'id', 'title');
            if($this->request->has('folder_id')){
                $item['folder_id'] = $this->request->get('folder_id', 0);
            }
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->orderBy('ordering')->getContentFields($ctype['name']);

        $form = $this->getItemForm($ctype, $fields, 'add', [
            'folders_list' => $folders_list
        ]);

        // Связи
        $parents = $this->model->filterEqual('c.is_enabled', 1)->getContentTypeParents($ctype['id']);
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
                            return cmsCore::error404();
                        }
                        if (cmsUser::isAllowed($ctype['name'], 'add_to_parent', 'to_other') && $parent_item['user_id'] == $this->cms_user->id){
                            return cmsCore::error404();
                        }
                    }

                    $item[$parent['id_param_name']] = $parent_id;
                    $relation_id = $parent['id'];

                }

                break;
            }
        }

        if (!empty($is_check_parent_perm) && empty($relation_id)){
            return cmsCore::error404();
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

        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $item);
        $is_premoderation = cmsUser::isAllowed($ctype['name'], 'add', 'premod', true);

		$ctype = cmsEventsManager::hook('content_add', $ctype);
        list($form, $item) = cmsEventsManager::hook("content_{$ctype['name']}_form", [$form, $item]);

        // Форма отправлена?
        $is_submitted = $this->request->has('submit') || $this->request->has('to_draft');

        // форма отправлена к контексте черновика
        $is_draf_submitted = $this->request->has('to_draft');

        // Передана категория, в которую добавляем
        if (!$is_submitted && !empty($category_id)) {

            $item['category_id'] = $category_id;

            if ($ctype['is_cats'] && $item['category_id'] > 1){
                $item['category'] = $this->model->getCategory($ctype['name'], $item['category_id']);
            }
        }

        $item['ctype_name'] = $ctype['name'];
		$item['ctype_id']   = $ctype['id'];
        $item['ctype_data'] = $ctype;

        if ($is_submitted){

            // Добавляем поля свойств для валидации
            if ($ctype['props']){
                $form = $this->addFormPropsFields($form, $ctype, [], $is_submitted);
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

			list($item, $errors) = cmsEventsManager::hook('content_validate', [$item, $errors], null, $this->request);
            list($item, $errors, $ctype, $fields) = cmsEventsManager::hook("content_{$ctype['name']}_validate", [$item, $errors, $ctype, $fields], null, $this->request);

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

                if (!$is_pub_control) {
                    unset($item['is_pub']);
                }

                if (!isset($item['is_pub']) || $item['is_pub'] >= 1) {
                    $item['is_pub'] = $is_pub;
                }

                $item = cmsEventsManager::hook('content_before_add', $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_before_add", $item);

                $item = $this->model->addContentItem($ctype, $item, $fields);

                $item['ctype_name'] = $ctype['name'];
                $item['ctype_id']   = $ctype['id'];
                $item['ctype_data'] = $ctype;

                $this->bindItemToParents($ctype, $item, $parents);

                $item = cmsEventsManager::hook([
                    'content_after_add',
                    "content_{$ctype['name']}_after_add"
                ], $item, null, $this->request);

                if(!$is_draf_submitted){

                    if ($item['is_approved']){
                        cmsEventsManager::hook('content_after_add_approve', ['ctype_name' => $ctype['name'], 'item' => $item]);
                        cmsEventsManager::hook("content_{$ctype['name']}_after_add_approve", $item);
                    } else {

                        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

                        $succes_text = cmsCore::getController('moderation')->requestModeration($ctype['name'], $item);

                        if($succes_text){
                            cmsUser::addSessionMessage($succes_text, 'info');
                        }

                    }

                }

                $back_url = $this->getRequestBackUrl();

                if ($back_url){
                    return $this->redirect($back_url);
                } else {

                    if($is_draf_submitted){
                        return $this->redirectTo('moderation', 'draft');
                    }

					if ($ctype['options']['item_on']){
						return $this->redirectTo($ctype['name'], $item['slug'] . '.html');
					} else {
						return $this->redirectTo($ctype['name']);
					}

                }
            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $base_url = ($this->cms_config->ctype_default && in_array($ctype['name'], $this->cms_config->ctype_default)) ? '' : $ctype['name'];

        $perms_notices = [
            'edit_times' => cmsUser::getPermissionValue($ctype['name'], 'edit_times'),
            'delete_times' => cmsUser::getPermissionValue($ctype['name'], 'delete_times')
        ];

        $back_url = $this->getRequestBackUrl();

        return $this->cms_template->render('item_form', [
            'do'               => 'add',
            'perms_notices'    => array_filter($perms_notices),
            'base_url'         => $base_url,
            'page_title'       => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
            'cancel_url'       => ($back_url ? $back_url : ($ctype['options']['list_on'] ? href_to($ctype['name']) : $this->getBackURL())),
            'parent'           => isset($parent) ? $parent : false,
            'ctype'            => $ctype,
            'item'             => $item,
            'form'             => $form,
            'props'            => $props,
            'is_moderator'     => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'button_save_text' => (($is_premoderation && !$is_moderator) ? LANG_MODERATION_SEND : LANG_SAVE),
            'button_draft_text' => LANG_CONTENT_SAVE_DRAFT,
            'hide_draft_btn'   => !empty($ctype['options']['disable_drafts']),
            'is_load_props'    => !isset($errors),
            'errors'           => isset($errors) ? $errors : false
        ]);
    }

}
