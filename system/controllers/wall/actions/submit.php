<?php

class actionWallSubmit extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $action = $this->request->get('action');

        $template = cmsTemplate::getInstance();

        $user = cmsUser::getInstance();

        $csrf_token = $this->request->get('csrf_token');
        $controller_name = $this->request->get('pc');
        $profile_type = $this->request->get('pt');
        $profile_id = $this->request->get('pi');
        $parent_id = $this->request->get('parent_id');
        $entry_id = $this->request->get('id');
        $content = $this->request->get('content');

        // Проверяем валидность
        $is_valid = ($this->validate_sysname($controller_name)===true) &&
                    ($this->validate_sysname($profile_type)===true) &&
                    is_numeric($profile_id) &&
                    is_numeric($parent_id) &&
                    (!$entry_id || is_numeric($entry_id)) &&
                    cmsForm::validateCSRFToken($csrf_token, false) &&
                    in_array($action, array('add', 'preview', 'update'));

        if (!$is_valid){ $this->error(); }

        //
        // Получаем права доступа
        //
        $controller = cmsCore::getController($controller_name);

        if (!$controller){ $this->error(); }

        $permissions = $controller->runHook('wall_permissions', array(
            'profile_type' => $profile_type,
            'profile_id' => $profile_id
        ));

        if (!$permissions || !is_array($permissions)){ $this->error(); }

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', $content);

        //
        // Превью записи
        //
        if ($action=='preview'){

            $result = array('error' => false, 'html' => cmsEventsManager::hook('parse_text', $content_html));

            $template->renderJSON($result);

        }

        //
        // Редактирование записи
        //
        if ($action=='update'){

            $entry = $this->model->getEntry($entry_id);

            if ($entry['user']['id'] != $user->id && !$user->is_admin){ $this->error(); }

            list($entry_id, $content, $content_html) = cmsEventsManager::hook('wall_before_update', array($entry_id, $content, $content_html));

            $this->model->updateEntryContent($entry_id, $content, $content_html);

            $entry_html = $content_html;

        }

        //
        // Добавление записи
        //
        if ($action=='add'){

            // проверяем права на добавление
            if (!$permissions['add']){ $this->error(); }

            // Собираем данные записи
            $entry = array(
                'user_id'      => $user->id,
                'parent_id'    => $parent_id,
                'profile_type' => $profile_type,
                'profile_id'   => $profile_id,
                'content'      => $content,
                'content_html' => $content_html
            );

            // Сохраняем запись
            $entry_id = $this->model->addEntry(cmsEventsManager::hook('wall_before_add', $entry));

            if ($entry_id){

                // Получаем и рендерим добавленную запись
                $entry = $this->model->getEntry($entry_id);

                $entry_html = $template->renderInternal($this, 'entry', array(
                    'entries' => array($entry),
                    'user'=>$user,
                    'permissions'=>$permissions
                ));

                // Уведомляем владельца профиля
                if ($controller_name == 'users' && $profile_type == 'user'){
                    $this->notifyProfileOwner($profile_id, $entry);
                }

                // Если родительская запись привязана к статусу,
                // то увеличиваем число ответов у статуса
                if ($entry['parent_id']){
                    $parent_entry = $this->model->getEntry($entry['parent_id']);
                    if ($parent_entry['status_id']){
                        $users_model = cmsCore::getModel('users');
                        $users_model->increaseUserStatusRepliesCount($parent_entry['status_id']);
                    }
                }

            }

        }

        // Формируем и возвращаем результат
        $result = array(
            'error'     => $entry_id ? false : true,
            'message'   => $entry_id ? LANG_WALL_ENTRY_SUCCESS : LANG_WALL_ENTRY_ERROR,
            'id'        => $entry_id,
            'parent_id' => isset($entry['parent_id']) ? $entry['parent_id'] : 0,
            'html'      => isset($entry_html) ? (cmsEventsManager::hook('parse_text', $entry_html)) : false
        );

        $template->renderJSON($result);

    }

    private function error($message=LANG_WALL_ENTRY_ERROR){

        $result = array('error' => true, 'message' => $message);
        cmsTemplate::getInstance()->renderJSON($result);

    }

    public function notifyProfileOwner($profile_id, $entry){

        if ($entry['user_id'] == $profile_id) { return; }

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($profile_id);

        $messenger->sendNoticeEmail('wall_reply', array(
            'profile_url' => href_to_abs('users', $profile_id) . "?wid={$entry['id']}&reply=1",
            'author_url' => href_to_abs('users', $entry['user_id']),
            'author_nickname' => $entry['user_nickname'],
            'content' => $entry['content_html'],
        ));

    }

}