<?php

class actionGroupsInviteFriends extends cmsAction {

    public function run($group_id){

        $user = cmsUser::getInstance();

        $membership = $this->model->getMembership($group_id, $user->id);
        if (!$membership) { cmsCore::error404(); }

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $is_owner = $user->id == $group['owner_id'];
        $is_can_invite = ($membership && ($group['join_policy'] != groups::JOIN_POLICY_PRIVATE)) || $is_owner;
        if (!$is_can_invite) { cmsCore::error404(); }

        $friends = $this->model->getInvitableFriends($group_id);

        $is_submitted = $this->request->has('submit');

        if ($is_submitted && $friends){

            $friends_list = $this->request->get('friends');
            $invited_list = array();

            if (!$friends_list) { $this->redirectBack(); }

            foreach($friends_list as $friend_id){
                if (!$this->model->getInvite($group_id, $friend_id)){
                    $invited_list[] = $friend_id;
                }
            }

            return $this->sendInvite($invited_list, $group_id);

        }

        return cmsTemplate::getInstance()->render('invite_friends', array(
            'group_id' => $group_id,
            'friends' => $friends,
        ));

    }

}
