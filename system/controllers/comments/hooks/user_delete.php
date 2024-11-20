<?php

class onCommentsUserDelete extends cmsAction {

    public function run($user) {

        if (!empty($this->options['hide_deleted_user_comments'])) {
            $this->model->deleteUserComments($user['id']);
        }

        return $user;
    }

}
