<?php

class actionCommentsCommentsDelete extends cmsAction {

    private $delete_count = 0;

    public function run($id = null){

        if($id){
            $items = array($id);
        } else {
            $items = $this->request->get('selected', array());
        }

        if (!$items) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        foreach ($items as $comment_id) {
            if(is_numeric($comment_id)){
                $this->deleteComment($comment_id);
            }
        }

        cmsUser::addSessionMessage(html_spellcount($this->delete_count, LANG_COMMENT1, LANG_COMMENT2, LANG_COMMENT10).LANG_COMMENTS_DELETED, 'success');

        $this->redirectToAction('comments_list');

    }

    private function deleteComment($id) {

        $comment = $this->model->getComment($id);
        if (!$comment){ return false; }

        $this->delete_count += $this->model->deleteComment($comment['id'], true);

        // обновляем количество
        $comments_count = $this->model->
                                    filterEqual('target_controller', $comment['target_controller'])->
                                    filterEqual('target_subject', $comment['target_subject'])->
                                    filterEqual('target_id', $comment['target_id'])->
                                    getCommentsCount();

        cmsCore::getModel($comment['target_controller'])->updateCommentsCount($comment['target_subject'], $comment['target_id'], $comments_count);

        if(!$comment['is_approved']){
            cmsEventsManager::hook('comments_after_refuse', $comment);
        } else {
            cmsEventsManager::hook('comments_after_delete', $comment);
        }

        return true;

    }

}
