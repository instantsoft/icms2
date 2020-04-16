<?php

class onGroupsContentViewHidden extends cmsAction {

    public function run($data){

        $viewable     = $data['viewable'];
        $item         = $data['item'];
        $is_moderator = !empty($data['is_moderator']);
        $ctype        = !empty($data['ctype']) ? $data['ctype'] : array();

        if (!$viewable) { return $data; }

        if ($item['parent_type'] != 'group') { return $data; }

        if ($item['is_parent_hidden'] || in_array($item['is_private'], array(3, 4, 5))){

            if (!$this->cms_user->is_logged){ $data['viewable'] = false; return $data; }

            $membership = $this->model->getMembership($item['parent_id'], $this->cms_user->id);

            if($this->cms_user->is_admin || $is_moderator || $this->cms_user->id == $item['user_id']){
                return $data;
            }

            if ($membership === false ||
                    ($item['is_private'] == 4 && !cmsUser::isAllowed($ctype['name'], 'add')) ||
                    ($item['is_private'] == 5 && !empty($item['allow_groups_roles']))){

                $group = $this->model->getGroup($item['parent_id']);

                if($group){

                    if($membership){
                        if($item['is_private'] == 5){

                            $roles = $this->model->getUserRoles($group['id'], $this->cms_user->id);

                            $allow_groups_roles = cmsModel::yamlToArray($item['allow_groups_roles']);

                            $is_can_view = $roles && $this->cms_user->isUserInGroups($roles, $allow_groups_roles);

                            if($is_can_view){
                                return $data;
                            }

                            $allow_roles_name = array();

                            foreach ($group['roles'] as $role_id => $name) {
                                if(in_array($role_id, $allow_groups_roles)){
                                    $allow_roles_name[] = $name;
                                }
                            }

                            $data['access_text'] = sprintf(LANG_GROUPS_ROLES_ACCESS, $group['title'], implode(', ', $allow_roles_name));

                        } else {
                            $data['access_text'] = LANG_ACCESS_DENIED;
                        }
                    } else {
                        $data['access_text'] = sprintf(LANG_GROUPS_CTYPE_ACCESS, '<a href="'.href_to('groups', $group['slug']).'">'.$group['title'].'</a>');
                    }

                    $data['access_redirect_url'] = href_to('groups', $group['slug']);

                    $data['viewable'] = false;

                    return $data;

                }

            }

        }

        return $data;

    }

}
