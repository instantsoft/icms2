<?php

class actionGroupsGroup extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();

        // Стена
        if ($this->options['is_wall']){

            $wall_controller = cmsCore::getController('wall', $this->request);

            $wall_title = LANG_GROUPS_WALL;

            $wall_target = array(
                'controller' => 'groups',
                'profile_type' => 'group',
                'profile_id' => $group['id']
            );

            $is_owner = $user->id == $group['owner_id'];
            $membership = $this->model->getMembership($group['id'], $user->id);
            $is_member = ($membership !== false);
            $member_role = $is_member ? $membership['role'] : groups::ROLE_NONE;

            $wall_permissions = array(

                'add' =>$user->is_admin || (
                            $membership && (
                                ($group['wall_policy'] == groups::WALL_POLICY_MEMBERS) ||
                                ($group['wall_policy'] == groups::WALL_POLICY_STAFF && $member_role==groups::ROLE_STAFF) ||
                                $is_owner
                            )
                        ),

                'delete' => ($user->is_admin || $is_owner),

            );

            $wall_html = $wall_controller->getWidget($wall_title, $wall_target, $wall_permissions);

        }

        // Контент
        $content_counts = $this->model->getGroupContentCounts($group['id']);

        return cmsTemplate::getInstance()->render('group_view', array(
            'group' => $group,
            'content_counts' => $content_counts,
            'user' => $user,
            'wall_html' => isset($wall_html) ? $wall_html : false,
        ));

    }

}
