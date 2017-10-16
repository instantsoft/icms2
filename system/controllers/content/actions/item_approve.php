<?php

class actionContentItemApprove extends cmsAction {

    public function run(){

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) { cmsCore::error404(); }

        if ($item['is_approved']){ $this->redirectBack(); }

        // Проверяем права
        $is_moderator = $this->cms_user->is_admin || $this->controller_moderation->model->userIsContentModerator($ctype['name'], $this->cms_user->id);
        if (!$is_moderator){ cmsCore::error404(); }

        $task = $this->controller_moderation->model->getModeratorTask($ctype['name'], $item['id']);

        $this->model->approveContentItem($ctype['name'], $item['id'], $this->cms_user->id);

        $this->controller_moderation->model->closeModeratorTask($ctype['name'], $item['id'], true, $this->cms_user->id);

        $after_action = $task['is_new_item'] ? 'add' : 'update';

        cmsEventsManager::hook("content_after_{$after_action}_approve", array('ctype_name'=>$ctype['name'], 'item'=>$item));
        cmsEventsManager::hook("content_{$ctype['name']}_after_{$after_action}_approve", $item);

        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

        $this->controller_moderation->moderationNotifyAuthor($item, 'moderation_approved');

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        $this->redirectTo($ctype['name'], $item['slug'] . '.html');

    }

}
