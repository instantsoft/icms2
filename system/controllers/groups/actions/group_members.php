<?php

class actionGroupsGroupMembers extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group, $role_id = null){

        $users_controller = cmsCore::getController('users', $this->request);

        $this->model->filterUsersMembers($group['id'], $users_controller->model);

        if($role_id !== null){
            if($role_id > 0){
                $users_controller->model->joinInner('groups_member_roles', 'mr', 'mr.user_id = i.id');
                $users_controller->model->filterEqual('mr.group_id', $group['id']);
                $users_controller->model->filterEqual('mr.role_id', $role_id);
            }
            if($role_id == -1){
                $users_controller->model->filterEqual('m.role', groups::ROLE_STAFF);
            }
        }

        $page_url = href_to($this->name, $group['slug'], 'members');

        $group['sub_title'] = LANG_GROUPS_GROUP_MEMBERS;
        if($role_id == -1){
            $group['sub_title'] = LANG_GROUPS_EDIT_STAFF;
        } elseif(isset($group['roles'][$role_id])){
            $group['sub_title'] = $group['roles'][$role_id];
        }

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb($group['sub_title']);

        $profiles_list_html = $users_controller->renderProfilesList($page_url, false, array(
            array(
                'title'   => LANG_GROUPS_SET_ROLES,
                'class'   => 'ajax-modal',
                'href'    => href_to('groups', 'set_roles', array($group['id'], '{id}')),
                'handler' => function($user) use($group){
                    return ($group['access']['is_owner'] || cmsUser::isAdmin()) && $group['roles'];
                }
            ),
            array(
                'title' => LANG_GROUPS_STAFF_SET,
                'class' => 'ajax-modal',
                'href'  => href_to('groups', 'set_staff', array($group['id'], '{id}')),
                'handler' => function($user) use($group){
                    return ($group['access']['is_owner'] || cmsUser::isAdmin()) && $user['member_role'] != groups::ROLE_STAFF;
                }
            ),
            array(
                'title' => LANG_GROUPS_STAFF_REMOVE,
                'class' => 'ajax-modal',
                'href'  => href_to('groups', 'remove_staff', array($group['id'], '{id}')),
                'handler' => function($user) use($group){
                    return ($group['access']['is_owner'] || cmsUser::isAdmin()) && $user['member_role'] == groups::ROLE_STAFF && $user['id'] != $group['owner_id'];
                }
            ),
            array(
                'title' => LANG_GROUPS_MEMBER_EXPEL,
                'class' => 'ajax-modal',
                'href'  => href_to('groups', 'expel', array($group['id'], '{id}')),
                'handler' => function($user) use($group){
                    return ($group['access']['is_owner'] || cmsUser::isAdmin()) && $user['member_role'] != groups::ROLE_STAFF;
                }
            )
        ));

        return $this->cms_template->render('group_members', array(
            'user'               => $this->cms_user,
            'group'              => $group,
            'current_role_id'    => $role_id,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
