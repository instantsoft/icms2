<?php

define('GUEST_GROUP_ID', 1);
define('DEF_GROUP_ID', 3);

class cmsUser {

    private static $instance;

    public $id;
    public $email;
    public $password;
    public $nickname;
    public $is_admin;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get($key){
        return self::getInstance()->$key;
    }

    public static function getIp(){
        return $_SERVER['REMOTE_ADDR'];
    }

    public function __construct(){

        $config = cmsConfig::getInstance();

        $this->is_logged = false;
        $this->groups = array(GUEST_GROUP_ID);
        $this->ip = self::getIp();

        self::deleteOldSessions();

        if (self::isSessionSet('user:id')){

            // уже авторизован
            $this->id  = self::sessionGet('user:id');

        } elseif (self::getCookie('auth')) {

            // пробуем авторизовать по кукису
            $this->id  = self::autoLogin(self::getCookie('auth'));

        } else {

            // не авторизован
            $this->groups = array(GUEST_GROUP_ID);
            $this->id = 0;

        }

        //
        // если авторизован, заполняем объект данными из базы
        //
        if ($this->id){

            $model = cmsCore::getModel('users');

            $user = $model->getUser($this->id);
			
			if (!$user){
				self::logout();
				return;
			}

            // если дата последнего визита еще не сохранена в сессии,
            // значит авторизация была только что
            // сохраним дату в сессии и обновим в базе
            if (!self::isSessionSet('user:date_log')){
                self::sessionSet('user:date_log', $user['date_log']);
                $model->update('{users}', $this->id, array( 'date_log' => null ));
            }

            // заполняем объект данными из базы
            foreach($user as $field=>$value){
                $this->{$field} = $value;
            }

            // конвертим список аватаров в массив
            // к пути каждого аватара добавляем путь корня
            $this->avatar = cmsModel::yamlToArray($this->avatar);
            if ($this->avatar){
                foreach($this->avatar as $size=>$path){
                    $this->avatar[$size] = $config->upload_host . '/' . $path;
                }
            }

            // кешируем список друзей в сессию
            $this->recacheFriends();

            // создаем online-сессию
            self::createSession($this->id);

            // восстанавливаем те поля, которые не должны
            // изменяться в течении сессии
            $this->date_log = self::sessionGet('user:date_log');
            $this->perms = self::getPermissions($user['groups'], $user['id']);
            $this->is_logged = true;

            cmsEventsManager::hook('user_loaded', $user);

        }

    }

//============================================================================//
//============================================================================//

    /**
     * Авторизует пользователя по кукису
     * @param str $auth_token
     */
    public static function autoLogin($auth_token){

        if (!preg_match("/^([a-zA-Z0-9]{32})$/i", $auth_token)){ return false; }

        $model = cmsCore::getModel('users');

        $model->filterEqual('auth_token', $auth_token);

        $user = $model->getUser();

        if (!$user){ return false; }

        $model->update('{users}', $user['id'], array(
            'pass_token' => null,
            'ip' => self::getIp()
        ));

        $user = cmsEventsManager::hook('user_login', $user);

        self::sessionSet('user', array(
            'id' => $user['id'],
            'groups' => $user['groups'],
            'time_zone' => $user['time_zone'],
            'perms' => self::getPermissions($user['groups'], $user['id']),
            'is_admin' => $user['is_admin'],
        ));

        return $user['id'];

    }

    /**
     * Авторизует пользователя
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function login($email, $password, $remember=false) {

        if (!preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $email)){
            return false;
        }

        $model = cmsCore::getModel('users');

        $model->filterEqual('email', $email);
        $model->filterFunc('password', "MD5(CONCAT(MD5('{$password}'), i.password_salt))");

        $user = $model->getUser();

        if(!$user) {
            $user = cmsEventsManager::hook('user_auth_error', array('email'=>$email,'password'=>$password));
        }

        if (empty($user['id'])) { return false; } 

        $user = cmsEventsManager::hook('user_login', $user);

        self::sessionSet('user', array(
            'id' => $user['id'],
            'groups' => $user['groups'],
            'time_zone' => $user['time_zone'],
            'perms' => self::getPermissions($user['groups'], $user['id']),
            'is_admin' => $user['is_admin'],
        ));

        if ($remember){

            $auth_token = string_random(32, $email);
            self::setCookie('auth', $auth_token, 8640000); //100 дней

            $model->update('{users}', $user['id'], array('auth_token'=>$auth_token));

        }

        $model->update('{users}', $user['id'], array(
            'ip' => self::getIp()
        ));

        return $user['id'];

    }

    /**
     * Выход пользователя
     *
     */
    public static function logout() {

        $model = cmsCore::getModel('users');

        $userSession = self::sessionGet('user');

        $model->update('{users}', $userSession['id'], array(
            'date_log' => null,
        ));

        cmsEventsManager::hook('user_logout', $userSession);

        self::sessionUnset('user');
        self::unsetCookie('auth');

        self::deleteSession($userSession['id']);
        self::deleteOldSessions();

        return true;

    }

//============================================================================//
//============================================================================//

    public static function createSession($user_id){

        $model = new cmsModel();

        $insert_data = array(
            'session_id' => session_id(),
            'user_id' => $user_id
        );

        $update_data = array(
            'date_created' => null
        );

        $model->insertOrUpdate('sessions_online', $insert_data, $update_data);

        if ($user_id){

            $model->filterEqual('id', $user_id)->
                    updateFiltered('{users}', array('is_online' => 1));

        }

    }

    public static function deleteSession($user_id){

        $model = new cmsModel();

        $model->filterEqual('user_id', $user_id)->
                deleteFiltered('sessions_online');

        $model->filterEqual('id', $user_id)->
                updateFiltered('{users}', array('is_online' => 0));

    }

    public static function deleteOldSessions(){

        $model = new cmsModel();

        $model->filterDateOlder('date_created', 3, 'MINUTE');

        $users = $model->get('sessions_online', function($item, $model){

            return $item['user_id'] ? $item['user_id'] : false;

        }, false);

        if ($users){

            $model->filterIn('id', $users)->
                    updateFiltered('{users}', array('is_online' => 0));

            $model->filterDateOlder('date_created', 3, 'MINUTE')->
                    deleteFiltered('sessions_online');

        }

    }

//============================================================================//
//============================================================================//

    public static function isLogged(){
        return self::getInstance()->is_logged;
    }

    public static function isAdmin() {
        return self::getInstance()->is_admin;
    }

    public static function goLogin($back_url=''){
        if (!$back_url){ $back_url = $_SERVER['REQUEST_URI']; }
        header('Location:' . href_to('auth', 'login') . "?back=" . $back_url);
        exit;
    }

//============================================================================//
//============================================================================//

    public static function sessionSet($key, $value){

        if (!strstr($key, ':')){
            $_SESSION[$key] = $value;
        } else {
            list($key, $subkey) = explode(':', $key);
            $_SESSION[$key][$subkey] = $value;
        }

    }

    public static function sessionPush($key, $value){
        $_SESSION[$key][] = $value;
    }

    public static function sessionGet($key, $is_clean=false){

        if (!self::isSessionSet($key)){ return false; }

        if (!strstr($key, ':')){
            $value = $_SESSION[$key];
        } else {
            list($key, $subkey) = explode(':', $key);
            $value = $_SESSION[$key][$subkey];
        }

        if ($is_clean) { self::sessionUnset($key); }

        return $value;

    }

    public static function isSessionSet($key){
        if (!strstr($key, ':')){
            return isset($_SESSION[$key]);
        } else {
            list($key, $subkey) = explode(':', $key);
            return isset($_SESSION[$key][$subkey]);
        }
    }

    public static function sessionUnset($key){
        if (!strstr($key, ':')){
            unset($_SESSION[$key]);
        } else {
            list($key, $subkey) = explode(':', $key);
            unset($_SESSION[$key][$subkey]);
        }
    }

    /**
     * Устанавливает куки
     *
     * @param str $name Имя кукиса
     * @param str $value Значение
     * @param int $time Время жизни, в секундах
     * @param str $path Путь на сервере
     * @param str $domain Разрешенный домен
     *
     * */
    public static function setCookie($key, $value, $time=3600, $path='/', $http_only=true){
        setcookie('icms['.$key.']', $value, time()+$time, $path, null, false, $http_only);
        return;
    }

    public static function setCookiePublic($key, $value, $time=3600, $path='/'){
        return self::setCookie($key, $value, $time, $path, false);
    }

    public static function unsetCookie($key){
        setcookie('icms['.$key.']', '', time()-3600, '/');
        return;
    }

    /**
     * Проверяет наличие кукиса и возвращает его значение
     *
     * @param str $name Имя кукиса
     * @return str или false
     */
    public static function getCookie($key){
        if (isset($_COOKIE['icms'][$key])){
            return $_COOKIE['icms'][$key];
        } else {
            return false;
        }
    }

    public static function hasCookie($key){
        return isset($_COOKIE['icms'][$key]);
    }


//============================================================================//
//============================================================================//

    public static function addSessionMessage($message, $class='info'){
        self::sessionPush('core_message', '<div class="message_'.$class.'">'.$message.'</div>');
    }

    public static function getSessionMessages(){

        if (self::isSessionSet('core_message')){
            $messages = self::sessionGet('core_message');
        } else {
            $messages = false;
        }

        self::clearSessionMessages();
        return $messages;

    }

    public static function clearSessionMessages(){
        self::sessionUnset('core_message');
    }


//============================================================================//
//============================================================================//

    public static function getPermissions($groups, $user_id){

        $perms = cmsPermissions::getUserPermissions($groups);

        return $perms;

    }

    public static function getPermissionValue($subject, $permission){

        $user = self::getInstance();

        if (!isset($user->perms[$subject])) { return false; }
        if (!isset($user->perms[$subject][$permission])) { return false; }

        return $user->perms[$subject][$permission];

    }

//============================================================================//
//============================================================================//

    public static function isAllowed($subject, $permission, $value=true){

        $user = self::getInstance();

        if ($user->is_admin){ return true; }

        if (!isset($user->perms[$subject])) { return false; }
        if (!isset($user->perms[$subject][$permission])) { return false; }
        if ($user->perms[$subject][$permission] != $value) { return false; }

        return true;

    }

    public static function isPermittedLimitReached($subject, $permission, $current_value=0){		
		
        $user = self::getInstance();
		
        if ($user->is_admin){ return false; }

        if (!isset($user->perms[$subject])) { return false; }
        if (!isset($user->perms[$subject][$permission])) { return false; }
        if ((int)$current_value >= $user->perms[$subject][$permission]) { return true; }
		
        return false;

    }

    public static function isPermittedLimitHigher($subject, $permission, $current_value=0){

        $user = self::getInstance();

        if ($user->is_admin){ return false; }

        if (!isset($user->perms[$subject])) { return false; }
        if (!isset($user->perms[$subject][$permission])) { return false; }
        if ((int)$current_value < $user->perms[$subject][$permission]) { return true; }

        return false;

    }

//============================================================================//
//============================================================================//

    /**
     * Увеличивает счетчик загруженных пользователем файлов
     * @return bool
     */
    public function increaseFilesCount(){

        $this->files_count++;

        $model = new cmsModel();

        $model->filterEqual('id', $this->id);

        return $model->increment('{users}', 'files_count');

    }

    /**
     * Уменьшает счетчик загруженных пользователем файлов
     * @return bool
     */
    public function decreaseFilesCount(){

        $this->files_count--;

        $model = new cmsModel();

        $model->filterEqual('id', $this->id);

        return $model->decrement('{users}', 'files_count');

    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет членство пользователя в группе
     * @param int $group_id ID группы
     * @return boolean
     */
    public function isInGroup($group_id){
        return in_array($group_id, $this->groups);
    }

    /**
     * Проверяет членство пользователя в любой группе из списка
     * @param array $groups Список ID групп
     * @return boolean
     */
    public function isInGroups($groups){

        if ($groups == array(0)){ return true; }
        if (in_array(0, $groups)){ return true; }

        $found = false;

        foreach($groups as $group_id){
            $found = $found || in_array($group_id, $this->groups);
        }

        return $found;

    }

    public static function isUserInGroups($user_groups, $groups){

        if (in_array(0, $groups)) { return true; }

        $found = false;

        foreach($groups as $group_id){
            $found = $found || in_array($group_id, $user_groups);
        }

        return $found;

    }

//============================================================================//
//============================================================================//

    public function isPrivacyAllowed($profile, $option){

        if ($this->is_admin) { return true; }

        if ($profile['id'] == $this->id) { return true; }

        if (!$profile || !$option) { return false; }

        $options = $profile['privacy_options'];

        if (!isset($options[$option])){ return true; }

        if ($options[$option] == 'anyone'){ return true; }

        if ($options[$option] == 'friends' && $this->isFriend($profile['id'])){ return true; }

        return false;

    }

//============================================================================//
//============================================================================//

    public function recacheFriends(){

        $model = cmsCore::getModel('users');

        $this->friends = $model->getFriendsIds($this->id);

    }

    public function isFriend($friend_id){

        if ($friend_id == $this->id) { return true; }

        if (!isset($this->friends)) { return false; }
        if (!is_array($this->friends)) { return false; }

        return in_array($friend_id, $this->friends);

    }

    public function hasFriends(){

        return !empty($this->friends);

    }

//============================================================================//
//============================================================================//


}
