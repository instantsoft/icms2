<?php

class onGroupsContentBeforeList extends cmsAction {

    public function run($data){

        if($this->cms_user->is_admin){ return $data; }

        list($ctype, $items) = $data;

        if ($items){

            $items_to_groups = $groups_ids = $access_by_roles = $group_roles = $groups_access_by_roles = array();

            // сначала смотрим есть ли записи групп
            foreach($items as $id => $item){

                if ($item['parent_type'] != 'group') { continue; }

                if ($item['is_parent_hidden'] || in_array($item['is_private'], array(3, 4, 5))){

                    // для гостей показываем общее сообщение
                    if (!$this->cms_user->is_logged){
                        $items[$id]['private_item_hint'] = sprintf(LANG_GROUPS_CTYPE_ACCESS, $item['parent_title']);
                        continue;
                    }

                    // авторам всё показываем
                    if($this->cms_user->id == $item['user_id']){
                        $items[$id]['is_private_item'] = false;
                        continue;
                    }

                    $items_to_groups[] = $id;
                    $groups_ids[] = $item['parent_id'];

                    // отдельно складываем id групп с ограничением по ролям
                    if($item['is_private'] == 5){
                        $groups_access_by_roles[] = $item['parent_id'];
                    }

                }

            }

            if($items_to_groups){

                $my_groups = $this->model->filterIn('group_id', $groups_ids)->getUserMemberships($this->cms_user->id);

                // если есть огранчиения по ролям, получаем их для каждой группы
                if($groups_access_by_roles){

                    $_group_roles = $this->model->selectOnly('role_id')->select('group_id')->filterIn('group_id', $groups_access_by_roles)->
                            filterEqual('user_id', $this->cms_user->id)->
                            get('groups_member_roles', false, false);

                    if($_group_roles){
                        foreach ($_group_roles as $role) {
                            $group_roles[$role['group_id']][] = $role['role_id'];
                        }
                    }

                }

                // перебираем все записи, которые прикреплены к группам
                foreach($items_to_groups as $item_id){

                    $item = $items[$item_id];

                    $membership = !empty($my_groups[$item['parent_id']]);

                    if(!$membership){

                        $items[$id]['private_item_hint'] = sprintf(LANG_GROUPS_CTYPE_ACCESS, $item['parent_title']);

                        continue;

                    }

                    if($item['is_private'] == 3){

                        $items[$item_id]['is_private_item'] = false;
                        continue;

                    }

                    if($item['is_private'] == 4){

                        if(cmsUser::isAllowed($ctype['name'], 'add')){
                            $items[$item_id]['is_private_item'] = false;
                        } else {
                            $items[$id]['private_item_hint'] = LANG_ACCESS_DENIED;
                        }

                        continue;

                    }

                    if($item['is_private'] == 5){

                        $user_roles = !empty($group_roles[$item['parent_id']]) ? $group_roles[$item['parent_id']] : array();

                        if(!is_array($item['allow_groups_roles'])){
                            $item['allow_groups_roles'] = cmsModel::yamlToArray($item['allow_groups_roles']);
                        }

                        $is_can_view = $user_roles && $this->cms_user->isUserInGroups($user_roles, $item['allow_groups_roles']);

                        if($is_can_view){
                            $items[$item_id]['is_private_item'] = false;
                        } else {
                            $items[$id]['private_item_hint'] = LANG_GROUPS_ROLES_LIST_ACCESS;
                        }

                        continue;

                    }

                }

            }

        }

        return array($ctype, $items);

    }

}
