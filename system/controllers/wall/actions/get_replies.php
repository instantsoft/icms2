<?php

class actionWallGetReplies extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $entry_id = $this->request->get('id');

        // Проверяем валидность
        $is_valid = is_numeric($entry_id);

        if (!$is_valid){
            $result = array('error' => true, 'message' => LANG_ERROR);
            $template->renderJSON($result);
        }

        $user = cmsUser::getInstance();

        $entry = $this->model->getEntry($entry_id);

        $replies = $this->model->getReplies($entry_id);

        if (!$replies){
            $result = array('error' => true, 'message' => LANG_ERROR);
            $template->renderJSON($result);
        }

        $replies = cmsEventsManager::hook('wall_before_list', $replies);

        $permissions = array(
            'add'    => $user->is_logged,
            'delete' => ($user->is_admin || ($user->id == $entry['profile_id']))
        );

        $html = $template->renderInternal($this, 'entry', array(
            'entries'     => $replies,
            'user'        => $user,
            'permissions' => $permissions
        ));

        // Формируем и возвращаем результат
        $result = array(
            'error' => false,
            'html'  => $html
        );

        $template->renderJSON($result);

    }

}
