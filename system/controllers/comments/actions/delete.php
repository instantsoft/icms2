<?php

class actionCommentsDelete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $is_moderator = $this->controller_moderation->model->userIsContentModerator($this->name, $this->cms_user->id);

        if (!cmsUser::isAllowed('comments', 'delete') && !$is_moderator){
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
        $is_full_delete = (!$is_comment_child && cmsUser::isAllowed('comments', 'delete', 'full_delete', true)) || !$comment['is_approved'];

        $this->model->deleteComment($comment['id'], $is_full_delete);

        if(!$comment['is_approved']){

            cmsEventsManager::hook('comments_after_refuse', $comment);

        } else {

            if($is_full_delete){

                // обновляем количество
                $comments_count = $this->model->
                                            filterEqual('target_controller', $comment['target_controller'])->
                                            filterEqual('target_subject', $comment['target_subject'])->
                                            filterEqual('target_id', $comment['target_id'])->
                                            getCommentsCount();

                cmsCore::getModel($comment['target_controller'])->updateCommentsCount($comment['target_subject'], $comment['target_id'], $comments_count);

                cmsEventsManager::hook('comments_after_delete', $comment);

            } else {
                cmsEventsManager::hook('comments_after_hide', $comment);
            }

        }

        $this->cms_template->renderJSON(array(
            'error'   => false,
            'message' => LANG_COMMENT_DELETED
        ));

    }

}
