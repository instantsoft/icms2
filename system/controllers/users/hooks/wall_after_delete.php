<?php

class onUsersWallAfterDelete extends cmsAction {

    public function run($entry){

        if ($entry['status_id']){

            $profile = $this->model->getUser($entry['profile_id']);

            if($profile && $profile['status_id'] == $entry['status_id']){
                $this->model->clearUserStatus($entry['profile_id']);
            }

        }

    }

}
