<?php

class onUsersWallPermissions extends cmsAction {

    public function run($profile_type, $profile_id) {

        if ($profile_type !== 'user') {
            return false;
        }

        if (!is_array($profile_id)) {
            $profile = $this->model->getUser($profile_id);
        } else {
            $profile = $profile_id;
        }

        return [
            'add'    => cmsUser::isAllowed('users', 'wall_add') && $this->cms_user->isPrivacyAllowed($profile, 'users_profile_wall') && !$profile['is_locked'] && !$profile['is_deleted'],
            'reply'  => cmsUser::isAllowed('users', 'wall_add') && $this->cms_user->isPrivacyAllowed($profile, 'users_profile_wall_reply') && !$profile['is_locked'] && !$profile['is_deleted'],
            'delete_handler' => function($entry) use($profile) {

                $is_wall_delete = false;

                if (cmsUser::isAllowed('users', 'wall_delete')) {
                    $is_wall_delete = true;
                }

                if (!cmsUser::isAllowed('users', 'wall_delete', 'all')) {
                    if (cmsUser::isAllowed('users', 'wall_delete', 'own')) {
                        if ($profile['id'] != $this->cms_user->id) {
                            $is_wall_delete = false;
                        }
                        if($entry['user']['id'] == $this->cms_user->id){
                            $is_wall_delete = true;
                        }
                    }
                }

                return $is_wall_delete;
            }
        ];
    }

}
