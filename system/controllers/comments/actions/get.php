<?php

class actionCommentsGet extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!cmsUser::isAllowed('comments', 'edit')){ cmsCore::error404(); }

        $comment_id = $this->request->get('id');

        // Проверяем валидность
        $is_valid = is_numeric($comment_id);

        if (!$is_valid){
            $result = array('error' => true, 'message' => LANG_ERROR);
            cmsTemplate::getInstance()->renderJSON($result);
        }

        $user = cmsUser::getInstance();

        $comment = $this->model->getComment($comment_id);

        if (!cmsUser::isAllowed('comments', 'edit', 'all')) {
            if (cmsUser::isAllowed('comments', 'edit', 'own') && $comment['user']['id'] != $user->id) {
                $result = array('error' => true, 'message' => LANG_ERROR);
                cmsTemplate::getInstance()->renderJSON($result);
            }
        }

        // Формируем и возвращаем результат
        $result = array(
            'error' => $comment ? false : true,
            'id' => $comment_id,
            'html' => $comment ? string_strip_br($comment['content']) : false
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
