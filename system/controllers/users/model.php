<?php
/**
 * Модель для работы с пользователями
 */
class modelUsers extends cmsModel {

    use icms\traits\controllers\models\transactable;

    public function getUsersCount($reset = false) {

        $this->useCache('users.list');

        if (!$this->delete_filter_disabled) { $this->filterAvailableOnly(); }

        return $this->getCount('{users}', 'id', $reset);
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

        $this->joinSessionsOnline('i');

        return $this->get('{users}', function($user) use ($actions){

            unset($user['pass_token'], $user['password'], $user['password_salt'], $user['password_hash']);

            $user['slug']            = !empty($user['slug']) ? $user['slug'] : $user['id'];
            $user['groups']          = cmsModel::yamlToArray($user['groups']);
            $user['notify_options']  = cmsModel::yamlToArray($user['notify_options']);
            $user['theme']           = cmsModel::yamlToArray($user['theme']);
            $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);
            $user['item_css_class']  = [];
            $user['notice_title']    = [];
            $user['ctype_name']      = 'users';

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
                        if (!$cell_value) { $cell_value = ''; }

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

        return $this->get('{users}', function($user){
            return $user['id'];
        });

    }

    public function makeProfileFields($fields, &$profiles, $user) {

        if($fields && $profiles){
            foreach ($profiles as $key => $profile) {
                foreach($fields as $field){

                    if ($field['is_system'] || !$field['is_in_list'] || !isset($profile[$field['name']])) { continue; }

                    // проверяем что группа пользователя имеет доступ к чтению этого поля
                    if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) {
                        // если группа пользователя не имеет доступ к чтению этого поля,
                        // проверяем на доступ к нему для авторов
                        if (empty($field['options']['author_access'])){ continue; }
                        if (!in_array('is_read', $field['options']['author_access'])){ continue; }
                        if ($profile['id'] != $user->id){ continue; }
                    }

                    if (!$profile[$field['name']] && $profile[$field['name']] !== '0') { continue; }

                    if (!isset($field['options']['label_in_list'])) {
                        $label_pos = 'none';
                    } else {
                        $label_pos = $field['options']['label_in_list'];
                    }

                    $field_html = $field['handler']->setItem($profile)->parseTeaser($profile[$field['name']]);
                    if (!$field_html) { continue; }

                    $profiles[$key]['fields'][$field['name']] = array(
                        'label_pos' => $label_pos,
                        'type'      => $field['type'],
                        'options'   => $field['options'],
                        'name'      => $field['name'],
                        'title'     => $field['title'],
                        'html'      => $field_html
                    );

                }
            }
        }

    }

//============================================================================//
//============================================================================//

    public function getUser($id = false, $join_inviter = false) {

        if($id){
            $this->useCache('users.user.'.$id);
        }

        if($join_inviter){
            $this->joinUser('inviter_id', [
                'u.nickname'   => 'inviter_nickname',
                'u.slug'       => 'inviter_slug',
                'u.is_deleted' => 'inviter_is_deleted',
                'u.avatar'     => 'inviter_avatar'
            ], 'left');
        }

        $this->joinSessionsOnline('i');

        if ($id){
            $user = $this->getItemById('{users}', $id);
        } else {
            // На случай, если по ошибке запросят без предварительного фильтра
            if (!$this->where){
                return false;
            }
            $user = $this->getItem('{users}');
        }

        if (!$user) { return false; }

        $user['slug']            = !empty($user['slug']) ? $user['slug'] : $user['id'];
        $user['groups']          = cmsModel::yamlToArray($user['groups']);
        $user['theme']           = cmsModel::yamlToArray($user['theme']);
        $user['notify_options']  = cmsModel::yamlToArray($user['notify_options']);
        $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);
        $user['ctype_name']      = 'users';
        $user['inviter'] = [
            'id' => $user['inviter_id']
        ];
        if (!empty($user['inviter_nickname'])) {
            $user['inviter'] = [
                'id'         => $user['inviter_id'],
                'nickname'   => $user['inviter_nickname'],
                'slug'       => $user['inviter_slug'],
                'is_deleted' => $user['inviter_is_deleted'],
                'avatar'     => $user['inviter_avatar']
            ];
        }

        return $user;
    }

    public function getUserBySlug($slug){

        if(!$slug){ return false; }

        return $this->filterEqual('slug', $slug)->getUser(false, true);
    }

    public function getUserByEmail($email){

        if(!$email){ return false; }

        return $this->filterEqual('email', $email)->getUser();
    }

    public function getUserByAuth($email, $password) {

        if(!$password){ return false; }

        $this->filterIsNull('is_deleted');

        $user = $this->getUserByEmail($email);

        // совместимость с ранее захэшированными паролями
        // ищем юзера по email, если есть,
        // проверяем по двум алгоритмам
        if($user){

            // старый механизм
            if(empty($user['password_hash'])){

                $password_hash = md5(md5($password) . $user['password_salt']);

                if ($password_hash !== $user['password']){
                    $user = false;
                } else {
                    // ставим метку в массив, что старая авторизация
                    $user['is_old_auth'] = true;
                }

            // новый механизм
            } else {

                if (!password_verify($password, $user['password_hash'])) {
                    $user = false;
                }

            }

        }

        return $user;

    }

    /**
     * Псевдоним для связей
     * @param integer $id
     * @return array
     */
    public function getContentItem($id){
        return $this->getUser($id);
    }

    public function getContentTypeTableName($name){
        return '{users}';
    }

//============================================================================//
//============================================================================//

    public function setAuthToken($user_id, $auth_token, $type = null, $subj = null){

        if(!$type){ $type = cmsRequest::getDeviceType(); }

        return $this->insert('{users}_auth_tokens', array(
            'ip' => function ($db){
                return '\''.$db->escape(string_iptobin(cmsUser::getIp())).'\'';
            },
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

    public function getUserAuthTokens($user_id) {

        return $this->filterEqual('user_id', $user_id)->get('{users}_auth_tokens', function ($item, $model) {

            $item['ip']          = string_bintoip($item['ip']);
            $item['ip_location'] = string_ip_to_location($item['ip']);
            $item['date_log']    = $item['date_log'] ? $item['date_log'] : $item['date_auth'];
            $item['access_type'] = cmsModel::yamlToArray($item['access_type']);

            return $item;
        });
    }

    public function getUserByPassToken($pass_token) {
        return $this->filterEqual('pass_token', $pass_token)->getUser();
    }

    public function clearUserPassToken($id) {
        return $this->updateUserPassToken($id, null);
    }

    public function updateUserPassToken($id, $pass_token = null){

        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this->
                    filterEqual('id', $id)->
                    updateFiltered('{users}', array(
                        'pass_token' => $pass_token,
                        'date_token' => ''
                    ));

    }

//============================================================================//
//============================================================================//

    public function addUser($user) {

        if ($user['password1'] !== $user['password2']) {

            return [
                'success' => false,
                'errors'  => [
                    'password1' => LANG_REG_PASS_NOT_EQUAL,
                    'password2' => LANG_REG_PASS_NOT_EQUAL
                ]
            ];
        }

        $user['password_hash'] = password_hash($user['password1'], PASSWORD_BCRYPT);

        if ($user['password_hash'] === false) {

            return [
                'success' => false,
                'errors'  => [
                    'password1' => LANG_ERROR,
                    'password2' => LANG_ERROR
                ]
            ];
        }

        $date_reg = date('Y-m-d H:i:s');
        $date_log = $date_reg;

        $groups = !empty($user['groups']) ? $user['groups'] : [DEF_GROUP_ID];

        $user = array_merge($user, [
            'groups'         => $groups,
            'date_reg'       => $date_reg,
            'date_log'       => $date_log,
            'time_zone'      => cmsConfig::get('time_zone'),
            'notify_options' => $this->getUserNotifyTypes(true)
        ]);

        $id = $this->insert('{users}', $user);

        if ($id) {
            $this->saveUserGroupsMembership($id, $groups);
        }

        cmsCache::getInstance()->clean('users.list');

        return [
            'success' => $id !== false,
            'errors'  => false,
            'id'      => $id
        ];
    }

//============================================================================//
//============================================================================//

    public function updateUser($id, $user) {

        $success = false;
        $errors  = [];

        if (!empty($user['email'])) {

            $email_exists_user = $this->getItemByField('{users}', 'email', $user['email']);

            if ($email_exists_user && ($email_exists_user['id'] != $id)) {
                $errors['email'] = LANG_REG_EMAIL_EXISTS;
            }
        }

        unset($user['password'], $user['password_hash']);

        if (!empty($user['password1']) && !$errors) {

            if (mb_strlen($user['password1']) < 6) {
                $errors['password1'] = sprintf(ERR_VALIDATE_MIN_LENGTH, 6);
            }

            if ($user['password1'] !== $user['password2']) {
                $errors['password2'] = LANG_REG_PASS_NOT_EQUAL;
            }

            // старым ячейкам стави null (< 2.12.1)
            $user['password']      = null;
            $user['password_salt'] = null;

            // хэш пароля пишем в новую ячейку (>=2.12.1)
            $user['password_hash'] = password_hash($user['password1'], PASSWORD_BCRYPT);

            if ($user['password_hash'] === false) {
                $errors['password1'] = LANG_ERROR;
            }
        }

        if (!$errors) {

            if (isset($user['groups'])) {

                $user['groups'] = is_array($user['groups']) ? $user['groups'] : array(DEF_GROUP_ID);

                $this->saveUserGroupsMembership($id, $user['groups']);
            }

            $success = $this->update('{users}', $id, $user);
        }

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $id);

        return [
            'success' => $success,
            'errors'  => $errors,
            'id'      => $id
        ];
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

        $res = $this->update('{users}', $id, array('theme' => $theme), true);

        cmsCache::getInstance()->clean('users.user.'.$id);

        return $res;

    }

//============================================================================//
//============================================================================//
    /**
     * Удаляет пользователя
     * @param integer|array $user id пользователя или массив данных пользователя
     * @return boolean
     */
    public function deleteUser($user) {

        if (is_numeric($user)) {
            $user = $this->getUser($user);
            if (!$user) {
                return false;
            }
        }

        $inCache = cmsCache::getInstance();
        $content_model = cmsCore::getModel('content');

        $fields = $content_model->setTablePrefix('')->getContentFields('{users}', $user['id']);

        $user['ctype']      = [];
        $user['ctype_name'] = 'users';

        foreach ($fields as $field) {
            $field['handler']->setItem($user)->delete($user[$field['name']]);
        }

        // уменьшаем счётчики друзей и подписчиков
        $data = $this->getFriendsIds($user['id']);

        if (!empty($data['friends'])) {
            foreach ($data['friends'] as $friend_id) {
                $this->filterEqual('id', $friend_id)->decrement('{users}', 'friends_count');
                $inCache->clean('users.user.' . $friend_id);
            }
        }
        if (!empty($data['subscribes'])) {
            foreach ($data['subscribes'] as $friend_id) {
                $this->filterEqual('id', $friend_id)->decrement('{users}', 'subscribers_count');
                $inCache->clean('users.user.' . $friend_id);
            }
        }

        $this->deleteUserAuthTokens($user['id']);
        $this->delete('{users}_friends', $user['id'], 'user_id');
        $this->delete('{users}_friends', $user['id'], 'friend_id');
        $this->delete('{users}_groups_members', $user['id'], 'user_id');
        $this->delete('{users}_karma', $user['id'], 'user_id');
        $this->delete('{users}_statuses', $user['id'], 'user_id');
        $this->delete('{users}_personal_settings', $user['id'], 'user_id');
        $this->delete('{users}', $user['id']);

        $inCache->clean('users.list');
        $inCache->clean('users.ups');
        $inCache->clean('users.user.' . $user['id']);
        $inCache->clean('users.status');

        $this->filterEqual('child_ctype_id', null);
        $this->filterEqual('child_item_id', $user['id']);
        $this->filterEqual('target_controller', 'users');

        $this->deleteFiltered('content_relations_bind');

        return true;
    }

    public function updateUserIp($id, $ip = false){

        $this->update('{users}', $id, array(
            'ip' => ($ip ? $ip : cmsUser::getIp())
        ), true);

        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this;

    }

    public function updateUserDateLog($id){

        $this->update('{users}', $id, array(
            'date_log' => null
        ), true);

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this;

    }

    public function setUserIsDeleted($id){

        $this->update('{users}', $id, array(
            'is_deleted' => 1
        ), true);

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this;

    }

    public function restoreUser($id){

        $this->update('{users}', $id, array(
            'is_deleted' => null
        ), true);

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this;

    }

    public function unlockUser($id){

        $this->update('{users}', $id, array(
            'is_locked'   => null,
            'lock_until'  => null,
            'lock_reason' => null
        ), true);

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.'.$id);

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
//=========================    УВЕДОМЛЕНИЯ   =================================//
//============================================================================//

    public function getUserNotifyTypes($only_default_values = false) {

        $notify_types = cmsEventsManager::hookAll('user_notify_types');

        $notify_types = cmsEventsManager::hook('update_user_notify_types', $notify_types);

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

        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this->update('{users}', $id, array('notify_options' => $options), true);

    }

    public function getNotifiedUsers($notice_type = false, $id_list = array(), $options_only = array(), $default = 'email'){

        $list = array();

        $this->selectList(array(
            'i.id'             => 'id',
            'i.email'          => 'email',
            'i.slug'           => 'slug',
            'i.nickname'       => 'nickname',
            'i.notify_options' => 'notify_options'
        ), true);

        if($id_list){
            $this->filterIn('id', $id_list);
        }

        $this->filterIsNull('is_locked');
        $this->filterIsNull('is_deleted');

        $users = $this->get('{users}', function($user, $model){

            $user['slug']           = !empty($user['slug']) ? $user['slug'] : $user['id'];
            $user['notify_options'] = cmsModel::yamlToArray($user['notify_options']);

            return $user;

        }, false);

        if (!$users) { return false; }

        if ($options_only){
            foreach($users as $user){

                if (!isset($user['notify_options'][$notice_type])){
                    $user['notify_options'][$notice_type] = $default;
                }

                if (empty($user['notify_options'][$notice_type])){
                    continue;
                }

                if (!in_array($user['notify_options'][$notice_type], $options_only)){
                    continue;
                }

                $list[] = $user;

            }
        } else {
            $list = $users;
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

        cmsCache::getInstance()->clean('users.user.'.$id);

        return $this->update('{users}', $id, array('privacy_options' => $options), true);

    }

//============================================================================//
//==============================    ГРУППЫ   =================================//
//============================================================================//

    public function getGroups($is_guests = false){

        if (!$is_guests) { $this->filterNotEqual('id', GUEST_GROUP_ID); }

        $this->orderBy('ordering', 'asc');

        return $this->get('{users}_groups');

    }

    public function getPublicGroups(){
        return $this->filterEqual('is_public', 1)->getGroups();
    }

    public function getFilteredGroups(){
        return $this->filterEqual('is_filter', 1)->getGroups();
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

    public function deleteGroup($id) {

        $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$id}'");

        $members = $this->disableDeleteFilter()->getUsers();

        $first_group = $this->orderBy('id', 'asc')->filterNotEqual('id', GUEST_GROUP_ID)->getItem('{users}_groups');
        if (!$first_group) { return false; }

        if ($members) {

            foreach ($members as $user) {

                $groups = $user['groups'];

                // удаляем ID из массива групп пользователя
                // и переиндексируем ключи массива
                $groups = array_values(array_diff($groups, array($id)));

                if (!$groups) {
                    $groups = array($first_group['id']);
                }

                $this->update('{users}', $user['id'], array(
                    'groups' => $groups
                ), true);

                cmsCache::getInstance()->clean('users.user.' . $id);
            }

            cmsCache::getInstance()->clean('users.list');

            $this->delete('{users}_groups_members', $id, 'group_id');
        }

        $this->filterEqual('group_id', $id)->deleteFiltered('perms_users');

        return $this->delete('{users}_groups', $id);
    }

//============================================================================//
//==============================    ДРУЖБА   =================================//
//============================================================================//

    public function filterFriends($user_id, $is_mutual = 1) {

        $this->joinInner('{users}_friends', 'f', 'f.friend_id = i.id');

        $this->filterEqual('f.user_id', (int) $user_id);

        if ($is_mutual !== null) {
            $this->filterEqual('f.is_mutual', $is_mutual);
        } else {
            // подписчики (null) и друзья (1)
            $this->filterStart();
            $this->filterEqual('f.is_mutual', 1);
            $this->filterOr();
            $this->filterIsNull('f.is_mutual');
            $this->filterEnd();
        }

        return $this;
    }

    public function getFriends($user_id){

        $this->useCache('users.friends');

        $this->select('u.id', 'id');
        $this->select('u.*');

        $this->joinInner('{users}', 'u', 'u.id = i.friend_id');
        $this->joinSessionsOnline();

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        if (!$this->order_by){
            $this->orderBy('u.date_log', 'desc');
        }

        return $this->get('{users}_friends', function($user){

            $user['groups']          = cmsModel::yamlToArray($user['groups']);
            $user['notify_options']  = cmsModel::yamlToArray($user['notify_options']);
            $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);

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

    public function getSubscribersCount($user_id){

        $this->filterEqual('friend_id', $user_id);
        $this->filterIsNull('is_mutual');

        $count = $this->getCount('{users}_friends');

        $this->resetFilters();

        return $count;
    }

    public function cacheSubscribersCount($user_id){

        cmsCache::getInstance()->clean('users.user.'.$user_id);

        $success = $this->update('{users}', $user_id, ['subscribers_count' => $this->getSubscribersCount($user_id)], true);

        cmsEventsManager::hook('users_subscribers_count_update', $user_id);

        return $success;
    }

    public function getFriendsIds($user_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);

        $data = array(
            'friends' => array(),
            'subscribes' => array()
        );

        $items = $this->get('{users}_friends', false, false);

        if($items){
            foreach ($items as $item) {
                if($item['is_mutual'] !== null){
                    if($item['is_mutual']){
                        $data['friends'][] = $item['friend_id'];
                    }
                } else {
                    $data['subscribes'][] = $item['friend_id'];
                }
            }
        }

        return $data;
    }

    public function getFriendshipRequested($user_id, $friend_id, $field = 'is_mutual'){

        if(!$user_id){ return false; }

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        // учитываем и подписки и запросы дружбы
        $this->filterStart();
            $this->filterEqual('is_mutual', 0);
                $this->filterOr();
            $this->filterIsNull('is_mutual');
        $this->filterEnd();

        return $this->getFieldFiltered('{users}_friends', $field);
    }

    public function isFriendshipRequested($user_id, $friend_id) {
        return $this->getFriendshipRequested($user_id, $friend_id, 'id');
    }

    public function isFriendshipExists($user_id, $friend_id) {

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

        return (bool) $this->getFieldFiltered('{users}_friends', 'id');
    }

    public function isFriendshipMutual($user_id, $friend_id) {

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

        return (bool) $this->getFieldFiltered('{users}_friends', 'id');
    }

    public function subscribeUser($user_id, $friend_id) {

        cmsCache::getInstance()->clean('users.friends');

        $this->insert('{users}_friends', array(
            'user_id'   => $user_id,
            'friend_id' => $friend_id
        ));

        return $this->cacheSubscribersCount($friend_id);
    }

    public function unsubscribeUser($user_id, $friend_id) {

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->deleteFiltered('{users}_friends');

        cmsCache::getInstance()->clean('users.friends');

        return $this->cacheSubscribersCount($friend_id);
    }

    public function addFriendship($user_id, $friend_id) {

        $is_mutual = false;

        if ($this->isFriendshipRequested($friend_id, $user_id)) {

            $this->filterEqual('user_id', $friend_id);
            $this->filterEqual('friend_id', $user_id);

            $this->updateFiltered('{users}_friends', [
                'is_mutual' => true
            ]);

            $this->cacheSubscribersCount($user_id);

            $is_mutual = true;
        }

        if ($is_mutual) {

            $this->filterEqual('id', $user_id)->increment('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->increment('{users}', 'friends_count');

            $friend = $this->getUser($friend_id);

            list($user_id, $friend) = cmsEventsManager::hook('users_add_friendship_mutual', [$user_id, $friend]);
        }

        if ($this->isFriendshipRequested($user_id, $friend_id)) {

            $this->filterEqual('user_id', $user_id);
            $this->filterEqual('friend_id', $friend_id);

            $this->updateFiltered('{users}_friends', [
                'is_mutual' => true
            ]);

            $this->cacheSubscribersCount($friend_id);
        } else {

            $this->insert('{users}_friends', [
                'user_id'   => $user_id,
                'friend_id' => $friend_id,
                'is_mutual' => $is_mutual
            ]);
        }

        list($user_id, $friend_id, $is_mutual) = cmsEventsManager::hook('users_add_friendship', [$user_id, $friend_id, $is_mutual]);

        cmsCache::getInstance()->clean('users.friends');

        return $is_mutual;
    }

    public function deleteFriendship($user_id, $friend_id) {

        $is_mutual = $this->isFriendshipMutual($user_id, $friend_id);

        list($user_id, $friend_id, $is_mutual) = cmsEventsManager::hook('users_before_delete_friendship', [$user_id, $friend_id, $is_mutual]);

        if ($is_mutual) {
            $this->filterEqual('id', $user_id)->decrement('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->decrement('{users}', 'friends_count');
        }

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $success = $this->deleteFiltered('{users}_friends');

        if ($success) {
            $this->filterEqual('user_id', $friend_id);
            $this->filterEqual('friend_id', $user_id);
            $success = $this->deleteFiltered('{users}_friends');
        }

        if ($success) {
            list($user_id, $friend_id, $is_mutual) = cmsEventsManager::hook('users_after_delete_friendship', [$user_id, $friend_id, $is_mutual]);
        }

        cmsCache::getInstance()->clean('users.friends');

        return $success;
    }

    public function keepInSubscribers($user_id, $friend_id) {

        if ($this->isFriendshipMutual($user_id, $friend_id)) {
            $this->filterEqual('id', $user_id)->decrement('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->decrement('{users}', 'friends_count');
        }

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->deleteFiltered('{users}_friends');

        $this->filterEqual('user_id', $friend_id);
        $this->filterEqual('friend_id', $user_id);
        $this->updateFiltered('{users}_friends', [
            'is_mutual' => null
        ]);

        cmsCache::getInstance()->clean('users.friends');

        return $this->cacheSubscribersCount($user_id);
    }

//============================================================================//
//=========================    ВКЛАДКИ ПРОФИЛЕЙ   ============================//
//============================================================================//

    public function getUsersProfilesTabs($only_active = false, $by_field = 'id') {

        $this->useCache('users.tabs');

        if ($only_active) {
            $this->filterEqual('is_active', 1);
        }

        return $this->orderBy('ordering')->get('{users}_tabs', function ($item, $model) {

            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);

            return $item;
        }, $by_field);
    }

//============================================================================//
//==============================    СТАТУСЫ   ================================//
//============================================================================//

    public function getUserStatus($id) {

        if (!$id) { return false; }

        $this->useCache('users.status');

        return $this->getItemById('{users}_statuses', $id);
    }

    public function addUserStatus($status) {

        $id = $this->insert('{users}_statuses', $status);

        $this->update('{users}', $status['user_id'], [
            'status_text' => $status['content'],
            'status_id'   => $id
        ], true);

        cmsCache::getInstance()->clean('users.status');
        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $status['user_id']);

        return $id;
    }

    public function clearUserStatus($user_id) {

        cmsCache::getInstance()->clean('users.status');
        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $user_id);

        $this->filterEqual('user_id', $user_id)->deleteFiltered('{users}_statuses');

        return $this->update('{users}', $user_id, [
            'status_text' => null,
            'status_id'   => null
        ], true);
    }

    public function increaseUserStatusRepliesCount($status_id, $is_increment = true) {

        cmsCache::getInstance()->clean('users.status');

        $this->filterEqual('id', $status_id);

        if ($is_increment) {
            $result = $this->increment('{users}_statuses', 'replies_count');
        } else {
            $result = $this->decrement('{users}_statuses', 'replies_count');
        }

        return $result;
    }

//============================================================================//
//============================    РЕПУТАЦИЯ   ================================//
//============================================================================//

    public function isUserCanVoteKarma($user_id, $profile_id, $voting_days = 1) {

        $this->selectOnly('id');
        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('profile_id', $profile_id);
        $this->filterDateYounger('date_pub', $voting_days);

        $this->useCache('users.karma');

        return $this->getItem('{users}_karma') ? false : true;
    }

    public function addKarmaVote($vote) {

        cmsCache::getInstance()->clean('users.karma');

        $result = $this->insert('{users}_karma', $vote);

        if (!$result) { return false; }

        $this->filterEqual('id', $vote['profile_id'])->
                increment('{users}', 'karma', $vote['points']);

        return $result;
    }

    public function getKarmaLogCount($profile_id) {

        $this->useCache('users.karma');

        return $this->filterEqual('profile_id', $profile_id)->
                getCount('{users}_karma', 'id', true);
    }

    public function getKarmaLog($profile_id){

        $this->useCache('users.karma');

        $this->joinUser();

        $this->orderBy('id', 'desc');

        $this->filterEqual('profile_id', $profile_id);

        $this->joinSessionsOnline();

        return $this->get('{users}_karma', function($item, $model){

            $item['user'] = [
                'id'       => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'slug'     => $item['user_slug'],
                'avatar'   => $item['user_avatar']
            ];

            return $item;
        });
    }

//============================================================================//
//============================================================================//

    public function updateUserRating($user_id, $score) {

        $this->filterEqual('id', $user_id);

        $score = intval($score);

        if ($score > 0) {
            $this->increment('{users}', 'rating', abs($score));
        }
        if ($score < 0) {
            $this->decrement('{users}', 'rating', abs($score));
        }

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $user_id);

        cmsEventsManager::hook('users_rating_update', [$user_id, $score]);

        return true;
    }

//============================================================================//
//============================================================================//

    public function setUPS($key, $data, $user_id) {
        if (is_array($data)) {
            $data = self::arrayToYaml($data);
        }
        $insert = [
            'user_id'  => $user_id,
            'skey'     => $key,
            'settings' => $data
        ];
        $update = [
            'settings' => $data
        ];

        $ret = $this->insertOrUpdate('{users}_personal_settings', $insert, $update);
        cmsCache::getInstance()->clean('users.ups');

        return $ret;
    }

    public function getSetUPS($key) {

        $this->useCache('users.ups');

        $this->selectList([
            'i.settings' => 'settings',
            'i.user_id'  => 'user_id'
        ], true);

        $this->filterEqual('skey', $key);

        return $this->get('{users}_personal_settings', function ($item, $model) {
            if (strpos($item['settings'], '---') === 0) {
                $item['settings'] = cmsModel::yamlToArray($item['settings']);
            }
            return $item['settings'];
        }, 'user_id');
    }

    public function getUPS($key, $user_id) {

        $this->useCache('users.ups');

        $this->filterEqual('user_id', $user_id)->filterEqual('skey', $key);

        return $this->getItem('{users}_personal_settings', function ($item, $model) {
            if (strpos($item['settings'], '---') === 0) {
                $item['settings'] = cmsModel::yamlToArray($item['settings']);
            }
            return $item['settings'];
        });
    }

    public function deleteUPS($key, $user_id = null) {
        if ($user_id && $key) {
            $this->filterEqual('user_id', $user_id)->filterEqual('skey', $key);
        } elseif ($user_id) {
            $this->filterEqual('user_id', $user_id);
        } elseif ($key) {
            $this->filterEqual('skey', $key);
        } else {
            return false;
        }
        $ret = $this->deleteFiltered('{users}_personal_settings');
        cmsCache::getInstance()->clean('users.ups');

        return $ret;
    }

}
