<?php

class modelUsers extends cmsModel {

//============================================================================//
//========================    ПОЛЬЗОВАТЕЛИ   =================================//
//============================================================================//

    public function getUsersCount(){

        $this->useCache('users.list');

        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }

        return $this->getCount('{users}');

    }

//============================================================================//
//============================================================================//

    public function filterGroup($group_id){
        return $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$group_id}'");
    }

    public function filterGroups($groups_list){
        $groups_list = implode(',', $groups_list);
        return $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id IN ({$groups_list})");
    }

    public function filterGroupByName($group_name){
        $this->join('{users}_groups_members', 'm', "m.user_id = i.id");
        return $this->join('{users}_groups', 'g', "g.id = m.group_id AND g.name = '{$group_name}'");
    }

    public function getUsers($actions = false){

        $this->useCache('users.list');

        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }

        return $this->get('{users}', function($user) use ($actions){

            $user['groups']    = cmsModel::yamlToArray($user['groups']);
            $user['theme']     = cmsModel::yamlToArray($user['theme']);
            $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);
            $user['is_online'] = cmsUser::userIsOnline($user['id']);
            $user['item_css_class'] = array();
            $user['notice_title']   = array();
            $user['ctype_name']     = 'users';

            if (is_array($actions)){
                foreach($actions as $key => $action){

                    if (isset($action['handler'])){
                        $is_active = $action['handler']($user);
                    } else {
                        $is_active = true;
                    }

                    if (!$is_active){ continue; }

                    if(!empty($action['item_css_class'])){ $user['item_css_class'][] = $action['item_css_class']; }
                    if(!empty($action['notice_title'])){ $user['notice_title'][] = $action['notice_title']; }

                    if(empty($action['href'])){ continue; }

                    foreach($user as $cell_id => $cell_value){

                        if (is_array($cell_value) || is_object($cell_value)) { continue; }

                        $action['href']  = str_replace('{'.$cell_id.'}', $cell_value, $action['href']);
                        $action['title'] = str_replace('{'.$cell_id.'}', $cell_value, $action['title']);
                        $action['class'] = (isset($action['class']) ? $action['class'] : '');

                    }
                    $user['actions'][$key] = $action;
                }
            }

            return $user;

        });

    }

    public function getUsersIds(){

        $this->selectOnly('i.id', 'id');

        return $this->get('{users}');

    }

//============================================================================//
//============================================================================//

    public function getUser($id=false){

        $this->useCache('users.user.'.$id);

        $this->select('u.nickname', 'inviter_nickname');
        $this->joinLeft('{users}', 'u', 'u.id = i.inviter_id');

        if ($id){
            $user = $this->getItemById('{users}', $id);
        } else {
            $user = $this->getItem('{users}');
        }

        if (!$user) { return false; }

        $user['groups']          = cmsModel::yamlToArray($user['groups']);
        $user['theme']           = cmsModel::yamlToArray($user['theme']);
        $user['notify_options']  = cmsModel::yamlToArray($user['notify_options']);
        $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);
        $user['is_online']       = cmsUser::userIsOnline($user['id']);
        $user['ctype_name']      = 'users';

        return $user;

    }

    public function getUserByEmail($email){

        return $this->filterEqual('email', $email)->getUser();

    }

    /**
     * Псевдоним для связей
     * @param integer $id
     * @return array
     */
    public function getContentItem($id){
        return $this->getUser($id);
    }

//============================================================================//
//============================================================================//

    public function setAuthToken($user_id, $auth_token, $type = null, $subj = null){

        if(!$type){ $type = cmsRequest::getDeviceType(); }

        return $this->insert('{users}_auth_tokens', array(
            'ip'          => sprintf('%u', ip2long(cmsUser::getIp())),
            'access_type' => array(
                'type' => $type,
                'subj' => $subj
            ),
            'auth_token'  => $auth_token,
            'user_id'     => $user_id
        ));

    }

    public function deleteExpiredToken($user_id, $auth_token_expiration_int){
        return $this->filterEqual('user_id', $user_id)->
                filterDateOlder('date_auth', $auth_token_expiration_int, 'SECOND')->
                deleteFiltered('{users}_auth_tokens');
    }

    public function deleteAuthToken($auth_token){
        return $this->filterEqual('auth_token', $auth_token)->deleteFiltered('{users}_auth_tokens');
    }

    public function deleteUserAuthTokens($user_id){
        return $this->filterEqual('user_id', $user_id)->deleteFiltered('{users}_auth_tokens');
    }

    public function getUserAuthTokens($user_id){
        return $this->filterEqual('user_id', $user_id)->get('{users}_auth_tokens', function ($item, $model){
            $item['ip'] = long2ip($item['ip']);
            $item['date_log'] = $item['date_log'] ? $item['date_log'] : $item['date_auth'];
            $item['access_type'] = cmsModel::yamlToArray($item['access_type']);
            return $item;
        });
    }

    public function getUserByPassToken($pass_token){

        return $this->filterEqual('pass_token', $pass_token)->getUser();

    }

    public function clearUserPassToken($id){

        return $this->updateUserPassToken($id, null);

    }

    public function updateUserPassToken($id, $pass_token=null){

        return $this->
                    filterEqual('id', $id)->
                    updateFiltered('{users}', array(
                        'pass_token' => $pass_token,
                        'date_token' => ''
                    ));

    }

//============================================================================//
//============================================================================//

    public function addUser($user){

        $errors = false;

        if ($user['password1'] != $user['password2']){
            $errors['password1'] = LANG_REG_PASS_NOT_EQUAL;
            $errors['password2'] = LANG_REG_PASS_NOT_EQUAL;
            return array( 'success'=>false, 'errors'=>$errors );
        }

        $date_reg = date('Y-m-d H:i:s');
        $date_log = $date_reg;

        $password_salt = md5(implode(':', array($user['password1'], session_id(), microtime(), uniqid())));
        $password_salt = substr($password_salt, rand(1,8), 16);
        $password_hash = md5(md5($user['password1']) . $password_salt);

        $groups = !empty($user['groups']) ? $user['groups'] : array(DEF_GROUP_ID);

        $user = array_merge($user, array(
            'groups'         => $groups,
            'password'       => $password_hash,
            'password_salt'  => $password_salt,
            'date_reg'       => $date_reg,
            'date_log'       => $date_log,
            'time_zone'      => cmsConfig::get('time_zone'),
            'notify_options' => $this->getUserNotifyTypes(true)
        ));

        $id = $this->insert('{users}', $user);

        if ($id){

            $this->saveUserGroupsMembership($id, $groups);

            cmsCore::getController('activity')->addEntry('users', 'signup', array(
                'user_id' => $id
            ));

        }

        cmsCache::getInstance()->clean('users.list');

        return array(
            'success' => $id !== false,
            'errors'  => false,
            'id'      => $id
        );

    }

//============================================================================//
//============================================================================//

    public function updateUser($id, $user){

        $success    = false;
        $errors     = false;

        if (!empty($user['email'])){

            $email_exists_id = $this->db->getField('{users}', "email = '{$user['email']}'", 'id');

            if ($email_exists_id && ($email_exists_id != $id)){
                $errors['email'] = LANG_REG_EMAIL_EXISTS;
            }

        }

        if (!empty($user['password1']) && !$errors){

            if (mb_strlen($user['password1']) < 6) {
                $errors['password1'] = sprintf(ERR_VALIDATE_MIN_LENGTH, 6);
            }

            if ($user['password1'] != $user['password2']){
                $errors['password2'] = LANG_REG_PASS_NOT_EQUAL;
            }

            $password_salt = md5(implode(':', array($user['password1'], session_id(), microtime(), uniqid())));
            $password_salt = substr($password_salt, rand(1,8), 16);
            $password_hash = md5(md5($user['password1']) . $password_salt);

            $user['password']      = $password_hash;
            $user['password_salt'] = $password_salt;

        }

        if (!$errors){

            if(isset($user['groups'])){

                $user['groups'] = is_array($user['groups']) ? $user['groups'] : array(DEF_GROUP_ID);

                $this->saveUserGroupsMembership($id, $user['groups']);

            }

            $success = $this->update('{users}', $id, $user);

        }

        cmsCache::getInstance()->clean("users.list");
        cmsCache::getInstance()->clean("users.user.{$id}");

        return array(
            'success' => $success,
            'errors' => $errors,
            'id' => $id
        );

    }

    public function updateUserTheme($id, $theme){

		$user = cmsUser::getInstance();

		$old_bg_img = isset($user->theme['bg_img']) ? $user->theme['bg_img'] : array();
		$new_bg_img = isset($theme['bg_img']) ? $theme['bg_img'] : array();

		if (($old_bg_img != $new_bg_img) && isset($old_bg_img['original'])){

			$config = cmsConfig::getInstance();

            foreach($old_bg_img as $path){
                @unlink($config->upload_path . $path);
            }

		}

        $res = $this->update('{users}', $id, array('theme'=>$theme));

        cmsCache::getInstance()->clean("users.user.{$id}");

        return $res;

    }

//============================================================================//
//============================================================================//
    /**
     * Удаляет пользователя
     * @param integer|array $user id пользователя или массив данных пользователя
     * @return boolean
     */
    public function deleteUser($user){

        if(is_numeric($user)){
            $user = $this->getUser($user);
            if(!$user){ return false; }
        }

        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $fields = $content_model->getContentFields('{users}', $user['id']);

        foreach($fields as $field){
            $field['handler']->delete($user[$field['name']]);
        }

        $this->deleteUserAuthTokens($user['id']);
        $this->delete('{users}_friends', $user['id'], 'user_id');
        $this->delete('{users}_friends', $user['id'], 'friend_id');
        $this->delete('{users}_groups_members', $user['id'], 'user_id');
        $this->delete('{users}_karma', $user['id'], 'user_id');
        $this->delete('{users}_statuses', $user['id'], 'user_id');
        $this->delete('{users}_personal_settings', $user['id'], 'user_id');
        $this->delete('{users}', $user['id']);

        $inCache = cmsCache::getInstance();
        $inCache->clean('users.list');
        $inCache->clean('users.ups');
        $inCache->clean('users.user.'.$user['id']);

        $this->filterEqual('child_ctype_id', null);
        $this->filterEqual('child_item_id', $user['id']);
        $this->filterEqual('target_controller', 'users');

        $this->deleteFiltered('content_relations_bind');

        return true;

    }

    public function setUserIsDeleted($id){
        $this->update('{users}', $id, array(
            'is_deleted' => 1
        ));
        cmsCache::getInstance()->clean("users.user.{$id}");
        return $this;
    }

    public function restoreUser($id){
        $this->update('{users}', $id, array(
            'is_deleted' => null
        ));
        cmsCache::getInstance()->clean("users.user.{$id}");
        return $this;
    }

    public function unlockUser($id){
        $this->update('{users}', $id, array(
            'is_locked' => null,
            'lock_until' => null,
            'lock_reason' => null
        ));
        cmsCache::getInstance()->clean("users.user.{$id}");
        return $this;
    }

//============================================================================//
//============================================================================//

    public function saveUserGroupsMembership($id, $groups){

        $this->delete('{users}_groups_members', $id, 'user_id');

        foreach($groups as $group_id){
            $this->insert('{users}_groups_members', array(
                'user_id' => $id,
                'group_id' => $group_id
            ));
        }

        cmsCache::getInstance()->clean("users.list");

    }

//============================================================================//
//============================================================================//

    public function isAvatarsEqual($old, $new){

        if (!is_array($old)){ $old = cmsModel::yamlToArray($old); }
        if (!is_array($new)){ $new = cmsModel::yamlToArray($new); }

        return $old == $new;

    }

//============================================================================//
//=========================    УВЕДОМЛЕНИЯ   =================================//
//============================================================================//

    public function getUserNotifyTypes($only_default_values = false) {

        $notify_types = cmsEventsManager::hookAll('user_notify_types');

        $default_options = array('', 'email', 'pm', 'both');

        $types = array();

        foreach($notify_types as $list){
            foreach($list as $name => $type){

                $options = array();

                if(!isset($type['options'])) { $type['options'] = $default_options; }

                foreach($type['options'] as $option){
                    if (!$option){
                        $options[''] = LANG_USERS_NOTIFY_VIA_NONE;
                    } else {
                        $options[$option] = constant('LANG_USERS_NOTIFY_VIA_'.strtoupper($option));
                    }
                }

                if(!$only_default_values){
                    $types[$name] = array(
                        'title'   => $type['title'],
                        'default' => (isset($type['default']) ? $type['default'] : 'email'),
                        'items'   => $options
                    );
                } else {
                    $types[$name] = (isset($type['default']) ? $type['default'] : 'email');
                }

            }
        }

        return $types;

    }

    public function getUserNotifyOptions($id){

        return $this->getItemById('{users}', $id, function($item, $model){
            return cmsModel::yamlToArray($item['notify_options']);
        });

    }

    public function updateUserNotifyOptions($id, $options){

        return $this->update('{users}', $id, array('notify_options'=>$options));

    }

    public function getNotifiedUsers($notice_type, $id_list, $options_only=array()){

        $list = array();

        $this->filterIn('id', $id_list);

        $this->filterIsNull('is_locked');
        $this->filterIsNull('is_deleted');

        $users = $this->get('{users}', function($user, $model){

            return array(
                'id' => $user['id'],
                'email' => $user['email'],
                'nickname' => $user['nickname'],
                'notify_options' => cmsModel::yamlToArray($user['notify_options'])
            );

        });

        if (!$users) { return false; }

        foreach($users as $user){

            if ($options_only){

                if (!isset($user['notify_options'][$notice_type])){
                    $user['notify_options'][$notice_type] = 'email';
                }

                if (empty($user['notify_options'][$notice_type])){
                    continue;
                }

                if (!in_array($user['notify_options'][$notice_type], $options_only)){
                    continue;
                }

            }

            unset($user['notify_options']);
            $list[] = $user;

        }

        return $list ? $list : false;

    }


//============================================================================//
//=========================    ПРИВАТНОСТЬ   =================================//
//============================================================================//

    public function getUserPrivacyOptions($id){

        return $this->getItemById('{users}', $id, function($item, $model){
            return cmsModel::yamlToArray($item['privacy_options']);
        });

    }

    public function updateUserPrivacyOptions($id, $options){

        return $this->update('{users}', $id, array('privacy_options'=>$options));

    }

//============================================================================//
//==============================    ГРУППЫ   =================================//
//============================================================================//

    public function getGroups($is_guests = false){

        if (!$is_guests) { $this->filterNotEqual('id', GUEST_GROUP_ID); }

        return $this->get('{users}_groups');

    }

    public function getPublicGroups(){

        return $this->filterNotEqual('id', GUEST_GROUP_ID)->
                        filterEqual('is_public', 1)->
                        get('{users}_groups');

    }

    public function getFilteredGroups(){

        return $this->filterNotEqual('id', GUEST_GROUP_ID)->
                        filterEqual('is_filter', 1)->
                        get('{users}_groups');

    }

    public function getGroup($id=false){

        return $this->getItemById('{users}_groups', $id);

    }

    public function updateGroup($id, $group){

        return $this->update('{users}_groups', $id, $group);

    }

    public function addGroup($group){

        return $this->insert('{users}_groups', $group);

    }

    public function deleteGroup($id){

        $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$id}'");

        $members = $this->disableDeleteFilter()->getUsers();

        if ($members){

            foreach($members as $user){

                $groups = $user['groups'];

                // удаляем ID из массива групп пользователя
                // и переиндексируем ключи массива
                $groups = array_values( array_diff($groups, array($id)) );

                $this->update('{users}', $user['id'], array(
                    'groups' => $groups
                ));

            }

            $this->delete('{users}_groups_members', $id, "group_id");

        }

        $this->delete('{users}_groups', $id);

        return true;

    }

//============================================================================//
//==============================    ДРУЖБА   =================================//
//============================================================================//

    public function filterFriends($user_id){
        $user_id = intval($user_id);
        $this->joinInner('{users}_friends', 'f', "friend_id = i.id AND f.is_mutual = 1 AND f.user_id = '{$user_id}'");
        return $this;
    }

    public function getFriends($user_id){

        $this->useCache('users.friends');

        $this->select('u.id', 'id');
        $this->select('u.*');

        $this->joinInner('{users}', 'u', 'u.id = i.friend_id');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        if (!$this->order_by){
            $this->orderBy('u.date_log', 'desc');
        }

        return $this->get('{users}_friends', function($user){

            $user['groups']          = cmsModel::yamlToArray($user['groups']);
            $user['notify_options']  = cmsModel::yamlToArray($user['notify_options']);
            $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);
            $user['is_online']       = cmsUser::userIsOnline($user['id']);

            return $user;

        });

    }


    public function getFriendsCount($user_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        $count = $this->getCount('{users}_friends');

        $this->resetFilters();

        return $count;
    }


    public function getFriendsIds($user_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        return $this->get('{users}_friends', function($item, $model){

            return $item['friend_id'];

        }, false);

    }

    public function isFriendshipRequested($user_id, $friend_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->filterEqual('is_mutual', 0);

        $is_exists = (bool)$this->getCount('{users}_friends');

        $this->resetFilters();

        return $is_exists;

    }

    public function isFriendshipExists($user_id, $friend_id){

        $this->useCache('users.friends');

        $this->filterStart();
        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->filterEnd();

        $this->filterOr();

        $this->filterStart();
        $this->filterEqual('user_id', $friend_id);
        $this->filterEqual('friend_id', $user_id);
        $this->filterEnd();

        $is_exists = (bool)$this->getCount('{users}_friends');

        $this->resetFilters();

        return $is_exists;

    }


    public function isFriendshipMutual($user_id, $friend_id){

        $this->useCache('users.friends');

        $this->filterStart();
            $this->filterStart();
            $this->filterEqual('user_id', $user_id);
            $this->filterEqual('friend_id', $friend_id);
            $this->filterEnd();

            $this->filterOr();

            $this->filterStart();
            $this->filterEqual('user_id', $friend_id);
            $this->filterEqual('friend_id', $user_id);
            $this->filterEnd();
        $this->filterEnd();

        $this->filterAnd();

        $this->filterEqual('is_mutual', 1);

        $is_exists = (bool)$this->getCount('{users}_friends');

        $this->resetFilters();

        return $is_exists;

    }


    public function addFriendship($user_id, $friend_id){

        $is_mutual = false;

        if ($this->isFriendshipRequested($friend_id, $user_id)){

            $this->filterEqual('user_id', $friend_id);
            $this->filterEqual('friend_id', $user_id);

            $this->updateFiltered('{users}_friends', array(
                'is_mutual' => true
            ));

            $is_mutual = true;

        }

        if ($is_mutual){

            $this->filterEqual('id', $user_id)->increment('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->increment('{users}', 'friends_count');

            $friend = $this->getUser($friend_id);

            cmsCore::getController('activity')->addEntry('users', 'friendship', array(
                'subject_title' => $friend['nickname'],
                'subject_id'    => $friend_id,
                'subject_url'   => href_to_rel('users', $friend_id)
            ));

        }

        cmsCache::getInstance()->clean('users.friends');

        return $this->insert('{users}_friends', array(
            'user_id'   => $user_id,
            'friend_id' => $friend_id,
            'is_mutual' => $is_mutual
        ));

    }

    public function deleteFriendship($user_id, $friend_id){

        if ($this->isFriendshipMutual($user_id, $friend_id)){
            $this->filterEqual('id', $user_id)->decrement('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->decrement('{users}', 'friends_count');
        }

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->deleteFiltered('{users}_friends');

        $this->filterEqual('user_id', $friend_id);
        $this->filterEqual('friend_id', $user_id);
        $this->deleteFiltered('{users}_friends');

        cmsCache::getInstance()->clean("users.friends");

    }

//============================================================================//
//=========================    ВКЛАДКИ ПРОФИЛЕЙ   ============================//
//============================================================================//

    public function getUsersProfilesTabs($only_active=false, $by_field='id'){

        $this->useCache('users.tabs');

        if ($only_active){ $this->filterEqual('is_active', 1); }

        return $this->orderBy('ordering')->get('{users}_tabs', function($item, $model){
            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            return $item;
        }, $by_field);

    }

    public function getUsersProfilesTab($tab_id){

        $this->useCache('users.tabs');

        return $this->getItemById('{users}_tabs', $tab_id);

    }

    public function updateUsersProfilesTab($id, $tab){

        cmsCache::getInstance()->clean('users.tabs');

        return $this->update('{users}_tabs', $id, $tab);

    }

    public function reorderUsersProfilesTabs($fields_ids_list){

        $this->reorderByList('{users}_tabs', $fields_ids_list);

        cmsCache::getInstance()->clean('users.tabs');

        return true;

    }


//============================================================================//
//==============================    СТАТУСЫ   ================================//
//============================================================================//

    public function getUserStatus($id){

        if (!$id) { return false; }

        $this->useCache('users.status');

        return $this->getItemById('{users}_statuses', $id);

    }

    public function addUserStatus($status){

        $id = $this->insert('{users}_statuses', $status);

        $this->update('{users}', $status['user_id'], array(
            'status_text' => $status['content'],
            'status_id' => $id
        ));

        cmsCache::getInstance()->clean("users.status");

        return $id;

    }

    public function clearUserStatus($user_id){

        cmsCache::getInstance()->clean("users.status");
        cmsCache::getInstance()->clean("users.list");
        cmsCache::getInstance()->clean("users.user.{$user_id}");

        $this->update('{users}', $user_id, array(
            'status_text' => null,
            'status_id' => null
        ));

    }

    public function increaseUserStatusRepliesCount($status_id){

        cmsCache::getInstance()->clean('users.status');

        $this->filterEqual('id', $status_id)->increment('{users}_statuses', 'replies_count');

    }

//============================================================================//
//============================    РЕПУТАЦИЯ   ================================//
//============================================================================//

    public function isUserCanVoteKarma($user_id, $profile_id, $voting_days=1){

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('profile_id', $profile_id);
        $this->filterDateYounger('date_pub', $voting_days);

        $this->useCache('users.karma');

        $votes_count = $this->getCount('{users}_karma');

        $this->resetFilters();

        return $votes_count > 0 ? false : true;

    }

    public function addKarmaVote($vote){

        cmsCache::getInstance()->clean('users.karma');

        $result = $this->insert('{users}_karma', $vote);

        if (!$result) { return false; }

        $this->
            filterEqual('id', $vote['profile_id'])->
            increment('{users}', 'karma', $vote['points']);

        return $result;

    }

    public function getKarmaLogCount($profile_id){

        $this->useCache('users.karma');

        $count = $this->filterEqual('profile_id', $profile_id)->getCount('{users}_karma');

        $this->resetFilters();

        return $count;

    }

    public function getKarmaLog($profile_id){

        $this->useCache('users.karma');

        $this->joinUser();

        $this->orderBy('id', 'desc');

        $this->filterEqual('profile_id', $profile_id);

        return $this->get('{users}_karma', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function updateUserRating($user_id, $score){

        $this->filterEqual('id', $user_id);

		if ($score > 0){
            $this->increment('{users}', 'rating', abs($score));
		}
		if ($score < 0){
            $this->decrement('{users}', 'rating', abs($score));
		}

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.'.$user_id);

    }

//============================================================================//
//============================================================================//

    public function getMigrationRulesCount(){

        return $this->getCount('{users}_groups_migration');

    }

    public function getMigrationRules(){

        return $this->get('{users}_groups_migration');

    }

    public function getMigrationRule($id){

        return $this->getItemById('{users}_groups_migration', $id);

    }

    public function addMigrationRule($rule){

        return $this->insert('{users}_groups_migration', $rule);

    }

    public function updateMigrationRule($id, $rule){

        return $this->update('{users}_groups_migration', $id, $rule);

    }

    public function deleteMigrationRule($id){

        return $this->delete('{users}_groups_migration', $id);

    }

//============================================================================//
//============================================================================//

    public function setUPS($key, $data, $user_id){
        if(is_array($data)){
            $data = self::arrayToYaml($data);
        }
        $insert = array(
            'user_id' => $user_id,
            'skey' => $key,
            'settings' => $data
        );
        $update = array(
            'settings' => $data
        );

        $ret = $this->insertOrUpdate('{users}_personal_settings', $insert, $update);
        cmsCache::getInstance()->clean('users.ups');

        return $ret;
    }

    public function getUPS($key, $user_id){
        $this->useCache('users.ups');

        $this->filterEqual('user_id', $user_id)->filterEqual('skey', $key);

        return $this->getItem('{users}_personal_settings', function($item, $model){
            if(strpos($item['settings'], '---') === 0){
                $item['settings'] = cmsModel::yamlToArray($item['settings']);
            }
            return $item['settings'];
        });
    }

    public function deleteUPS($key, $user_id){
        if($user_id && $key){
            $this->filterEqual('user_id', $user_id)->filterEqual('skey', $key);
        }elseif($user_id){
            $this->filterEqual('user_id', $user_id);
        }elseif($key){
            $this->filterEqual('skey', $key);
        }else{
            return false;
        }
        $ret = $this->deleteFiltered('{users}_personal_settings');
        cmsCache::getInstance()->clean('users.ups');

        return $ret;
    }

}
