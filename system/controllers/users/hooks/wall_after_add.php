<?php

class onUsersWallAfterAdd extends cmsAction {

    public function run($profile_type, $profile_id, $entry, $wall_model){

        if ($profile_type != 'user') { return false; }

        $this->notifyProfileOwner($profile_id, $entry);

        // Если родительская запись привязана к статусу,
        // то увеличиваем число ответов у статуса
        if ($entry['parent_id']){
            $parent_entry = $wall_model->getEntry($entry['parent_id']);
            if ($parent_entry['status_id']){
                $this->model->increaseUserStatusRepliesCount($parent_entry['status_id']);
            }
        }

    }

    private function notifyProfileOwner($profile_id, $entry){

        if ($entry['user_id'] == $profile_id) { return; }

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipient($profile_id);

        $messenger->sendNoticeEmail('wall_reply', array(
            'profile_url'     => href_to_abs('users', $profile_id) . "?wid={$entry['id']}&reply=1",
            'author_url'      => href_to_abs('users', $entry['user_id']),
            'author_nickname' => $entry['user_nickname'],
            'content'         => $entry['content_html']
        ), 'users_wall_write');

    }

}
