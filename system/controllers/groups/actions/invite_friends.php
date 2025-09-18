<?php

class actionGroupsInviteFriends extends cmsAction {

    public function run($group_id) {

        $group = $this->model->getGroup($group_id);
        if (!$group) {
            return cmsCore::error404();
        }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_can_invite']) {
            return cmsCore::error404();
        }

        $friends = $this->model->getInvitableFriends($group_id, $this->cms_user->id);

        if ($this->request->has('submit') && $friends) {

            $friends_list = $this->request->get('friends', []);
            $invited_list = [];

            if (!$friends_list) {
                return $this->redirectBack();
            }

            foreach ($friends_list as $friend_id) {
                if (is_numeric($friend_id) && !$this->model->getInvite($group_id, $friend_id)) {
                    $invited_list[] = $friend_id;
                }
            }

            if (!$invited_list) {
                return $this->redirectBack();
            }

            return $this->sendInvite($invited_list, $group_id);
        }

        return $this->cms_template->render('invite_friends', [
            'group_id' => $group_id,
            'friends'  => $friends
        ]);
    }

}
