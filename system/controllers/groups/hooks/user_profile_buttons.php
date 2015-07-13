<?php

class onGroupsUserProfileButtons extends cmsAction {

    public function run($data){

        $profile_id = $data['profile']['id'];

        $user = cmsUser::getInstance();

        if ($profile_id == $user->id) { return $data; }

        $my_groups = $this->model->getUserMemberships($user->id);

        if ($my_groups){

            $data['buttons'][] = array(
                'title' => LANG_GROUPS_INVITE,
                'class' => 'group_add ajax-modal',
                'href' => href_to($this->name, 'invite', $profile_id)
            );

        }

        return $data;

    }

}
