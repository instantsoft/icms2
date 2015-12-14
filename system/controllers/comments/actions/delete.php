<?php

class actionCommentsDelete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!cmsUser::isAllowed('comments', 'delete')){ cmsCore::error404(); }

        $comment = $this->model->getComment((int)$this->request->get('id'));

        // Проверяем
        if (!$comment){

            cmsTemplate::getInstance()->renderJSON($result = array(
                'error' => true,
                'message' => LANG_ERROR
            ));

        }

        $user = cmsUser::getInstance();

        if (!cmsUser::isAllowed('comments', 'edit', 'all')) {
            if (cmsUser::isAllowed('comments', 'edit', 'own') && $comment['user']['id'] != $user->id) {

                cmsTemplate::getInstance()->renderJSON(array(
                    'error' => true,
                    'message' => LANG_ERROR
                ));

            }
        }

        $this->model->deleteComment($comment['id']);

        cmsEventsManager::hook('comments_after_hide', $comment['id']);

        cmsTemplate::getInstance()->renderJSON(array(
            'error' => false,
            'message' => LANG_COMMENT_DELETED
        ));

    }

}
