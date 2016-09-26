<?php

class actionMessagesNoticeAction extends cmsAction {

    public function run(){

        $notice_id   = $this->request->get('notice_id', 0) or cmsCore::error404();
        $action_name = $this->request->get('action_name', '') or cmsCore::error404();

        $notice = $this->model->getNotice($notice_id);

        $result = array('error' => true);

        //
        // Проверяем хозяина уведомления
        //
        if ($notice['user_id'] != $this->cms_user->id){
            $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => 'unknown user'
            ));
        }

        //
        // Если это закрытие уведомления и его можно закрывать, то закроем
        //
        if ($action_name == 'close' && $notice['options']['is_closeable']){
            $this->model->deleteNotice($notice_id);
            $this->cms_template->renderJSON(array(
                'error' => false
            ));
        }

        //
        // Проверяем наличие требуемого действия
        //
        if (!isset($notice['actions'][$action_name])){
            $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => 'unknown action'
            ));
        }

        $action = $notice['actions'][$action_name];

        //
        // Если указан URL для редиректа, то возвращаем его
        //
        if (isset($action['href'])){
            $result = array(
                'error' => false,
                'href'  => $action['href']
            );
        }

        //
        // Если указан контроллер и действие, то выполняем
        //
        if (isset($action['controller'], $action['action'])){

            $params = isset($action['params']) ? $action['params'] : array();

            $controller = cmsCore::getController($action['controller']);

            $controller->runAction($action['action'], $params);

            $result = array(
                'error' => false
            );

        }

        //
        // Удаляем уведомление и возвращаем результат
        //
        if (!$result['error']) { $this->model->deleteNotice($notice_id); }

        $this->cms_template->renderJSON($result);

    }

}
