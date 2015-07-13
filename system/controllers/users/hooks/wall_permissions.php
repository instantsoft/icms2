<?php

class onUsersWallPermissions extends cmsAction {

    public function run($profile_type, $profile_id){

        if ($profile_type != 'user') { return false; }

        $user = cmsUser::getInstance();

        $profile = $this->model->getUser($profile_id);

        return array(
            'add' => $user->is_logged && $user->isPrivacyAllowed($profile, 'users_profile_wall'),
            'delete' => ($user->is_admin || ($user->id == $profile['id'])),
        );

    }

}
