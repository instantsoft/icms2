<?php

class actionContentItemApprove extends cmsAction {

    public function run(){

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) { cmsCore::error404(); }

        if ($item['is_approved'] || $item['is_draft']){ cmsCore::error404(); }

        // Проверяем права
        $is_moderator = $this->cms_user->is_admin || $this->controller_moderation->model->userIsContentModerator($ctype['name'], $this->cms_user->id);
        if (!$is_moderator){ cmsCore::error404(); }

        $this->model->approveContentItem($ctype['name'], $item['id'], $this->cms_user->id);

        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

        $this->controller_moderation->approve($ctype['name'], $item, $this->getUniqueKey(array($ctype['name'], 'moderation', $item['id'])));

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        $back_url = $this->request->get('back', '');

        if ($back_url) {
            $this->redirect($back_url);
        } else {
            $this->redirectTo($ctype['name'], $item['slug'] . '.html');
        }

    }

}
