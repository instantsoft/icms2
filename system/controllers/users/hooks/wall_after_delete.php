<?php

class onUsersWallAfterDelete extends cmsAction {

    public function run($entry){

        if ($entry['status_id']){

            $profile = $this->model->getUser($entry['profile_id']);

            if($profile && $profile['status_id'] == $entry['status_id']){
                $this->model->clearUserStatus($entry['profile_id']);
            }

        }

        if($entry['parent_id']){

            $parent_entry = $this->model->getItemById('wall_entries', $entry['parent_id']);

            if($parent_entry && $parent_entry['status_id']){
                $this->model->increaseUserStatusRepliesCount($parent_entry['status_id'], false);
            }

        }

    }

}
