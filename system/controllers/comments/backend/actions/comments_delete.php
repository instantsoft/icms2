<?php

class actionCommentsCommentsDelete extends cmsAction {

    private $delete_count = 0;

    public function run($id = null) {

        if ($id) {
            $items = [$id];
        } else {
            $items = $this->request->get('selected', []);
        }

        if (!$items) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        foreach ($items as $comment_id) {
            if (is_numeric($comment_id)) {
                $this->deleteComment($comment_id);
            }
        }

        cmsUser::addSessionMessage(html_spellcount($this->delete_count, LANG_COMMENT1, LANG_COMMENT2, LANG_COMMENT10) . LANG_COMMENTS_DELETED, 'success');

        return $this->redirectToAction('');
    }

    private function deleteComment($id) {

        $this->delete_count += count($this->model->deleteComment($id, true));

        return true;
    }

}
