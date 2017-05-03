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

    public function updateGroup($id, $group){

        $result = $this->update('groups', $id, $group);

        cmsCore::getModel('content')->toggleParentVisibility('group', $id, $group['is_closed']);

        cmsCache::getInstance()->clean('groups.list');

        $this->updateGroupContentParams($id, $group);

        $update = array('subject_title' => $group['title']);
        if(!empty($group['slug'])){
            $update['subject_url'] = href_to_rel('groups', $group['slug']);
        }

        $activity = cmsCore::getController('activity');

        $activity->updateEntry('groups', 'join', $id, $update);
        $activity->updateEntry('groups', 'leave', $id, $update);

        return $result;

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

    public function deleteGroup($group){

        $this->deleteGroupMemberships($group['id']);
        $this->deleteGroupInvites($group['id']);

        foreach($group['fields'] as $field){
            $field['handler']->delete($group[$field['name']]);
        }

        $this->filterEqual('group_id', $group['id'])->deleteFiltered('activity');

        cmsCache::getInstance()->clean('activity.entries');

        $success = $this->delete('groups', $group['id']);

        cmsCache::getInstance()->clean('groups.list');

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

        return $this->getCount('groups');

    }

    public function getGroups(){

        $this->useCache('groups.list');

        return $this->get('groups', function($group, $model){

            $group['slug'] = $group['slug'] ? $group['slug'] : $group['id'];

            return $group;

        });

    }

    public function getGroup($id, $field_name = 'id'){

        $this->select('u.nickname', 'owner_nickname');

        $this->join('{users}', 'u', 'u.id = i.owner_id');

        $group = $this->getItemByField('groups', $field_name, $id);

        if($group){
            $group['slug'] = $group['slug'] ? $group['slug'] : $group['id'];
        }

        return $group;

    }

    public function getGroupBySlug($slug){
        return $this->getGroup($slug, 'slug');
    }

    public function getUserGroups($user_id){

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
                    get('groups_members');

    }

    public function deleteGroupMemberships($group_id){

        cmsCache::getInstance()->clean('groups.members');

        return $this->delete('groups_members', $group_id, 'group_id');

    }

    public function deleteUserMemberships($user_id){

        cmsCache::getInstance()->clean('groups.members');

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

    public function getInvitableUsers($group_id){

        $users_model = cmsCore::getModel('users');

        $users = $users_model->orderBy('email')->getUsers();

        if (!$users) { return false; }

        $group_members = $this->getMembersIds($group_id);

        foreach($users as $user){
            if (in_array($user['id'], $group_members)){
                unset($users[$user['id']]);
            }
        }

        return $users;

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

    public function filterUsersMembers($group_id, $users_model){

        $users_model->joinInner('groups_members', 'm', "user_id = i.id AND m.group_id = '{$group_id}'");

    }

//============================================================================//
//============================================================================//

    public function getGroupContentCounts($id, $is_owner=false, $filter_callback = false){

        $counts = array();

        $content_model = cmsCore::getModel('content');

        if ($is_owner){
            $content_model->disableApprovedFilter();
			$content_model->disablePubFilter();
        }

        $ctypes = $content_model->getContentTypes();

        foreach($ctypes as $ctype){

            $content_model->filterEqual('parent_id', $id);
            $content_model->filterEqual('parent_type', 'group');

            if(is_callable($filter_callback)){
                $res = $filter_callback($ctype, $content_model);
                if($res === false){ continue; }
            }

            $count = $content_model->getContentItemsCount( $ctype['name'] );

            $counts[ $ctype['name'] ] = array(
                'count'      => $count,
                'is_in_list' => $ctype['options']['list_on'],
                'title'      => empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'],
                'title_add'  => $ctype['labels']['create']
            );

        }

        return $counts;

    }

}

