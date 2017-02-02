<?php

class onModerationContentAfterRestore extends cmsAction {

    public function run($data){

        if(empty($this->options['moderation_log_restore'])){ return $data; }

        list($ctype_name, $item) = $data;

        $is_author = $this->cms_user->id == $item['user_id'];

        $this->model->log(modelModeration::LOG_RESTORE_ACTION, array(
            'moderator_id'      => (($this->cms_user->is_logged && !$is_author) ? $this->cms_user->id : null),
            'author_id'         => $item['user_id'],
            'target_id'         => $item['id'],
            'target_controller' => 'content',
            'target_subject'    => $ctype_name,
            'data'              => array(
                'title'   => $item['title'],
                'slug'    => $item['slug']
            )
        ));

        $this->model->logUpdateTarget('content', $ctype_name, $item['id'], array(
            'date_expired' => false
        ));

        return $data;

    }

}
