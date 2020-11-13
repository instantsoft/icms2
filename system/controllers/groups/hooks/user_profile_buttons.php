<?php

class onGroupsUserProfileButtons extends cmsAction {

    public function run($data){

        if ($data['profile']['id'] == $this->cms_user->id || $data['profile']['is_deleted']) { return $data; }

        if (!$this->cms_user->isPrivacyAllowed($data['profile'], 'invite_group_users')){
            return $data;
        }

        $my_groups = $this->model->getUserMemberships($this->cms_user->id);

        if ($my_groups && !$data['profile']['is_deleted'] && !$data['profile']['is_locked']){

            $data['buttons'][] = array(
                'title' => LANG_GROUPS_INVITE_USER,
                'class' => 'group_add ajax-modal', 'icon' => 'user-plus',
                'href'  => href_to($this->name, 'invite', $data['profile']['id'])
            );

        }

        return $data;

    }

}
