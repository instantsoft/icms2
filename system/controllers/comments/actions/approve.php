<?php
/**
 * @property \modelComments $model
 * @property \moderation $controller_moderation
 */
class actionCommentsApprove extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $comment_id = $this->request->get('id', 0);
        if (!$comment_id) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => sprintf(LANG_REQUEST_PARAMS_ERROR, 'id')
            ]);
        }

        $comment = $this->model->getComment($comment_id);
        if (!$comment || $comment['is_approved']) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_COMMENT_DELETED
            ]);
        }

        $is_moderator = $this->controller_moderation->userIsContentModerator($this->name, $this->cms_user->id, $comment);

        if (!$is_moderator) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => ERR_FORBIDDEN
            ]);
        }

        $this->model->approveComment($comment['id']);

        $comment['url']      = $comment['target_url'] . '#comment_' . $comment['id'];
        $comment['page_url'] = href_to_abs($comment['target_url']) . '#comment_' . $comment['id'];
        $comment['title']    = $comment['target_title'];

        $this->controller_moderation->approve($this->name, $comment, false, 'moderation_comment_approved');

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
        if ($parent_comment) {
            $this->notifyParent($comment, $parent_comment);
        }

        $comment = cmsEventsManager::hook('comment_after_add', $comment, null, $this->request);

        return $this->cms_template->renderJSON([
            'error'     => false,
            'is_on'     => 1, // для одобрения с админки
            'message'   => '',
            'id'        => $comment['id'],
            'parent_id' => $comment['parent_id'],
            'level'     => $comment['level'],
            'html'      => cmsEventsManager::hook('parse_text', $comment['content_html'])
        ]);
    }

}
