<?php

class onGroupsWallPermissions extends cmsAction {

    public function run($profile_type, $profile_id){

        if ($profile_type != 'group') { return false; }

        $group = $this->model->getGroup($profile_id);

        $group['access'] = $this->getGroupAccess($group);

        return $group['access']['wall'];

    }

}
