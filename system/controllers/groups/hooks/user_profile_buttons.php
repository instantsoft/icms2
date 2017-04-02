<?php

class onGroupsUserProfileButtons extends cmsAction {

    public function run($data){

        if ($data['profile']['id'] == $this->cms_user->id || $data['profile']['is_deleted']) { return $data; }

        $my_groups = $this->model->getUserMemberships($this->cms_user->id);

        if ($my_groups){

            $data['buttons'][] = array(
                'title' => LANG_GROUPS_INVITE,
                'class' => 'group_add ajax-modal',
                'href'  => href_to($this->name, 'invite', $data['profile']['id'])
            );

        }

        return $data;

    }

}
