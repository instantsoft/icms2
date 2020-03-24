<?php

class actionContentItemEdit extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        // Получаем нужную запись
        // принудительно отключаем локализацию, должны быть чистые данные
        $item = $this->model->localizedOff()->getContentItem($ctype['name'], $id);
        if (!$item) { cmsCore::error404(); }

        // возвращаем как было
        $this->model->localizedRestore();

        $item['ctype_id'] = $ctype['id'];
        $item['ctype_name'] = $ctype['name'];
        $item['ctype_data'] = $ctype;

        // автор записи?
        $is_owner = $item['user_id'] == $this->cms_user->id;

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed($ctype['name'], 'edit', 'all') && !cmsUser::isAllowed($ctype['name'], 'edit', 'premod_all')) {
            if (
                (cmsUser::isAllowed($ctype['name'], 'edit', 'own') ||
                    cmsUser::isAllowed($ctype['name'], 'edit', 'premod_own')
                ) && !$is_owner) {
                cmsCore::error404();
            }
        }

        // модерация
        $is_premoderation = false;
        if(cmsUser::isAllowed($ctype['name'], 'edit', 'premod_own', true) || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_all', true)){
            $is_premoderation = true;
        }
        if (!$is_premoderation && !$item['date_approved']) {
            $is_premoderation = cmsUser::isAllowed($ctype['name'], 'add', 'premod', true);
        }
        $is_moderator = $this->cms_user->is_admin || cmsCore::getModel('moderation')->userIsContentModerator($ctype['name'], $this->cms_user->id);

        if (!$item['is_approved'] && !$is_moderator && !$item['is_draft']) { cmsCore::error404(); }

        if ($item['is_deleted']){

            $allow_restore = (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $is_owner));

            if (!$is_moderator && !$allow_restore){ cmsCore::error404(); }
        }

        // Получаем родительский тип, если он задан
        if ($this->request->has('parent_type')){
            $parent['ctype'] = $this->model->getContentTypeByName($this->request->get('parent_type', ''));
            $parent['item']  = $this->model->getContentItemBySLUG($parent['ctype']['name'], $this->request->get('parent_slug', ''));
        }

        // Определяем наличие полей-свойств
        $props = $this->model->getContentProps($ctype['name']);
        $ctype['props'] = $props;

        // Если включены личные папки - получаем их список
        $folders_list = array();

        if ($ctype['is_folders']){
            $folders_list = $this->model->getContentFolders($ctype['id'], $item['user_id']);
            $folders_list = array_collection_to_list($folders_list, 'id', 'title');
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->orderBy('ordering')->getContentFields($ctype['name'], $id);

        // Если этот контент можно создавать в группах (сообществах) то получаем список групп
        $groups_list = array();

        if ($ctype['is_in_groups'] || $ctype['is_in_groups_only']){

            $groups_model = cmsCore::getModel('groups');
            $groups = $groups_model->getUserGroups($item['user_id']);

            if ($groups){
                $groups_list = ($ctype['is_in_groups_only']) ? array() : array('0'=>'');
                $groups_list = $groups_list + array_collection_to_list($groups, 'id', 'title');
            }

        }

        // Строим форму
        $form = $this->getItemForm($ctype, $fields, 'edit', array(
            'groups_list' => $groups_list,
            'folders_list' => $folders_list
        ), $id, $item);

		list($ctype, $item) = cmsEventsManager::hook('content_edit', array($ctype, $item));
        list($form, $item)  = cmsEventsManager::hook("content_{$ctype['name']}_form", array($form, $item));

        // Форма отправлена?
        $is_submitted = $this->request->has('submit') || $this->request->has('to_draft');

        // форма отправлена к контексте черновика
        $is_draf_submitted = $this->request->has('to_draft');

        if ($ctype['props']){

            $category_id = !$is_submitted ? $item['category_id'] :
                (($this->request->has('category_id') && $ctype['options']['is_cats_change']) ?
                    $this->request->get('category_id', 0) :
                    $item['category_id']);

            $item_props = $this->model->getContentProps($ctype['name'], $category_id);
            $item_props_fields = $this->getPropsFields($item_props);

            $item['props'] = $this->model->localizedOff()->getPropsValues($ctype['name'], $id);
            $this->model->localizedRestore();

            foreach($item_props_fields as $field){
                $form->addField('props', $field);
            }
        }

		$is_pub_control = cmsUser::isAllowed($ctype['name'], 'pub_on');
		$is_date_pub_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_late');
		$is_date_pub_end_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'any');
		$is_date_pub_days_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');
		$is_date_pub_ext_allowed = $is_date_pub_days_allowed && cmsUser::isAllowed($ctype['name'], 'pub_max_ext');

		if ($is_date_pub_ext_allowed){
			$item['pub_days'] = 0;
		}

		$add_cats = $this->model->getContentItemCategories($ctype['name'], $id);

		if ($add_cats){
			foreach($add_cats as $index => $cat_id){
				if ($cat_id == $item['category_id']) { unset($add_cats[$index]); break; }
			}
		}

        $show_save_button = ($is_owner || (!$is_premoderation && $item['is_approved']));

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $item = array_merge($item, $form->parse($this->request, $is_submitted, $item));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $item);

			list($item, $errors) = cmsEventsManager::hook('content_validate', array($item, $errors), null, $this->request);
            list($item, $errors, $ctype, $fields) = cmsEventsManager::hook("content_{$ctype['name']}_validate", array($item, $errors, $ctype, $fields), null, $this->request);

            if (!$errors){

                if($is_draf_submitted){

                    $item['is_approved'] = 0;

                } else {

                    if($item['is_draft']){
                        $item['is_approved'] = !$is_premoderation || $is_moderator;
                    } else {
                        $item['is_approved'] = $item['is_approved'] && (!$is_premoderation || $is_moderator);
                    }

                }

                if($is_draf_submitted || !$item['is_approved']){
                    unset($item['date_approved']);
                }

                if($is_owner){
                    $item['approved_by'] = null;
                }

				$date_pub_time = strtotime($item['date_pub']);
				$date_pub_end_time = strtotime($item['date_pub_end']);
				$now_time = time();
                $now_date = strtotime(date('Y-m-d', $now_time));
				$is_pub = true;

				if ($is_date_pub_allowed){
					$time_to_pub = $date_pub_time - $now_time;
					$is_pub = $is_pub && ($time_to_pub < 0);
				}
				if ($is_date_pub_end_allowed && !empty($item['date_pub_end'])){
					$days_from_pub = floor(($now_date - $date_pub_end_time)/60/60/24);
					$is_pub = $is_pub && ($days_from_pub < 1);
				} else if ($is_date_pub_ext_allowed && !$this->cms_user->is_admin) {
					$days = $item['pub_days'];
					$date_pub_end_time = (($date_pub_end_time - $now_time) > 0 ? $date_pub_end_time : $now_time) + 60*60*24*$days;
					$days_from_pub = floor(($now_date - $date_pub_end_time)/60/60/24);
					$is_pub = $is_pub && ($days_from_pub < 1);
					$item['date_pub_end'] = date('Y-m-d', $date_pub_end_time);
				}

				unset($item['pub_days']);

				if (!$is_pub_control) { unset($item['is_pub']); }
				if (!isset($item['is_pub']) || !empty($item['is_pub'])){
					$item['is_pub'] = $is_pub;
					if (!$is_pub){
						cmsUser::addSessionMessage(LANG_CONTENT_IS_PUB_OFF);
					}
				}

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

                //
                // Сохраняем запись и редиректим на ее просмотр
                //
                $item = cmsEventsManager::hook('content_before_update', $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_before_update", $item);

                $item = $this->model->updateContentItem($ctype, $id, $item, $fields);

                $this->bindItemToParents($ctype, $item);

                cmsEventsManager::hook('content_after_update', $item);
                cmsEventsManager::hook("content_{$ctype['name']}_after_update", $item);

                if(!$is_draf_submitted){

                    if ($item['is_approved'] || $is_moderator){

                        // новая запись, например из черновика
                        if(empty($item['date_approved'])){
                            cmsEventsManager::hook('content_after_add_approve', array('ctype_name' => $ctype['name'], 'item' => $item));
                            cmsEventsManager::hook("content_{$ctype['name']}_after_add_approve", $item);
                        }

                        cmsEventsManager::hook('content_after_update_approve', array('ctype_name'=>$ctype['name'], 'item'=>$item));
                        cmsEventsManager::hook("content_{$ctype['name']}_after_update_approve", $item);

                    } else {

                        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

                        $succes_text = cmsCore::getController('moderation')->requestModeration($ctype['name'], $item, false);

                        if($succes_text){
                            cmsUser::addSessionMessage($succes_text, 'info');
                        }

                    }

                } else {

                    if($show_save_button && $is_moderator && !$is_owner){

                        $item['reason']   = LANG_PM_MODERATION_REWORK_DRAFT;
                        $item['page_url'] = href_to_abs($ctype['name'], 'edit', $item['id']);

                        $this->controller_moderation->moderationNotifyAuthor($item, 'moderation_rework');

                        cmsUser::addSessionMessage(LANG_MODERATION_REMARK_NOTIFY, 'info');

                    }

                }

                $back_url = $this->request->get('back', '');

                if ($back_url){
                    $this->redirect($back_url);
                } else {
                    $this->redirectTo($ctype['name'], $item['slug'] . '.html');
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $back_url = $this->request->get('back', '');

        $button_draft_text = LANG_CONTENT_SAVE_DRAFT;

        if(!$item['is_draft']){
            if($show_save_button){
                if($is_moderator && !$is_owner){
                    $button_draft_text = LANG_MODERATION_RETURN_FOR_REVISION;
                } else {
                    $button_draft_text = LANG_CONTENT_MOVE_DRAFT;
                }
            } else {
                $button_draft_text = LANG_SAVE;
            }
        }

        return $this->cms_template->render('item_form', array(
            'do'               => 'edit',
            'page_title'       => $item['title'],
            'group'            => ((!empty($item['parent_id']) && !empty($groups[$item['parent_id']])) ? $groups[$item['parent_id']] : array()),
            'cancel_url'       => ($back_url ? $back_url : ($ctype['options']['item_on'] ? href_to($ctype['name'], $item['slug'] . '.html') : false)),
            'ctype'            => $ctype,
            'parent'           => isset($parent) ? $parent : false,
            'item'             => $item,
            'form'             => $form,
            'props'            => $props,
            'is_moderator'     => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'show_save_button' => $show_save_button,
            'button_save_text' => (($is_premoderation && !$is_moderator) ? LANG_MODERATION_SEND : ($item['is_approved'] ? LANG_SAVE : LANG_PUBLISH)),
            'button_draft_text' => $button_draft_text,
            'hide_draft_btn'   => !empty($ctype['options']['disable_drafts']),
            'is_multi_cats'    => !empty($ctype['options']['is_cats_multi']),
            'is_load_props'    => false,
            'add_cats'         => $add_cats,
            'errors'           => isset($errors) ? $errors : false
        ));

    }

}
