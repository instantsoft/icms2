<?php
/**
 * @property \modelMessages $model
 */
class onMessagesUserDelete extends cmsAction {

    public function run($user) {

        $this->model->deleteUserMessages($user['id']);

        return $user;
    }

}
