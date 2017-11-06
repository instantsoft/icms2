<?php

class actionCommentsCommentsDelete extends cmsAction {

    public function run($id){

        $comment = $this->model->getComment($id);
        if (!$comment){ cmsCore::error404(); }

        $delete_count = $this->model->deleteComment($comment['id'], true);

        // обновляем количество
        $comments_count = $this->model->
                                    filterEqual('target_controller', $comment['target_controller'])->
                                    filterEqual('target_subject', $comment['target_subject'])->
                                    filterEqual('target_id', $comment['target_id'])->
                                    getCommentsCount();

        cmsCore::getModel($comment['target_controller'])->updateCommentsCount($comment['target_subject'], $comment['target_id'], $comments_count);

        cmsEventsManager::hook('comments_after_delete', $comment['id']);

        cmsUser::addSessionMessage(html_spellcount($delete_count, LANG_COMMENT1, LANG_COMMENT2, LANG_COMMENT10).LANG_COMMENTS_DELETED, 'success');

        $this->redirectToAction('comments_list');

    }

}
