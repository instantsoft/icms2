<?php

class actionUsersStatus extends cmsAction {

    public function run(){

		if (!cmsUser::isLogged()) { cmsCore::error404(); }

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user_id = $this->request->get('user_id', 0);
        $content = $this->request->get('content', '');

        // Проверяем валидность
        if (!is_numeric($user_id)){
            return $this->cms_template->renderJSON(array( 'error' => true, 'message' => LANG_ERROR ));
        }

        if ($this->cms_user->id != $user_id){
            return $this->cms_template->renderJSON(array( 'error' => true, 'message' => LANG_ERROR ));
        }

        // Вырезаем теги и форматируем
        $content = cmsEventsManager::hook('html_filter', strip_tags(trim($content)));
        if (!$content){
            return $this->cms_template->renderJSON(array( 'error' => true, 'message' => ERR_VALIDATE_REQUIRED));
        }

        $status_content = trim(strip_tags($content));

        // проверяем длину статуса
        if (mb_strlen($status_content) > 140) {
            return $this->cms_template->renderJSON(array( 'error' => true, 'message' => sprintf(ERR_VALIDATE_MAX_LENGTH, 140)));
        }

        // Добавляем запись на стену
        $wall_model = cmsCore::getModel('wall');

        $wall_entry_id = $wall_model->addEntry(array(
            'controller'   => 'users',
            'profile_type' => 'user',
            'profile_id'   => $user_id,
            'user_id'      => $user_id,
            'content'      => $content,
            'content_html' => $content
        ));

        // сохраняем статус
        $status_id = $this->model->addUserStatus(array(
            'user_id'       => $user_id,
            'content'       => $status_content,
            'wall_entry_id' => $wall_entry_id
        ));

        if ($status_id){

            $wall_model->updateEntryStatusId($wall_entry_id, $status_id);

            cmsCore::getController('activity')->addEntry($this->name, 'status', array(
                'subject_title' => $status_content,
                'reply_url' => href_to_rel($this->name, $user_id) . "?wid={$wall_entry_id}&reply=1"
            ));

        }

        return $this->cms_template->renderJSON(array(
            'error'         => $status_id ? false : true,
            'wall_entry_id' => $wall_entry_id,
            'content'       => $status_content
        ));

    }

}
