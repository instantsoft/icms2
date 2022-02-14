<?php
/**
 * @property \modelComments $model
 */
class actionCommentsRate extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $comment_id = $this->request->get('comment_id', 0);
        $score      = $this->request->get('score', '');

        // Проверяем валидность
        $is_valid = is_numeric($comment_id) &&
                in_array($score, [-1, 1]);

        if (!$is_valid) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $is_can_rate = cmsUser::isAllowed('comments', 'rate');

        if (!$is_can_rate) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $is_voted = $this->model->isUserVoted($comment_id, $this->cms_user->id);

        if ($is_voted) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $comment = $this->model->getComment($comment_id);
        if (!$comment) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        if ($comment['user_id'] == $this->cms_user->id) {
            return $this->cms_template->renderJSON(['error' => true]);
        }

        $success = $this->model->rateComment($comment['id'], $this->cms_user->id, $score);

        if ($success && $comment['user_id'] && !empty($this->options['update_user_rating'])) {
            $this->model_users->updateUserRating($comment['user_id'], $score);
        }

        list($comment, $score) = cmsEventsManager::hook('comments_rate_after', [$comment, $score]);

        return $this->cms_template->renderJSON(['error' => !$success]);
    }

}
