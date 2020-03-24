<?php

class onModerationContentGroupsBeforeDelete extends cmsAction {

    public function run($group){

        if (!$group['is_approved'] && empty($group['access']['is_owner'])){

            $this->model->closeModeratorTask('groups', $group['id'], false, $this->cms_user->id);

            $group['reason'] = trim(strip_tags($this->cms_core->request->get('reason', '')));

            $this->moderationNotifyAuthor($group, 'moderation_refused');

        }

        if(!empty($this->options['clear_log_after_delete'])){
            $this->model->logDeleteTarget('groups', 'groups', $group['id']);
        }

        if(empty($this->options['moderation_log_delete'])){ return $group; }

        $this->model->log(modelModeration::LOG_DELETE_ACTION, array(
            'moderator_id'      => ($this->cms_user->is_logged ? $this->cms_user->id : null),
            'author_id'         => $group['owner_id'],
            'target_id'         => $group['id'],
            'target_controller' => 'groups',
            'target_subject'    => 'groups',
            'data'              => array(
                'title' => $group['title'],
                'url'   => href_to_rel('groups', $group['slug'])
            )
        ));

        return $group;

    }

}
