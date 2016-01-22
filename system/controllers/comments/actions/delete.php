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

        if (!cmsUser::isAllowed('comments', 'delete', 'all') && !cmsUser::isAllowed('comments', 'delete', 'full_delete')) {
            if (cmsUser::isAllowed('comments', 'delete', 'own') && $comment['user']['id'] != $user->id) {

                cmsTemplate::getInstance()->renderJSON(array(
                    'error' => true,
                    'message' => LANG_ERROR
                ));

            }
        }

        // проверяем, есть ли дети комментария
        $is_comment_child = $this->model->getItemByField('comments', 'parent_id', $comment['id']);

        $this->model->deleteComment($comment['id'], (!$is_comment_child && cmsUser::isAllowed('comments', 'delete', 'full_delete', true)));

        if(cmsUser::isAllowed('comments', 'delete', 'full_delete')){
            cmsEventsManager::hook('comments_after_delete', $comment['id']);
        } else {
            cmsEventsManager::hook('comments_after_hide', $comment['id']);
        }

        cmsTemplate::getInstance()->renderJSON(array(
            'error' => false,
            'message' => LANG_COMMENT_DELETED
        ));

    }

}
