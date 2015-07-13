<?php

class actionMessagesNoticeAction extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $notice_id = $this->request->get('notice_id') or cmsCore::error404();
        $action_name = $this->request->get('action_name') or cmsCore::error404();

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $notice = $this->model->getNotice($notice_id);

        $result = array('error' => true);

        //
        // Проверяем хозяина уведомления
        //
        if ($notice['user_id'] != $user->id){
            $template->renderJSON(array(
                'error' => true,
                'message' => 'unknown user'
            ));
        }

        //
        // Если это закрытие уведомления и его можно закрывать, то закроем
        //
        if ($action_name == 'close' && $notice['options']['is_closeable']){
            $this->model->deleteNotice($notice_id);
            $template->renderJSON(array(
                'error' => false,
            ));
        }

        //
        // Проверяем наличие требуемого действия
        //
        if (!isset($notice['actions'][$action_name])){
            $template->renderJSON(array(
                'error' => true,
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
                'href' => $action['href'],
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
                'error' => false,
            );

        }

        //
        // Удаляем уведомление и возвращаем результат
        //
        if (!$result['error']) { $this->model->deleteNotice($notice_id); }
        $template->renderJSON($result);

    }

}
