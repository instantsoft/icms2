<?php

class actionContentItemEdit extends cmsAction {

    public function run(){

        $back_url = $this->request->get('back', '');

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $id);
        if (!$item) { cmsCore::error404(); }

        $item['ctype_id'] = $ctype['id'];
        $item['ctype_name'] = $ctype['name'];

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed($ctype['name'], 'edit', 'all')) {
            if (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $item['user_id'] != $this->cms_user->id) {
                cmsCore::error404();
            }
        }

        $is_premoderation = $ctype['is_premod_edit'];
        $is_moderator = $this->cms_user->is_admin || $this->model->userIsContentTypeModerator($ctype['name'], $this->cms_user->id);

        if (!$item['is_approved'] && !$is_moderator) { cmsCore::error404(); }

        if ($item['is_deleted']){

            $allow_restore = (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $item['user_id'] == $this->cms_user->id));

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
            $groups = $groups_model->getUserGroups($this->cms_user->id);

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

        // Получаем теги
        if ($ctype['is_tags']){
            $tags_model = cmsCore::getModel('tags');
            $item['tags'] = $tags_model->getTagsStringForTarget($this->name, $ctype['name'], $id);
        }

		list($ctype, $item) = cmsEventsManager::hook('content_edit', array($ctype, $item));
        list($form, $item)  = cmsEventsManager::hook("content_{$ctype['name']}_form", array($form, $item));

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($ctype['props']){

            $category_id = !$is_submitted ? $item['category_id'] :
                (($this->request->has('category_id') && $ctype['options']['is_cats_change']) ?
                    $this->request->get('category_id', 0) :
                    $item['category_id']);

            $item_props = $this->model->getContentProps($ctype['name'], $category_id);
            $item_props_fields = $this->getPropsFields($item_props);
            $item['props'] = $this->model->getPropsValues($ctype['name'], $id);
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

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $item = array_merge($item, $form->parse($this->request, $is_submitted, $item));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $item);

			if (!$errors){
				list($item, $errors) = cmsEventsManager::hook('content_validate', array($item, $errors));
			}

            if (!$errors){

                $item['is_approved'] = $item['is_approved'] && (!$ctype['is_premod_edit'] || $is_moderator);
                $item['approved_by'] = null;

                if ($ctype['is_tags']){
                    $tags_model->updateTags($item['tags'], $this->name, $ctype['name'], $id);
                    $item['tags'] = $tags_model->getTagsStringForTarget($this->name, $ctype['name'], $id);
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
				} else {
					$item['date_pub_end'] = false;
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

                // SEO параметры
                $item_seo = $this->prepareItemSeo($item, $fields, $ctype);
                if(empty($ctype['options']['is_manual_title']) && !empty($ctype['options']['seo_title_pattern'])){
                    $item['seo_title'] = string_replace_keys_values($ctype['options']['seo_title_pattern'], $item_seo);
                } else {
                    $item['seo_title'] = empty($ctype['options']['is_manual_title']) ? null : $item['seo_title'];
                }
                if ($ctype['is_auto_keys']){
                    if(!empty($ctype['options']['seo_keys_pattern'])){
                        $item['seo_keys'] = string_replace_keys_values($ctype['options']['seo_keys_pattern'], $item_seo);
                    } else {
                        $item['seo_keys'] = string_get_meta_keywords($item['content']);
                    }
                }
                if ($ctype['is_auto_desc']){
                    if(!empty($ctype['options']['seo_desc_pattern'])){
                        $item['seo_desc'] = string_get_meta_description(string_replace_keys_values($ctype['options']['seo_desc_pattern'], $item_seo));
                    } else {
                        $item['seo_desc'] = string_get_meta_description($item['content']);
                    }
                }

                $item = $this->model->updateContentItem($ctype, $id, $item, $fields);

                $item['ctype_data'] = $ctype;

                $this->bindItemToParents($ctype, $item);

                cmsEventsManager::hook('content_after_update', $item);
                cmsEventsManager::hook("content_{$ctype['name']}_after_update", $item);

                if ($item['is_approved'] || $is_moderator){
                    cmsEventsManager::hook('content_after_update_approve', array('ctype_name'=>$ctype['name'], 'item'=>$item));
                    cmsEventsManager::hook("content_{$ctype['name']}_after_update_approve", $item);
                } else {
                    $this->requestModeration($ctype['name'], $item, false);
                }

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

        return $this->cms_template->render('item_form', array(
            'do'               => 'edit',
            'group'            => ((!empty($item['parent_id']) && !empty($groups[$item['parent_id']])) ? $groups[$item['parent_id']] : array()),
            'cancel_url'       => ($back_url ? $back_url : ($ctype['options']['item_on'] ? href_to($ctype['name'], $item['slug'] . '.html') : false)),
            'ctype'            => $ctype,
            'parent'           => isset($parent) ? $parent : false,
            'item'             => $item,
            'form'             => $form,
            'props'            => $props,
            'is_moderator'     => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'is_load_props'    => false,
            'add_cats'         => $add_cats,
            'errors'           => isset($errors) ? $errors : false
        ));

    }

}
