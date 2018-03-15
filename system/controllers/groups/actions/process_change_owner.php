<?php

class actionGroupsProcessChangeOwner extends cmsAction {

    public function run($group_id, $owner_id, $action){

        if (!$this->request->isInternal()){ cmsCore::error404(); }

        if (!$group_id || !$owner_id) { return false; }

        $group = $this->model->getGroup($group_id);
        if (!$group) { return false; }

        $user = $this->model_users->getUser($owner_id);
        if (!$user) { return false; }

        if ($group['owner_id'] != $owner_id){
            return false;
        }

        if(!cmsUser::getUPS('change_owner_'.$user['id'])){
            return false;
        }

        $group_link = '<a href="'.href_to('groups', $group['id']).'">'.$group['title'].'</a>';
        $user_link  = '<a href="'.href_to_profile($this->cms_user).'">'.$this->cms_user->nickname.'</a>';
        $old_user_link = '<a href="'.href_to_profile($user).'">'.$user['nickname'].'</a>';

        if($action == 'accept'){

            $this->model->updateGroupOwner($group['id'], $this->cms_user->id);

            if(!empty($this->options['change_owner_email'])){

                $this->controller_messages->sendEmail($this->options['change_owner_email'], 'groups_change_owner', array(
                    'group_title' => $group['title'],
                    'group_url' => href_to('groups', $group['id']),
                    'old_profile_link' => $old_user_link,
                    'new_profile_link' => $user_link
                ));

            }

            $this->controller_messages->addRecipient($this->cms_user->id);

            $notice = array(
                'content' => sprintf(LANG_GROUPS_CHANGE_OWNER_SUCCESS, $group_link),
                'options' => array(
                    'is_closeable' => true
                )
            );

            $this->controller_messages->sendNoticePM($notice);

        }

        $this->controller_messages->clearRecipients()->addRecipient($owner_id);

        $notice = array(
            'content' => sprintf(string_lang('LANG_GROUPS_CHOWNR_NOTICE_'.$action), $group_link, $user_link),
            'options' => array(
                'is_closeable' => true
            )
        );

        $this->controller_messages->sendNoticePM($notice);

        cmsUser::deleteUPS('change_owner_'.$user['id']);

        return true;

    }

}
