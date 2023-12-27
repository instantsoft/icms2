<?php

class actionContentItemEdit extends cmsAction {

    public function run() {

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        // Получаем нужную запись
        // принудительно отключаем локализацию, должны быть чистые данные
        $item = $this->model->localizedOff()->getContentItem($ctype['name'], $id);
        if (!$item) { cmsCore::error404(); }

        if ($ctype['is_cats'] && $item['category_id'] > 1){
            $item['category'] = $this->model->getCategory($ctype['name'], $item['category_id']);
        }

        // возвращаем как было
        $this->model->localizedRestore();

        $item['ctype_id']   = $ctype['id'];
        $item['ctype_name'] = $ctype['name'];
        $item['ctype_data'] = $ctype;

        $permissions = cmsEventsManager::hook('content_edit_permissions', [
            'can_edit' => false,
            'item'     => $item,
            'ctype'    => $ctype
        ]);

        // автор записи?
        $is_owner = $item['user_id'] == $this->cms_user->id;

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit') && !$permissions['can_edit']) {
            cmsCore::error404();
        }
        if (!cmsUser::isAllowed($ctype['name'], 'edit', 'all') &&
                !cmsUser::isAllowed($ctype['name'], 'edit', 'premod_all') &&
                !$permissions['can_edit'] &&
                ((cmsUser::isAllowed($ctype['name'], 'edit', 'own') || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_own')) && !$is_owner)) {
            cmsCore::error404();
        }

        // модерация
        $is_premoderation = false;
        if (cmsUser::isAllowed($ctype['name'], 'edit', 'premod_own', true) || cmsUser::isAllowed($ctype['name'], 'edit', 'premod_all', true)) {
            $is_premoderation = true;
        }
        if (!$is_premoderation && !$item['date_approved']) {
            $is_premoderation = cmsUser::isAllowed($ctype['name'], 'add', 'premod', true);
        }
        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $item);

        if (!$item['is_approved'] && !$is_moderator && !$item['is_draft']) {
            cmsCore::error404();
        }

        if ($item['is_deleted']) {

            $allow_restore = (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
                    (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $is_owner));

            if (!$is_moderator && !$allow_restore) {
                cmsCore::error404();
            }
        }

        // Не вышло ли время для редактирования
        if (cmsUser::isPermittedLimitReached($ctype['name'], 'edit_times', ((time() - strtotime($item['date_pub']))/60))){
            cmsUser::addSessionMessage(LANG_CONTENT_PERMS_TIME_UP_EDIT, 'error');
            $this->redirectTo($ctype['name'], $item['slug'] . '.html');
        }

        // Получаем родительский тип, если он задан
        if ($this->request->has('parent_type')) {
            $parent['ctype'] = $this->model->getContentTypeByName($this->request->get('parent_type', ''));
            $parent['item']  = $this->model->getContentItemBySLUG($parent['ctype']['name'], $this->request->get('parent_slug', ''));
        }

        // Определяем наличие полей-свойств
        $ctype['props'] = $props = $this->model->getContentProps($ctype['name']);

        // Если включены личные папки - получаем их список
        $folders_list = [];

        if ($ctype['is_folders']) {
            $folders_list = $this->model->getContentFolders($ctype['id'], $item['user_id']);
            $folders_list = array_collection_to_list($folders_list, 'id', 'title');
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->orderBy('ordering')->getContentFields($ctype['name'], $id);

        // Строим форму
        $form = $this->getItemForm($ctype, $fields, 'edit', [
            'folders_list' => $folders_list
        ], $id, $item);

        list($ctype, $item) = cmsEventsManager::hook('content_edit', array($ctype, $item));
        list($form, $item) = cmsEventsManager::hook("content_{$ctype['name']}_form", array($form, $item));

        // Категории записи
        $item['add_cats'] = $item_cats = $this->model->getContentItemCategories($ctype['name'], $id);
        if ($item['add_cats']) {
            // Отдельно дополнительные категории
            foreach ($item['add_cats'] as $index => $cat_id) {
                if ($cat_id == $item['category_id']) {
                    unset($item['add_cats'][$index]);
                    break;
                }
            }
        }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit') || $this->request->has('to_draft');

        // форма отправлена к контексте черновика
        $is_draf_submitted = $this->request->has('to_draft');

        if ($ctype['props']) {

            if (!$item_cats) {
                $item_cats = [$item['category_id']];
            }

            $form = $this->addFormPropsFields($form, $ctype, $item_cats, $is_submitted);

            $item['props'] = $this->model->localizedOff()->getPropsValues($ctype['name'], $id);
            $this->model->localizedRestore();
        }

        $is_pub_control           = cmsUser::isAllowed($ctype['name'], 'pub_on');
        $is_date_pub_allowed      = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_late');
        $is_date_pub_end_allowed  = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'any');
        $is_date_pub_days_allowed = $ctype['is_date_range'] && cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');
        $is_date_pub_ext_allowed  = $is_date_pub_days_allowed && cmsUser::isAllowed($ctype['name'], 'pub_max_ext');

        if ($is_date_pub_ext_allowed) {
            $item['pub_days'] = 0;
        }

        $show_save_button = ($is_owner || (!$is_premoderation && $item['is_approved']));

        if ($is_submitted) {

            // Парсим форму и получаем поля записи
            $item = array_merge($item, $form->parse($this->request, $is_submitted, $item));

            // Проверям правильность заполнения
            $errors = $form->validate($this, $item);

            list($item, $errors) = cmsEventsManager::hook('content_validate', array($item, $errors), null, $this->request);
            list($item, $errors, $ctype, $fields) = cmsEventsManager::hook("content_{$ctype['name']}_validate", array($item, $errors, $ctype, $fields), null, $this->request);

            if (!$errors) {

                if ($is_draf_submitted) {

                    $item['is_approved'] = 0;
                } else {

                    if ($item['is_draft']) {
                        $item['is_approved'] = !$is_premoderation || $is_moderator;
                    } else {
                        $item['is_approved'] = $item['is_approved'] && (!$is_premoderation || $is_moderator);
                    }
                }

                if ($is_draf_submitted || !$item['is_approved']) {
                    unset($item['date_approved']);
                }

                if ($is_owner) {
                    $item['approved_by'] = null;
                }

                $date_pub_time     = strtotime($item['date_pub']);
                $now_time          = time();
                $now_date          = strtotime(date('Y-m-d', $now_time));
                $is_pub            = true;

                if ($is_date_pub_allowed) {
                    $time_to_pub = $date_pub_time - $now_time;
                    $is_pub      = $is_pub && ($time_to_pub < 0);
                }
                if ($is_date_pub_end_allowed && !empty($item['date_pub_end'])) {

                    $date_pub_end_time = strtotime($item['date_pub_end']);

                    $days_from_pub = floor(($now_date - $date_pub_end_time) / 60 / 60 / 24);
                    $is_pub        = $is_pub && ($days_from_pub < 1);

                } else if ($is_date_pub_ext_allowed && !$this->cms_user->is_admin) {

                    $date_pub_end_time = !empty($item['date_pub_end']) ? strtotime($item['date_pub_end']) : $now_time;

                    $date_pub_end_time    = (($date_pub_end_time - $now_time) > 0 ? $date_pub_end_time : $now_time) + 60 * 60 * 24 * $item['pub_days'];
                    $days_from_pub        = floor(($now_date - $date_pub_end_time) / 60 / 60 / 24);
                    $is_pub               = $is_pub && ($days_from_pub < 1);
                    $item['date_pub_end'] = date('Y-m-d', $date_pub_end_time);
                }

                unset($item['pub_days']);

                if (!$is_pub_control) {
                    unset($item['is_pub']);
                }
                if (!isset($item['is_pub']) || $item['is_pub'] >= 1) {
                    $item['is_pub'] = $is_pub;
                    if (!$is_pub) {
                        cmsUser::addSessionMessage(LANG_CONTENT_IS_PUB_OFF);
                    }
                }

                //
                // Сохраняем запись и редиректим на ее просмотр
                //
                $item = cmsEventsManager::hook([
                    'content_before_update',
                    "content_{$ctype['name']}_before_update"
                ], $item, null, $this->request);

                $item = $this->model->updateContentItem($ctype, $id, $item, $fields);

                $this->bindItemToParents($ctype, $item);

                $item = cmsEventsManager::hook([
                    'content_after_update',
                    "content_{$ctype['name']}_after_update"
                ], $item, null, $this->request);

                if (!$is_draf_submitted) {

                    if ($item['is_approved'] || $is_moderator) {

                        // новая запись, например из черновика
                        if (empty($item['date_approved'])) {
                            cmsEventsManager::hook('content_after_add_approve', array('ctype_name' => $ctype['name'], 'item' => $item));
                            cmsEventsManager::hook("content_{$ctype['name']}_after_add_approve", $item);
                        }

                        cmsEventsManager::hook('content_after_update_approve', array('ctype_name' => $ctype['name'], 'item' => $item));
                        cmsEventsManager::hook("content_{$ctype['name']}_after_update_approve", $item);

                        cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                    } else {

                        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

                        $succes_text = cmsCore::getController('moderation')->requestModeration($ctype['name'], $item, false);

                        if ($succes_text) {
                            cmsUser::addSessionMessage($succes_text, 'info');
                        }
                    }
                } else {

                    if ($show_save_button && $is_moderator && !$is_owner) {

                        $item['reason']   = LANG_PM_MODERATION_REWORK_DRAFT;
                        $item['page_url'] = href_to_abs($ctype['name'], 'edit', $item['id']);

                        $this->controller_moderation->moderationNotifyAuthor($item, 'moderation_rework');

                        cmsUser::addSessionMessage(LANG_MODERATION_REMARK_NOTIFY, 'info');
                    }
                }

                $back_url = $this->getRequestBackUrl();

                if ($back_url) {
                    $this->redirect($back_url);
                } else {
                    $this->redirectTo($ctype['name'], $item['slug'] . '.html');
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $back_url = $this->getRequestBackUrl();

        $button_draft_text = LANG_CONTENT_SAVE_DRAFT;

        if (!$item['is_draft']) {
            if ($show_save_button) {
                if ($is_moderator && !$is_owner) {
                    $button_draft_text = LANG_MODERATION_RETURN_FOR_REVISION;
                } else {
                    $button_draft_text = LANG_CONTENT_MOVE_DRAFT;
                }
            } else {
                $button_draft_text = LANG_SAVE;
            }
        }

        $base_url = ($this->cms_config->ctype_default && in_array($ctype['name'], $this->cms_config->ctype_default)) ? '' : $ctype['name'];

        return $this->cms_template->render('item_form', [
            'do'                => 'edit',
            'perms_notices'     => [],
            'base_url'          => $base_url,
            'page_title'        => $item['title'],
            'cancel_url'        => ($back_url ? $back_url : ($ctype['options']['item_on'] ? href_to($ctype['name'], $item['slug'] . '.html') : false)),
            'ctype'             => $ctype,
            'parent'            => isset($parent) ? $parent : false,
            'item'              => $item,
            'form'              => $form,
            'props'             => $props,
            'is_moderator'      => $is_moderator,
            'is_premoderation'  => $is_premoderation,
            'show_save_button'  => $show_save_button,
            'button_save_text'  => (($is_premoderation && !$is_moderator) ? LANG_MODERATION_SEND : ($item['is_approved'] ? LANG_SAVE : LANG_PUBLISH)),
            'button_draft_text' => $button_draft_text,
            'hide_draft_btn'    => !empty($ctype['options']['disable_drafts']),
            'is_load_props'     => false,
            'errors'            => isset($errors) ? $errors : false
        ]);
    }

}
