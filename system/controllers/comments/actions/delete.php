<?php

class actionCommentsDelete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!cmsUser::isAllowed('comments', 'delete')){ cmsCore::error404(); }

        $comment_id = $this->request->get('id');

        // Проверяем валидность
        $is_valid = is_numeric($comment_id);

        if (!$is_valid){
            $result = array(
                'error' => true,
                'message' => LANG_ERROR
            );
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

        $this->model->deleteComment($comment_id);

        $result = array(
            'error' => false,
            'message' => LANG_COMMENT_DELETED
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
