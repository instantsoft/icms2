<?php
/**
 * @property \modelComments $model
 */
class actionCommentsDelete extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $is_moderator = $this->controller_moderation->model->userIsContentModerator($this->name, $this->cms_user->id);

        if (!cmsUser::isAllowed('comments', 'delete') && !$is_moderator) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $comment = $this->model->getComment($this->request->get('id', 0));

        // Проверяем
        if (!$comment) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        if (!cmsUser::isAllowed('comments', 'delete', 'all') && !cmsUser::isAllowed('comments', 'delete', 'full_delete')) {
            if (cmsUser::isAllowed('comments', 'delete', 'own') && $comment['user']['id'] != $this->cms_user->id) {
                return $this->cms_template->renderJSON([
                    'error'   => true,
                    'message' => LANG_ERROR
                ]);
            }
        }

        if (cmsUser::isPermittedLimitReached('comments', 'times', ((time() - strtotime($comment['date_pub']))/60))){
            return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Time is over'));
        }

        $comment = cmsEventsManager::hook('comments_before_delete', $comment);

        // можем ли полностью удалять
        $is_full_delete = cmsUser::isAllowed('comments', 'delete', 'full_delete', true) || !$comment['is_approved'];

        $delete_ids = $this->model->deleteComment($comment, $is_full_delete);

        return $this->cms_template->renderJSON([
            'error'          => false,
            'delete_ids'     => $delete_ids,
            'is_full_delete' => $is_full_delete,
            'message'        => LANG_COMMENT_DELETED
        ]);
    }

}
