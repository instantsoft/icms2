<?php

class onModerationContentBeforeDelete extends cmsAction {

    public function run($data){

        $ctype_name = $data['ctype_name'];
        $item       = $data['item'];

        $this->model->closeModeratorTask($ctype_name, $item['id'], false, $this->cms_user->id);

        if(!empty($this->options['clear_log_after_delete'])){
            $this->model->logDeleteTarget('content', $ctype_name, $item['id']);
        }

        if(empty($this->options['moderation_log_delete'])){ return $data; }

        $this->model->log(modelModeration::LOG_DELETE_ACTION, array(
            'moderator_id'      => ($this->cms_user->is_logged ? $this->cms_user->id : null),
            'author_id'         => $item['user_id'],
            'target_id'         => $item['id'],
            'target_controller' => 'content',
            'target_subject'    => $ctype_name,
            'data'              => array(
                'title'   => $item['title'],
                'slug'    => $item['slug']
            )
        ));

        return $data;

    }

}
