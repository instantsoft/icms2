<?php

class onGroupsWallPermissions extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($profile_type, $profile_id) {

        if ($profile_type !== 'group') {
            return false;
        }

        $group = $this->model->getGroup($profile_id);

        if(!$group){
            return false;
        }

        $group['access'] = $this->getGroupAccess($group);

        return $group['access']['wall'];
    }

}
