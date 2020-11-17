<?php

class onWallUserAddStatus extends cmsAction {

    public function run($data){

        list($status_id,
            $user_id,
            $content,
            $status_content,
            $status_link) = $data;

        $wall_entry_id = $this->model->addEntry(array(
            'controller'   => 'users',
            'profile_type' => 'user',
            'status_id'    => $status_id,
            'profile_id'   => $user_id,
            'user_id'      => $user_id,
            'content'      => $content,
            'content_html' => $content
        ));

        $this->model->update('{users}_statuses', $status_id, array(
            'wall_entry_id' => $wall_entry_id
        ), true);

        cmsCache::getInstance()->clean('users.status');

        $user = $this->model_users->getUser($user_id);

        $status_link = href_to_rel('users', (empty($user['slug']) ? $user['id'] : $user['slug'])) . '?wid='.$wall_entry_id.'&reply=1';

        return [
            $status_id,
            $user_id,
            $content,
            $status_content,
            $status_link
        ];

    }

}
