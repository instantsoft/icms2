<?php

class modelGroups extends cmsModel{

//============================================================================//
//============================================================================//

    public function addGroup($group){

        $user = cmsUser::getInstance();

        $group['owner_id'] = $user->id;
        $group['date_pub'] = null;

        $group_id = $this->insert('groups', $group);

        if ($group_id){
            $this->addMembership($group_id, $user->id, groups::ROLE_STAFF);
        }

        cmsCache::getInstance()->clean("groups.list");

        return $group_id;

    }

    public function updateGroup($id, $group){

        unset($group['owner_nickname']);

        $result = $this->update('groups', $id, $group);

        cmsCore::getModel('content')->toggleParentVisibility('group', $id, $group['is_closed']);

        cmsCache::getInstance()->clean("groups.list");

        $this->updateGroupContentTitles($id, $group['title']);

        return $result;

    }

    public function updateGroupContentTitles($id, $new_group_title){

        $counts = $this->getGroupContentCounts($id);

        if (!$counts) { return true; }

        $content_model = cmsCore::getModel('content');

        foreach(array_keys($counts) as $ctype_name){

            $content_model->
                    filterEqual('parent_id', $id)->
                    filterEqual('parent_type', 'group');

                $content_model->updateFiltered($content_model->getContentTypeTableName($ctype_name), array(
                    'parent_title' => $new_group_title,
                ));

        }

        return true;

    }

    public function removeContentFromGroup($id, $is_delete=false){

        $counts = $this->getGroupContentCounts($id);

        if (!$counts) { return true; }

        $content_model = cmsCore::getModel('content');

        foreach(array_keys($counts) as $ctype_name){

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
                    'parent_id' => null,
                    'parent_type' => null,
                    'parent_title' => null,
                    'parent_url' => null,
                ));

            }

        }

        return true;

    }

    public function deleteGroup($group){

        $this->deleteGroupMemberships($group['id']);
        $this->deleteGroupInvites($group['id']);

        cmsCache::getInstance()->clean('groups.list');

        if($group['logo']){

            if (!is_array($group['logo'])){ $group['logo'] = cmsModel::yamlToArray($group['logo']); }

            $config = cmsConfig::getInstance();

            foreach($group['logo'] as $image_url){
                $image_path = $config->upload_path . $image_url;
                @unlink($image_path);
            }

        }

        return $this->delete('groups', $group['id']);

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

        cmsCache::getInstance()->clean("groups.list");

    }

//============================================================================//
//============================================================================//

    public function filterByMember($user_id){

        return $this->join('groups_members', 'm', "i.id = m.group_id AND m.user_id = '{$user_id}'");

    }

//============================================================================//
//============================================================================//

    public function getGroupsCount(){

        return $this->getCount('groups');

    }

    public function getGroups(){

        $this->useCache('groups.list');

        return $this->get('groups');

    }

    public function getGroupsIds(){

        $this->selectOnly('i.id', 'id');
		$this->filterNotEqual('i.is_closed', 1);

        return $this->get('groups');

    }

    public function getGroup($id){

        $this->select('u.nickname', 'owner_nickname');

        $this->join('{users}', 'u', 'u.id = i.owner_id');

        return $this->getItemById('groups', $id);

    }

    public function getUserGroups($user_id){

        $this->select('g.id', 'id');
        $this->select('g.*');

        $this->joinInner('groups', 'g', 'g.id = i.group_id');

        $this->filterEqual('user_id', $user_id);

        if (!$this->order_by){
            $this->orderBy('g.title');
        }

        return $this->get('groups_members');

    }

//============================================================================//
//============================================================================//

    public function addMembership($group_id, $user_id, $role = groups::ROLE_MEMBER){

        $id = $this->insert('groups_members', array(
            'group_id' => $group_id,
            'user_id' => $user_id,
            'role' => $role,
        ));

        if ($id){
            $this->filterEqual('id', $group_id)->increment('groups', 'members_count');
        }

        cmsCache::getInstance()->clean("groups.members");

        return $id;

    }

    public function deleteMembership($group_id, $user_id){

        $result = $this->
                    filterEqual('group_id', $group_id)->
                    filterEqual('user_id', $user_id)->
                    deleteFiltered('groups_members');

        if ($result){
            $this->filterEqual('id', $group_id)->decrement('groups', 'members_count');
        }

        cmsCache::getInstance()->clean("groups.members");

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
                    get('groups_members');

    }

    public function deleteGroupMemberships($group_id){

        cmsCache::getInstance()->clean("groups.members");

        return $this->delete('groups_members', $group_id, 'group_id');

    }

    public function deleteUserMemberships($user_id){

        cmsCache::getInstance()->clean("groups.members");

        $groups_ids = array_collection_to_list($this->getUserGroups($user_id), 'id', 'id');

        if (!$groups_ids) { return false; }

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

        cmsCache::getInstance()->clean("groups.members");

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

        $this->joinInner('groups_members', 'm', "m.user_id = '{$user_id}'");

        $groups = $this->get('groups', function($group, $model){

            $is_can_invite = in_array($group['join_policy'], array(groups::JOIN_POLICY_FREE, groups::JOIN_POLICY_PUBLIC));
            $is_can_invite = $is_can_invite || (($group['join_policy'] == groups::JOIN_POLICY_PRIVATE) && ($group['role'] == groups::ROLE_STAFF));

            if (!$is_can_invite){ return false; }

            return $group;

        });

        return $groups;

    }

    public function getInvitableFriends($group_id){

        $user = cmsUser::getInstance();

        $users_model = cmsCore::getModel('users');

        $friends = $users_model->orderBy('u.nickname')->getFriends($user->id);

        if (!$friends) { return false; }

        $group_members = $this->getMembersIds($group_id);

        foreach($friends as $id=>$friend){
            if (in_array($id, $group_members)){
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

    public function filterUsersMembers($group_id, $users_model){

        $users_model->joinInner('groups_members', 'm', "user_id = i.id AND m.group_id = '{$group_id}'");

    }

//============================================================================//
//============================================================================//

    public function getGroupContentCounts($id){

        $counts = array();

        $content_model = cmsCore::getModel('content');

        $ctypes = $content_model->getContentTypes();

        foreach($ctypes as $ctype){

            $content_model->filterEqual('parent_id', $id);
            $content_model->filterEqual('parent_type', 'group');

            $count = $content_model->getContentItemsCount( $ctype['name'] );

            if ($count) {

                $counts[ $ctype['name'] ] = array(
                    'count' => $count,
                    'is_in_list' => $ctype['options']['list_on'],
                    'title' => empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']
                );

            }

        }

        return $counts;

    }

//============================================================================//
//============================================================================//


}

