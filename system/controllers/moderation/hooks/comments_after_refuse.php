<?php

class onModerationCommentsAfterRefuse extends cmsAction {

    public function run($comment){

        $this->model->closeModeratorTask('comments', $comment['id'], false, $this->cms_user->id);

        $comment['reason'] = trim(strip_tags($this->cms_core->request->get('reason', '')));
        $comment['title'] = $comment['target_title'];

        $this->moderationNotifyAuthor($comment, 'moderation_comment_refused');

        if(!empty($this->options['clear_log_after_delete'])){
            $this->model->logDeleteTarget('comments', 'comments', $comment['id']);
        }

        if(empty($this->options['moderation_log_delete'])){ return $comment; }

        $this->model->log(modelModeration::LOG_DELETE_ACTION, array(
            'moderator_id'      => ($this->cms_user->is_logged ? $this->cms_user->id : null),
            'author_id'         => $comment['user_id'],
            'target_id'         => $comment['id'],
            'target_controller' => 'comments',
            'target_subject'    => 'comments',
            'data'              => array(
                'title' => $comment['target_title'],
                'url'   => $comment['target_url']
            )
        ));

        return $comment;

    }

}
