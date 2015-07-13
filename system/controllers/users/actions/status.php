<?php

class actionUsersStatus extends cmsAction {

    public function run(){

		if (!cmsUser::isLogged()) { cmsCore::error404(); }
		
        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user_id = $this->request->get('user_id');
        $content = $this->request->get('content');

        // Проверяем валидность
        if (!is_numeric($user_id)){
            $result = array( 'error' => true, 'message' => LANG_ERROR );
            cmsTemplate::getInstance()->renderJSON($result);
        }

        $user = cmsUser::getInstance();

        if ($user->id != $user_id){
            $result = array( 'error' => true, 'message' => LANG_ERROR );
            cmsTemplate::getInstance()->renderJSON($result);
        }

        // Вырезаем теги и форматируем
        $content = strip_tags($content);
        if (mb_strlen($content) > 140) { $content = mb_substr($content, 0, 140); }
        $content = cmsEventsManager::hook('html_filter', $content);

        if (!$content){
            $result = array( 'error' => true, 'message' => '');
            cmsTemplate::getInstance()->renderJSON($result);
        }

        // Добавляем запись на стену
        $wall_model = cmsCore::getModel('wall');

        $wall_entry_id = $wall_model->addEntry(array(
            'controller' => 'users',
            'profile_type' => 'user',
            'profile_id' => $user_id,
            'user_id' => $user_id,
            'content' => $content,
            'content_html' => $content
        ));

        // сохраняем статус
        $status_id = $this->model->addUserStatus(array(
            'user_id' => $user_id,
            'content' => $content,
            'wall_entry_id' => $wall_entry_id
        ));

        if ($status_id){
            
            $wall_model->updateEntryStatusId($wall_entry_id, $status_id);
            
            cmsCore::getController('activity')->addEntry($this->name, "status", array(
                'subject_title' => $content,
                'reply_url' => href_to($this->name, $user_id) . "?wid={$wall_entry_id}&reply=1"
            ));
            
        }

        $result = array(
            'error' => $status_id ? false : true,
            'wall_entry_id' => $wall_entry_id,
            'content' => $content,
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
