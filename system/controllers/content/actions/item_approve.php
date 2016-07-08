<?php

class actionContentItemApprove extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name', '');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $id);
        if (!$item) { cmsCore::error404(); }

        if ($item['is_approved']){ $this->redirectBack(); }

        // Проверяем права
        $is_moderator = $user->is_admin || $this->model->userIsContentTypeModerator($ctype_name, $user->id);
        if (!$is_moderator){ cmsCore::error404(); }

        $task = $this->model->getModeratorTask($ctype_name, $id);

        $this->model->approveContentItem($ctype_name, $id, $user->id);

        $this->model->closeModeratorTask($ctype_name, $id, true);

        $after_action = $task['is_new_item'] ? 'add' : 'update';

        cmsEventsManager::hook("content_after_{$after_action}_approve", array('ctype_name'=>$ctype_name, 'item'=>$item));
        cmsEventsManager::hook("content_{$ctype['name']}_after_{$after_action}_approve", $item);

        $this->notifyAuthor($ctype_name, $item);

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        $this->redirectTo($ctype_name, $item['slug'] . '.html');

    }

    public function notifyAuthor($ctype_name, $item){

        $users_model = cmsCore::getModel('users');

        $author = $users_model->getUser($item['user_id']);

        $messenger = cmsCore::getController('messages');
        $to = array('email' => $author['email'], 'name' => $author['nickname']);
        $letter = array('name' => 'moderation_approved');

        $messenger->sendEmail($to, $letter, array(
            'nickname' => $author['nickname'],
            'page_title' => $item['title'],
            'page_url' => href_to_abs($ctype_name, $item['slug'] . ".html"),
            'date' => html_date_time(),
        ));

    }

}
