<?php

class onGroupsWallPermissions extends cmsAction {

    public function run($profile_type, $profile_id){

        if ($profile_type != 'group') { return false; }

        $group = $this->model->getGroup($profile_id);

        $is_owner    = $this->cms_user->id == $group['owner_id'];
        $membership  = $this->model->getMembership($group['id'], $this->cms_user->id);
        $is_member   = ($membership !== false);
        $member_role = $is_member ? $membership['role'] : groups::ROLE_NONE;

        return array(

            'add' => $this->cms_user->is_admin || (
                        $membership && (
                            ($group['wall_policy'] == groups::WALL_POLICY_MEMBERS) ||
                            ($group['wall_policy'] == groups::WALL_POLICY_STAFF && $member_role==groups::ROLE_STAFF) ||
                            $is_owner
                        )
                    ),

            'delete' => ($this->cms_user->is_admin || $is_owner)

        );

    }

}
