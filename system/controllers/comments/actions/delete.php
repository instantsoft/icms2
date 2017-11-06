<?php

class actionCommentsDelete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        if (!cmsUser::isAllowed('comments', 'delete')){
            $this->cms_template->renderJSON($result = array(
                'error' => true,
                'message' => LANG_ERROR
            ));
        }

        $comment = $this->model->getComment($this->request->get('id', 0));

        // Проверяем
        if (!$comment){
            $this->cms_template->renderJSON($result = array(
                'error' => true,
                'message' => LANG_ERROR
            ));
        }

        if (!cmsUser::isAllowed('comments', 'delete', 'all') && !cmsUser::isAllowed('comments', 'delete', 'full_delete')) {
            if (cmsUser::isAllowed('comments', 'delete', 'own') && $comment['user']['id'] != $this->cms_user->id) {

                $this->cms_template->renderJSON(array(
                    'error' => true,
                    'message' => LANG_ERROR
                ));

            }
        }

        $comment = cmsEventsManager::hook('comments_before_delete', $comment);

        // можем ли полностью удалять
        $is_comment_child = $this->model->getItemByField('comments', 'parent_id', $comment['id']);
        $full_delete = !$is_comment_child && cmsUser::isAllowed('comments', 'delete', 'full_delete', true);

        $this->model->deleteComment($comment['id'], ($full_delete || !$comment['is_approved']));

        if(!$comment['is_approved']){

            cmsEventsManager::hook('comments_after_refuse', $comment['id']);

        } else {

            if($full_delete){

                // обновляем количество
                $comments_count = $this->model->
                                            filterEqual('target_controller', $comment['target_controller'])->
                                            filterEqual('target_subject', $comment['target_subject'])->
                                            filterEqual('target_id', $comment['target_id'])->
                                            getCommentsCount();

                cmsCore::getModel($comment['target_controller'])->updateCommentsCount($comment['target_subject'], $comment['target_id'], $comments_count);

                cmsEventsManager::hook('comments_after_delete', $comment['id']);

            } else {
                cmsEventsManager::hook('comments_after_hide', $comment['id']);
            }

        }

        $this->cms_template->renderJSON(array(
            'error'   => false,
            'message' => LANG_COMMENT_DELETED
        ));

    }

}
