<?php

class actionContentItemDelete extends cmsAction {

    public function run() {

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) {
            return cmsCore::error404();
        }

        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) {
            return cmsCore::error404();
        }

        $permissions = cmsEventsManager::hook('content_delete_permissions', [
            'can_delete' => false,
            'item'       => $item,
            'ctype'      => $ctype
        ]);

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'delete') && !$permissions['can_delete']) {
            return cmsCore::error404();
        }
        if (!cmsUser::isAllowed($ctype['name'], 'delete', 'all') &&
                $item['user_id'] != $this->cms_user->id &&
                !$permissions['can_delete']) {
            return cmsCore::error404();
        }

        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $item);
        if (!$item['is_approved'] && !$is_moderator && !$item['is_draft']) {
            return cmsCore::error404();
        }

        // Не вышло ли время для удаления
        if (cmsUser::isPermittedLimitReached($ctype['name'], 'delete_times', ((time() - strtotime($item['date_pub'])) / 60))) {

            cmsUser::addSessionMessage(LANG_CONTENT_PERMS_TIME_UP_DELETE, 'error');

            return $this->redirectTo($ctype['name'], $item['slug'] . '.html');
        }

        // в случае отклонения неодобренной записи
        if ($this->request->isAjax() && !$item['is_approved'] && !$item['is_draft']) {

            return $this->cms_template->render('item_refuse', [
                'ctype' => $ctype,
                'item'  => $item
            ]);
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $back_action = '';

        if ($ctype['is_cats'] && $item['category_id']) {

            $category    = $this->model->getCategory($ctype['name'], $item['category_id']);
            $back_action = $category['slug'];
        }

        $this->model->deleteContentItem($ctype['name'], $item['id']);

        if (!$item['is_approved'] && !$item['is_draft'] && $item['user_id'] != $this->cms_user->id) {

            $item['reason'] = trim(strip_tags($this->request->get('reason', '')));

            $this->controller_moderation->moderationNotifyAuthor($item, 'moderation_refused');
        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $back_url = $this->getRequestBackUrl();

        if ($back_url) {
            return $this->redirect($back_url);
        } else {
            if ($ctype['options']['list_on']) {
                return $this->redirectTo($ctype['name'], $back_action);
            } else {
                return $this->redirectToHome();
            }
        }
    }

}
