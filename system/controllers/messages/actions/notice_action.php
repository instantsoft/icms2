<?php

class actionMessagesNoticeAction extends cmsAction {

    public function run() {

        $result = ['error' => true];

        $notice_id   = $this->request->get('notice_id', 0);
        $action_name = $this->request->get('action_name', '');

        //
        // Действие должно быть передано
        //
        if (!$action_name) {
            return $this->cms_template->renderJSON($result);
        }

        //
        // Очистка всех уведомлений
        //
        if (!$notice_id && $action_name == 'clear_notice') {

            $this->model->deleteUserNotices($this->cms_user->id);

            return $this->cms_template->renderJSON(['error' => false]);
        }

        //
        // id уведомления должно быть передано
        //
        if (!$notice_id) {
            return $this->cms_template->renderJSON($result);
        }

        //
        // Получаем уведомление
        //
        $notice = $this->model->getNotice($notice_id);
        if (!$notice) {
            return $this->cms_template->renderJSON($result);
        }

        //
        // Проверяем хозяина уведомления
        //
        if ($notice['user_id'] != $this->cms_user->id) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => 'unknown user'
            ]);
        }

        //
        // Если это закрытие уведомления и его можно закрывать, то закроем
        //
        if ($action_name == 'close' && $notice['options']['is_closeable']) {

            $this->model->deleteNotice($notice_id);

            return $this->cms_template->renderJSON([
                'error' => false
            ]);
        }

        //
        // Проверяем наличие требуемого действия
        //
        if (!isset($notice['actions'][$action_name])) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => 'unknown action'
            ]);
        }

        $action = $notice['actions'][$action_name];

        //
        // Если указан URL для редиректа, то возвращаем его
        //
        if (isset($action['href'])) {
            $result = [
                'error' => false,
                'href'  => $action['href']
            ];
        }

        //
        // Если указан контроллер и действие, то выполняем
        //
        if (isset($action['controller'], $action['action'])) {

            $params = isset($action['params']) ? $action['params'] : [];

            $controller = cmsCore::getController($action['controller']);

            $controller->runAction($action['action'], $params);

            $result = [
                'error' => false
            ];
        }

        //
        // Удаляем уведомление и возвращаем результат
        //
        if (!$result['error']) {
            $this->model->deleteNotice($notice_id);
        }

        return $this->cms_template->renderJSON($result);
    }

}
