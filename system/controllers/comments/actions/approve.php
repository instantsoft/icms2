<?php

class actionCommentsApprove extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        if(!cmsUser::isAllowed('comments', 'is_moderator')){
            return $this->cms_template->renderJSON(array(
                'error' => true,
                'message' => LANG_COMMENT_ERROR
            ));
        }

        $comment_id = $this->request->get('id', 0);
        if (!$comment_id){
            return $this->cms_template->renderJSON(array(
                'error' => true,
                'message' => LANG_COMMENT_ERROR
            ));
        }

        $comment = $this->model->getComment($comment_id);
        if (!$comment){
            return $this->cms_template->renderJSON(array(
                'error' => true,
                'message' => LANG_COMMENT_ERROR
            ));
        }

        $this->model->approveComment($comment['id']);

        // Уведомляем модель целевого контента об изменении количества комментариев
        $comments_count = $this->model->
                filterCommentTarget(
                    $comment['target_controller'],
                    $comment['target_subject'],
                    $comment['target_id']
                )->getCommentsCount();

        $this->model->resetFilters();

        cmsCore::getModel($comment['target_controller'])->updateCommentsCount($comment['target_subject'], $comment['target_id'], $comments_count);

        $parent_comment = $comment['parent_id'] ? $this->model->getComment($comment['parent_id']) : false;

        // Уведомляем подписчиков
        $this->notifySubscribers($comment, $parent_comment);

        // Уведомляем об ответе на комментарий
        if ($parent_comment){ $this->notifyParent($comment, $parent_comment); }

        $comment = cmsEventsManager::hook('comment_after_add', $comment);

        return $this->cms_template->renderJSON(array(
            'error'     => false,
            'message'   => '',
            'id'        => $comment['id'],
            'parent_id' => $comment['parent_id'],
            'level'     => $comment['level'],
            'html'      => cmsEventsManager::hook('parse_text', $comment['content_html'])
        ));

    }

}
