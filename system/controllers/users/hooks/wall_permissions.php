<?php

class onUsersWallPermissions extends cmsAction {

    public function run($profile_type, $profile_id){

        if ($profile_type != 'user') { return false; }

        $profile = $this->model->getUser($profile_id);

        return array(
            'add'    => $this->cms_user->is_logged && $this->cms_user->isPrivacyAllowed($profile, 'users_profile_wall'),
            'delete' => ($this->cms_user->is_admin || ($this->cms_user->id == $profile['id']))
        );

    }

}
