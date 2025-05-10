<?php

class actionContentItemReturn extends cmsAction {

    public function run() {

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) {
            return cmsCore::error404();
        }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) {
            return cmsCore::error404();
        }

        if ($item['is_approved'] || $item['is_draft']) {
            return cmsCore::error404();
        }

        // Проверяем права
        if ($this->cms_user->id != $item['user_id']) {
            return cmsCore::error404();
        }

        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

        $this->controller_moderation->cancelModeratorTask($ctype['name'], $item, $this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]));

        cmsUser::addSessionMessage(LANG_CONTENT_DRAFT_NOTICE, 'success');

        $back_url = $this->getRequestBackUrl();

        if ($back_url) {
            return $this->redirect($back_url);
        }

        return $this->redirectTo('moderation', 'draft');
    }

}
