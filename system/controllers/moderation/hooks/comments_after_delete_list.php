<?php

class onModerationCommentsAfterDeleteList extends cmsAction {

    public function run($ids){

        foreach ($ids as $comment_id) {

            $this->model->closeModeratorTask('comments', $comment_id, false, $this->cms_user->id);

            if(!empty($this->options['clear_log_after_delete'])){
                $this->model->logDeleteTarget('comments', 'comments', $comment_id);
            }
        }

        return $ids;
    }

}
