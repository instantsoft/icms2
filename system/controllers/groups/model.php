<?php

class modelGroups extends cmsModel {

    public function addGroup($group){

        $group['date_pub'] = null;

        $group_id = $this->insert('groups', $group);

        if ($group_id){
            $this->addMembership($group_id, $group['owner_id'], groups::ROLE_STAFF);
        }

        cmsCache::getInstance()->clean('groups.list');

        return $group_id;

    }

    public function updateGroupOwner($id, $owner_id){

        $membership = $this->getMembership($id, $owner_id);

        if (!$membership) {

            $this->addMembership($id, $owner_id, groups::ROLE_STAFF);

        } elseif($membership['role'] != groups::ROLE_STAFF){

            $this->updateMembershipRole($id, $owner_id, groups::ROLE_STAFF);

        }

        cmsCache::getInstance()->clean('groups.list');

        return $this->update('groups', $id, array(
            'owner_id' => $owner_id
        ));

    }

    public function updateGroup($id, $group) {

        $result = $this->update('groups', $id, $group);

        cmsCore::getModel('content')->toggleParentVisibility('group', $id, $group['is_closed']);

        cmsCache::getInstance()->clean('groups.list');

        $this->updateGroupContentParams($id, $group);

        if (empty($group['id'])) {
            $group['id'] = $id;
        }

        cmsEventsManager::hook('groups_after_update', $group);

        return $result;
    }

    public function approveGroup($id, $moderator_user_id){

        $this->update('groups', $id, array(
            'is_approved'   => 1,
            'approved_by'   => $moderator_user_id,
            'date_approved' => ''
        ));

        cmsCache::getInstance()->clean('groups.list');

        return true;

    }

    public function updateGroupContentParams($id, $group){

        $counts = $this->getGroupContentCounts($id, true);
        if (!$counts) { return true; }

        $content_model = cmsCore::getModel('content');

        foreach($counts as $ctype_name => $count){

            if (!$count['count']) { continue; }

            $content_model->filterEqual('parent_id', $id)->filterEqual('parent_type', 'group');

            $update = array('parent_title' => $group['title']);
            if(!empty($group['slug'])){
                $update['parent_url'] = href_to_rel('groups', $group['slug'], array('content', $ctype_name));
            }

            $content_model->updateFiltered($content_model->getContentTypeTableName($ctype_name), $update);

        }

        return true;

    }

    public function removeContentFromGroup($id, $is_delete=false){

        $counts = $this->getGroupContentCounts($id, true);
        if (!$counts) { return true; }

        $content_model = cmsCore::getModel('content');

        foreach($counts as $ctype_name => $count){

            if (!$count['count']) { continue; }

            $content_model->
                    filterEqual('parent_id', $id)->
                    filterEqual('parent_type', 'group');

            if ($is_delete){

                $items = $content_model->getContentItems($ctype_name);

                foreach($items as $item){
                    $content_model->deleteContentItem($ctype_name, $item['id']);
                }

            }

            if (!$is_delete){

                $content_model->updateFiltered($content_model->getContentTypeTableName($ctype_name), array(
                    'parent_id'    => null,
                    'parent_type'  => null,
                    'parent_title' => null,
                    'parent_url'   => null
                ));

            }

        }

        return true;

    }

    public function deleteGroup($group) {

        cmsEventsManager::hook('content_groups_before_delete', $group);

        $this->deleteGroupMemberships($group['id']);
        $this->deleteGroupInvites($group['id']);

        $group['ctype'] = [];
        $group['ctype_name'] = 'groups';

        foreach ($group['fields'] as $field) {
            $field['handler']->setItem($group)->delete($group[$field['name']]);
        }

        $success = $this->delete('groups', $group['id']);

        cmsCache::getInstance()->clean('groups.list');

        $this->filterEqual('child_ctype_id', null);
        $this->filterEqual('child_item_id', $group['id']);
        $this->filterEqual('target_controller', 'groups');

        $this->deleteFiltered('content_relations_bind');

        cmsEventsManager::hook('content_groups_after_delete', $group);

        return $success;
    }

    public function deleteUserGroupsAndMemberships($user_id){

        $groups = $this->filterEqual('owner_id', $user_id)->getGroups();

        if (is_array($groups)){
            foreach($groups as $group){
                $this->deleteGroup($group);
            }
        }

        $this->deleteUserMemberships($user_id);
        $this->deleteUserInvites($user_id);

    }

//============================================================================//
//============================================================================//

    public function updateGroupRating($group_id, $score){

        $this->
            filterEqual('id', $group_id)->
            increment('groups', 'rating', $score);

        cmsCache::getInstance()->clean('groups.list');

    }

//============================================================================//
//============================================================================//

    public function filterByMember($user_id){

        $this->filterEqual('m.user_id', $user_id);

        return $this->join('groups_members', 'm', 'i.id = m.group_id');

    }

//============================================================================//
//============================================================================//

    public function getGroupsCount(){

        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }

        return $this->getCount('groups');

    }

    public function getGroups(){

        $this->useCache('groups.list');

        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }

        if (!$this->order_by){ $this->orderBy('date_pub', 'desc'); }

        return $this->get('groups', function($group, $model){

            $group['slug'] = $group['slug'] ? $group['slug'] : $group['id'];

            // для связи с типами контента
            // проверка для выборки при генерации сайтмапа
            if(isset($group['owner_id'])){
                $group['ctype_name'] = 'groups';
                $group['user_id'] = $group['owner_id'];
            }

            return $group;

        });

    }

    public function getGroup($id, $field_name = 'id'){

        $this->select('u.nickname', 'owner_nickname');
        $this->select('u.slug', 'owner_slug');

        $this->join('{users}', 'u', 'u.id = i.owner_id');

        $group = $this->getItemByField('groups', $field_name, $id);

        if($group){
            $group['slug'] = $group['slug'] ? $group['slug'] : $group['id'];
            $group['content_policy'] = cmsModel::yamlToArray($group['content_policy']);
            $group['content_groups'] = cmsModel::yamlToArray($group['content_groups']);
            $group['roles']          = cmsModel::yamlToArray($group['roles']);
            $group['content_roles']  = cmsModel::yamlToArray($group['content_roles']);
            $group['join_roles']     = cmsModel::yamlToArray($group['join_roles']);
            $group['owner'] = [
                'id'       => $group['owner_id'],
                'slug'     => $group['owner_slug'],
                'nickname' => $group['owner_nickname']
            ];
            // для связи с типами контента
            $group['ctype_name'] = 'groups';
            $group['user_id'] = $group['owner_id'];
        }

        return $group;

    }

    /**
     * Псевдоним для связей
     * @param integer $id
     * @return array
     */
    public function getContentItem($id){
        return $this->getGroup($id);
    }

    public function getContentTypeTableName($name){
        return 'groups';
    }

    public function getGroupBySlug($slug){
        return $this->getGroup($slug, 'slug');
    }

    public function getUserGroups($user_id){

        $this->useCache('groups.list');

        $this->select('g.id', 'id');
        $this->select('g.*');

        $this->joinInner('groups', 'g', 'g.id = i.group_id');

        $this->filterEqual('user_id', $user_id);

        if (!$this->order_by){
            $this->orderBy('g.title');
        }

        return $this->get('groups_members', function($group, $model){

            $group['slug'] = $group['slug'] ? $group['slug'] : $group['id'];

            return $group;

        });

    }

    public function addRole($group, $role) {

        $role_id = 1;

        $roles = array();

        if($group['roles']){

            $ids = array_keys($group['roles']);

            $role_id = max($ids) + 1;

            $roles = $group['roles'];

        }

        $roles[$role_id] = $role;

        $this->update('groups', $group['id'], array('roles' => $roles));

        cmsCache::getInstance()->clean('groups.list');

        return $role_id;

    }

    public function deleteRole($group, $role_id) {

        if(!isset($group['roles'][$role_id])){
            return false;
        }

        unset($group['roles'][$role_id]);

        $this->update('groups', $group['id'], array('roles' => $group['roles']));

        cmsCache::getInstance()->clean('groups.list');

        return true;

    }

    public function editRole($group, $role, $role_id) {

        if(!isset($group['roles'][$role_id])){
            return false;
        }

        $group['roles'][$role_id] = $role;

        $this->update('groups', $group['id'], array('roles' => $group['roles']));

        cmsCache::getInstance()->clean('groups.list');

        return true;

    }

    public function setUserRoles($group_id, $role_ids, $user_id) {

        $this->filterEqual('group_id', $group_id);
        $this->filterEqual('user_id', $user_id);
        $this->deleteFiltered('groups_member_roles');

        if(!$role_ids){ return false; }

        if(!is_array($role_ids)){
            $role_ids = array($role_ids);
        }

        foreach ($role_ids as $role_id) {
            $this->insert('groups_member_roles', array(
                'group_id' => $group_id,
                'user_id'  => $user_id,
                'role_id'  => $role_id
            ));
        }

        return true;

    }

    public function getUserRoles($group_id, $user_id) {
        return $this->filterEqual('group_id', $group_id)->
                filterEqual('user_id', $user_id)->
                get('groups_member_roles', function($item, $model){
                    return $item['role_id'];
                }, false);
    }

//============================================================================//
//============================================================================//

    public function addMembership($group_id, $user_id, $role = groups::ROLE_MEMBER){

        $id = $this->insert('groups_members', array(
            'group_id' => $group_id,
            'user_id'  => $user_id,
            'role'     => $role
        ));

        if ($id){
            $this->filterEqual('id', $group_id)->increment('groups', 'members_count');
        }

        cmsCache::getInstance()->clean('groups.members');

        return $id;

    }

    public function deleteMembership($group_id, $user_id){

        $this->filterEqual('group_id', $group_id);
        $this->filterEqual('user_id', $user_id);
        $this->deleteFiltered('groups_member_roles');

        $result = $this->
                    filterEqual('group_id', $group_id)->
                    filterEqual('user_id', $user_id)->
                    deleteFiltered('groups_members');

        if ($result){
            $this->filterEqual('id', $group_id)->decrement('groups', 'members_count');
        }

        cmsCache::getInstance()->clean('groups.members');

        return $result;

    }

    public function getMembership($group_id, $user_id){

        if (!$user_id) { return false; }

        $this->useCache('groups.members');

        return $this->
                    filterEqual('group_id', $group_id)->
                    filterEqual('user_id', $user_id)->
                    getItem('groups_members');

    }

    public function getUserMemberships($user_id){

        $this->useCache('groups.members');

        return $this->
                    filterEqual('user_id', $user_id)->
                    get('groups_members', false, 'group_id');

    }

    public function deleteGroupMemberships($group_id){

        $this->filterEqual('group_id', $group_id);
        $this->deleteFiltered('groups_member_roles');

        cmsCache::getInstance()->clean('groups.members');

        return $this->delete('groups_members', $group_id, 'group_id');

    }

    public function deleteUserMemberships($user_id){

        cmsCache::getInstance()->clean('groups.members');

        $groups_ids = array_collection_to_list($this->getUserGroups($user_id), 'id', 'id');

        if (!$groups_ids) { return false; }

        $this->filterEqual('user_id', $user_id);
        $this->deleteFiltered('groups_member_roles');

        $this->filterIn('id', $groups_ids);

        $this->decrement('groups', 'members_count');

        return $this->delete('groups_members', $user_id, 'user_id');

    }

    public function getMembersIds($group_id){

        $this->useCache('groups.members');

        return $this->
                    filterEqual('group_id', $group_id)->
                    get('groups_members', function($item, $model){

                        return $item['user_id'];

                    }, false);

    }

    public function getMembers($group_id, $role=false){

        $this->useCache('groups.members');

        $this->select('u.id', 'id');
        $this->select('u.nickname', 'nickname');
        $this->select('u.email', 'email');
        $this->select('u.avatar', 'avatar');
        $this->select('u.slug', 'slug');

        $this->join('{users}', 'u', 'u.id = i.user_id');

        if ($role !== false){
            $this->filterEqual('role', $role);
        }

        $this->filterEqual('group_id', $group_id);

        if (!$this->order_by){
            $this->orderBy ('date_updated');
        }

        return $this->get('groups_members');

    }

    public function updateMembershipRole($group_id, $user_id, $new_role){

        cmsCache::getInstance()->clean('groups.members');

        return $this->
                    filterEqual('group_id', $group_id)->
                    filterEqual('user_id', $user_id)->
                    updateFiltered('groups_members', array(
                        'role' => $new_role,
                        'date_updated' => null
                    ));

    }

//============================================================================//
//============================================================================//

    public function getInvitableGroups($user_id){

        $this->select('m.role', 'role');

        $this->joinInner('groups_members', 'm', 'm.group_id = i.id');
        $this->filterEqual('m.user_id', $user_id);

        $groups = $this->get('groups', function($group, $model){

            $is_can_invite = in_array($group['join_policy'], array(groups::JOIN_POLICY_FREE, groups::JOIN_POLICY_PUBLIC));
            $is_can_invite = $is_can_invite || (($group['join_policy'] == groups::JOIN_POLICY_PRIVATE) && ($group['role'] == groups::ROLE_STAFF));

            if (!$is_can_invite){ return false; }

            return $group;

        });

        return $groups;

    }

    public function getInvitableFriends($group_id, $for_user_id){

        $users_model = cmsCore::getModel('users');

        $friends = $users_model->orderBy('u.nickname')->getFriends($for_user_id);

        if (!$friends) { return false; }

        $group_members = $this->getMembersIds($group_id);

        foreach($friends as $id=>$friend){
            if (in_array($id, $group_members)){
                unset($friends[$id]);
            }
            if (isset($friend['privacy_options']['invite_group_users']) && !$friend['privacy_options']['invite_group_users']){
                unset($friends[$id]);
            }
        }

        return $friends;

    }

    public function addInvite($invite){

        return $this->insert('groups_invites', $invite);

    }

    public function getInvite($group_id, $invited_id){

        return $this->
                    filterEqual('group_id', $group_id)->
                    filterEqual('invited_id', $invited_id)->
                    getItem('groups_invites');

    }

    public function getInviteRequest($group_id, $user_id){

        return $this->
                    filterEqual('group_id', $group_id)->
                    filterEqual('user_id', $user_id)->
                    getItem('groups_invites');

    }

    public function deleteInvite($id){

        return $this->delete('groups_invites', $id);

    }

    public function deleteGroupInvites($group_id){

        return $this->delete('groups_invites', $group_id, 'group_id');

    }

    public function deleteUserInvites($user_id){

        $this->delete('groups_invites', $user_id, 'user_id');
        $this->delete('groups_invites', $user_id, 'invited_id');

    }

//============================================================================//
//============================================================================//

    public function filterExcludeUsersMembers($group_id, $users_model){

        $users_model->select('mr.id', 'is_send_invite');

        $users_model->joinLeft('groups_invites', 'mr', "mr.invited_id = i.id AND mr.group_id = '{$group_id}'");

        return $users_model->joinExcludingLeft('groups_members', 'm', 'm.user_id', 'i.id', "m.group_id = '{$group_id}'");

    }

    public function filterUsersMembers($group_id, $users_model){

        $users_model->select('m.role', 'member_role');
        $users_model->joinInner('groups_members', 'm', 'm.user_id = i.id');
        $users_model->filterEqual('m.group_id', $group_id);

        return $users_model;

    }

    public function filterUsersRequests($group_id, $users_model){

        $users_model->joinInner('groups_invites', 'mr', 'mr.user_id = i.id');
        $users_model->filterEqual('mr.group_id', $group_id);

        return $users_model;

    }

//============================================================================//
//============================================================================//

    public function getGroupContentCounts($id, $is_owner = false, $filter_callback = false){

        if(!is_array($id)){
            $group = array('id' => $id);
        } else {
            $group = $id;
        }

        $counts = array();

        $content_model = cmsCore::getModel('content');

        $ctypes = $content_model->getContentTypes();

        foreach($ctypes as $ctype){

            $content_model = cmsCore::getModel('content');

            if(is_callable($filter_callback)){
                $res = call_user_func_array($filter_callback, array($ctype, $content_model, $group));
                if($res === false){ continue; }
            }

            $content_model->filterEqual('parent_id', $group['id']);
            $content_model->filterEqual('parent_type', 'group');

            if ($is_owner){
                $content_model->disableApprovedFilter();
                $content_model->disablePubFilter();
                $content_model->disablePrivacyFilter();
            }

            $count = $content_model->getContentItemsCount( $ctype['name'] );

            unset($content_model);

            $counts[ $ctype['name'] ] = array(
                'count'      => $count,
                'is_in_list' => $ctype['is_in_groups'],
                'title'      => empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'],
                'title_add'  => $ctype['labels']['create']
            );

        }

        return $counts;

    }

}
