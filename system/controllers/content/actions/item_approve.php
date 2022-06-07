<?php

class actionContentItemApprove extends cmsAction {

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
        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id, $item);
        if (!$is_moderator) {
            return cmsCore::error404();
        }

        $this->model->approveContentItem($ctype['name'], $item['id'], $this->cms_user->id);

        $item['page_url'] = href_to_abs($ctype['name'], $item['slug'] . '.html');

        $this->controller_moderation->approve($ctype['name'], $item, $this->getUniqueKey([$ctype['name'], 'moderation', $item['id']]));

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        $back_url = $this->getRequestBackUrl();

        if ($back_url) {
            return $this->redirect($back_url);
        } else {
            return $this->redirectTo($ctype['name'], $item['slug'] . '.html');
        }
    }

}
