<?php

class actionGroupsInvite extends cmsAction {

    public function run($invited_id){

        if (!$invited_id) { cmsCore::error404(); }

        // профиль приглашаемого
        $profile = cmsCore::getModel('users')->getUser($invited_id);
        if (!$profile || $profile['id'] == $this->cms_user->id) { cmsCore::error404(); }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'invite_group_users')){
            cmsCore::error404();
        }

        // Группы, в которые можно приглашать
        $my_groups = $this->model->getInvitableGroups($this->cms_user->id);

        // Членства приглашаемого в группах
        $his_groups = $this->model->getUserMemberships($invited_id);

        // Убираем из списка группы, в которых уже состоит приглашаемый
        if (is_array($my_groups) && is_array($his_groups)){
            foreach($his_groups as $membership){

                if (isset($my_groups[$membership['group_id']])){
                    unset($my_groups[$membership['group_id']]);
                }

            }
        }

        if ($this->request->has('submit') && $my_groups){

            $group_id = $this->request->get('group_id', 0);

            if (!isset($my_groups[$group_id])){ cmsCore::error404(); }

            if ($this->model->getInvite($group_id, $invited_id)){
                cmsUser::addSessionMessage(LANG_GROUPS_INVITE_PENDING, 'info');
                $this->redirectBack();
            }

            return $this->sendInvite($invited_id, $group_id);

        }

        return $this->cms_template->render('invite', array(
            'invited_id' => $invited_id,
            'groups'     => $my_groups
        ));

    }

}
