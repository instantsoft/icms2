<?php

class actionGroupsInviteFriends extends cmsAction {

    public function run($group_id){

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_can_invite']){
            cmsCore::error404();
        }

        $friends = $this->model->getInvitableFriends($group_id, $this->cms_user->id);

        if ($this->request->has('submit') && $friends){

            $friends_list = $this->request->get('friends', array());
            $invited_list = array();

            if (!$friends_list) { $this->redirectBack(); }

            foreach($friends_list as $friend_id){
                if (!$this->model->getInvite($group_id, $friend_id)){
                    $invited_list[] = $friend_id;
                }
            }

            return $this->sendInvite($invited_list, $group_id);

        }

        return $this->cms_template->render('invite_friends', array(
            'group_id' => $group_id,
            'friends'  => $friends
        ));

    }

}
