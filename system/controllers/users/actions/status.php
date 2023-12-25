<?php

class actionUsersStatus extends cmsAction {

    public function run() {

        if (!cmsUser::isLogged()) {
            return cmsCore::error404();
        }

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $user_id = $this->request->get('user_id', 0);
        $content = $this->request->get('content', '');

        // Проверяем валидность
        if (!is_numeric($user_id)) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        if ($this->cms_user->id != $user_id) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        // Вырезаем теги и форматируем
        $content = cmsEventsManager::hook('html_filter', strip_tags(trim($content)));
        if (!$content) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => ERR_VALIDATE_REQUIRED
            ]);
        }

        $status_content = trim(strip_tags($content));

        // проверяем длину статуса
        if (mb_strlen($status_content) > 140) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => sprintf(ERR_VALIDATE_MAX_LENGTH, 140)
            ]);
        }

        // сохраняем статус
        $status_id = $this->model->addUserStatus([
            'user_id' => $user_id,
            'content' => $status_content
        ]);

        list($status_id,
                $user_id,
                $content,
                $status_content,
                $status_link) = cmsEventsManager::hook('user_add_status', [
                    $status_id,
                    $user_id,
                    $content,
                    $status_content,
                    false
        ]);

        list($status_id,
                $user_id,
                $content,
                $status_content,
                $status_link) = cmsEventsManager::hook('user_add_status_after', [
                    $status_id,
                    $user_id,
                    $content,
                    $status_content,
                    $status_link
        ]);

        return $this->cms_template->renderJSON([
            'error'       => $status_id ? false : true,
            'status_link' => rel_to_href($status_link),
            'content'     => $status_content
        ]);
    }

}
