<?php

class onUsersWallPermissions extends cmsAction {

    public function run($profile_type, $profile_id){

        if ($profile_type != 'user') { return false; }

        if(!is_array($profile_id)){
            $profile = $this->model->getUser($profile_id);
        } else {
            $profile = $profile_id;
        }

        return array(
            'add'    => $this->cms_user->is_logged && $this->cms_user->isPrivacyAllowed($profile, 'users_profile_wall') && !$profile['is_locked'] && !$profile['is_deleted'],
            'reply'  => $this->cms_user->is_logged && $this->cms_user->isPrivacyAllowed($profile, 'users_profile_wall_reply') && !$profile['is_locked'] && !$profile['is_deleted'],
            'delete' => ($this->cms_user->is_admin || ($this->cms_user->id == $profile['id']))
        );

    }

}
