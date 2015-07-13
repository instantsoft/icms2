<?php

class actionGroupsInvite extends cmsAction {

    public function run($invited_id){

        $user = cmsUser::getInstance();

        // Группы, в которые можно приглашать
        $my_groups = $this->model->getInvitableGroups($user->id);

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

        $is_submitted = $this->request->has('submit');

        if ($is_submitted && $my_groups){

            $group_id = $this->request->get('group_id');

            if (!isset($my_groups[$group_id])){ cmsCore::error404(); }

            if ($this->model->getInvite($group_id, $invited_id)){
                cmsUser::addSessionMessage(LANG_GROUPS_INVITE_PENDING, 'info');
                $this->redirectBack();
            }

            return $this->sendInvite($invited_id, $group_id);

        }

        return cmsTemplate::getInstance()->render('invite', array(
            'invited_id' => $invited_id,
            'groups' => $my_groups,
        ));

    }

}
