<?php

class actionCommentsRate extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $comment_id = $this->request->get('comment_id', 0);
        $score      = $this->request->get('score', '');

        // Проверяем валидность
        $is_valid = is_numeric($comment_id) &&
                    in_array($score, array(-1, 1));

        if (!$is_valid){ $this->cms_template->renderJSON(array('error' => true)); }

        $is_can_rate = cmsUser::isAllowed('comments', 'rate');

        if (!$is_can_rate){ $this->cms_template->renderJSON(array('error' => true)); }

        $is_voted = $this->model->isUserVoted($comment_id, $this->cms_user->id);

        if ($is_voted){ $this->cms_template->renderJSON(array('error' => true)); }

        $comment = $this->model->getComment($comment_id);

        if ($comment['user_id'] == $this->cms_user->id) { $this->cms_template->renderJSON(array('error' => true)); }

        $success = $this->model->rateComment($comment_id, $this->cms_user->id, $score);

		if($success && $comment['user_id'] && !empty($this->options['update_user_rating'])){
            $rating = $this->model->getItemById('{users}', $comment['user_id']);
            $this->model->update('{users}', $comment['user_id'], array('rating' => ($rating['rating'] + $score)));
		}

        cmsCore::getController('activity')->addEntry($this->name, 'vote.comment', array(
            'is_private'    => (int)$comment['is_private'],
            'subject_title' => $comment['target_title'],
            'subject_id'    => $comment_id,
            'subject_url'   => $comment['target_url'] . '#comment_'.$comment['id']
        ));

        $this->cms_template->renderJSON(array('error' => !$success));

    }

}
