<?php

define('GUEST_GROUP_ID', 1);
define('DEF_GROUP_ID', 3);

class cmsUser {

    private static $instance;
    private static $_ip;

    public $id = 0;
    public $email;
    public $password;
    public $nickname;
    public $is_admin = 0;
    public $is_logged = false;

    private static $online_users = array();
    private static $online_interval = 180;

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

        if(self::$_ip === null){

            $config = cmsConfig::getInstance();

            self::$_ip = isset($_SERVER[$config->detect_ip_key]) ? $_SERVER[$config->detect_ip_key] : '127.0.0.1';

            if (!filter_var(self::$_ip, FILTER_VALIDATE_IP)) {
                self::$_ip = '127.0.0.1';
            }

        }

        return self::$_ip;

    }

    public function __construct(){

        $config = cmsConfig::getInstance();

        $this->groups = array(GUEST_GROUP_ID);
        $this->ip     = self::getIp();

        self::loadOnlineUsersIds();

        if (self::isSessionSet('user:id')){

            // уже авторизован
            $this->id  = self::sessionGet('user:id');

        } elseif (self::getCookie('auth')) {

            // пробуем авторизовать по кукису
            $this->id  = self::autoLogin(self::getCookie('auth'));

        }

        self::deleteOldSessions($this->id);

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
                $model->update('{users}', $this->id, array('date_log' => null), true);
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

        if (!preg_match('/^[0-9a-f]{32}$/i', $auth_token)){ return 0; }

        $model = cmsCore::getModel('users');

        $model->filterEqual('auth_token', $auth_token);

        $user = $model->getUser();

        if (!$user){ return 0; }

        $model->update('{users}', $user['id'], array(
            'pass_token' => null,
            'ip' => self::getIp()
        ), true);

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
     * @param bool $remember
     * @return int
     */
    public static function login($email, $password, $remember=false) {

        if (!preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $email)){
            return 0;
        }

        $model = cmsCore::getModel('users');

        $model->filterEqual('email', $email);
        $model->filterFunc('password', "MD5(CONCAT(MD5('{$password}'), i.password_salt))");

        $user = $model->getUser();

        if(!$user) {
            $user = cmsEventsManager::hook('user_auth_error', array('email'=>$email,'password'=>$password));
        }

        if (empty($user['id'])) { return 0; }

        $user = cmsEventsManager::hook('user_login', $user);

        self::sessionSet('user', array(
            'id' => $user['id'],
            'groups' => $user['groups'],
            'time_zone' => $user['time_zone'],
            'perms' => self::getPermissions($user['groups'], $user['id']),
            'is_admin' => $user['is_admin'],
        ));

        $update_data = array(
            'ip' => self::getIp()
        );

        if ($remember){

            $auth_token = string_random(32, $email);
            self::setCookie('auth', $auth_token, 8640000); //100 дней

            $update_data['auth_token'] = $auth_token;

        }

        $model->update('{users}', $user['id'], $update_data, true);

        return $user['id'];

    }

    /**
     * Выход пользователя
     */
    public static function logout() {

        $model = cmsCore::getModel('users');

        $userSession = self::sessionGet('user');

        $model->update('{users}', $userSession['id'], array(
            'date_log' => null,
        ), true);

        cmsEventsManager::hook('user_logout', $userSession);

        self::sessionUnset('user');
        self::unsetCookie('auth');

        self::deleteSession($userSession['id']);
        self::deleteOldSessions();

        return true;

    }

//============================================================================//
//============================================================================//

    private static function loadOnlineUsersIds() {

        $model = new cmsModel();

        $users = $model->get('sessions_online', false, 'user_id');

        if($users){
            self::$online_users = $users;
        }

    }

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

    }

    public static function deleteSession($user_id){

        $model = new cmsModel();

        $model->filterEqual('user_id', $user_id)->
                deleteFiltered('sessions_online');

    }

    public static function deleteOldSessions($current_user_id=0){

        $expired_users = array();

        if(self::$online_users){
            foreach (self::$online_users as $k=>$user) {
                if((time()-self::$online_interval) >= strtotime($user['date_created'])){
                    $expired_users[] = $user['user_id'];
                    if($current_user_id != $user['user_id']){
                        unset(self::$online_users[$k]);
                    }
                }
            }
        }

        if ($expired_users){

            $model = new cmsModel();

            $model->filterIn('user_id', $expired_users)->
                    deleteFiltered('sessions_online');

        }

    }

//============================================================================//
//============================================================================//

    public static function userIsOnline($user_id) {
        $online = false;
        if(self::$online_users){
            foreach (self::$online_users as $user) {
                if($user['user_id'] == $user_id){
                    $online = true; break;
                }
            }
        }
        return $online;
    }

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

        if (strpos($key, ':') === false){
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

        if (strpos($key, ':') === false){
            $value = $_SESSION[$key];
        } else {
            list($key, $subkey) = explode(':', $key);
            $value = $_SESSION[$key][$subkey];
        }

        if ($is_clean) { self::sessionUnset($key); }

        return $value;

    }

    public static function isSessionSet($key){
        if (strpos($key, ':') === false){
            return isset($_SESSION[$key]);
        } else {
            list($key, $subkey) = explode(':', $key);
            return isset($_SESSION[$key][$subkey]);
        }
    }

    public static function sessionUnset($key){
        if (strpos($key, ':') === false){
            unset($_SESSION[$key]);
        } else {
            list($key, $subkey) = explode(':', $key);
            unset($_SESSION[$key][$subkey]);
        }
    }

    /**
     * Устанавливает куки
     * @param string $key Имя кукиса
     * @param string $value Значение
     * @param int $time Время жизни, в секундах
     * @param string $path Путь на сервере
     * @param bool $http_only Куки недоступны для скриптов
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
     * @param str $key Имя кукиса
     * @return str или false
     */
    public static function getCookie($key){
        if (isset($_COOKIE['icms'][$key])){
            return trim($_COOKIE['icms'][$key]);
        } else {
            return false;
        }
    }

    public static function hasCookie($key){
        return isset($_COOKIE['icms'][$key]);
    }

//============================================================================//
//============================================================================//

    /**
     * Устанавливает для пользователя его уникальные персональные настройки
     * User Personal Setting
     *
     * @param str       $key        Ключ настроек
     * @param str|array $data       Данные
     * @param int       $user_id    Ид юзера
     * @return bool
     */

    public static function setUPS($key, $data, $user_id = null){

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        return (bool) cmsCore::getModel('users')->setUPS($key, $data, $user_id);

    }

    public static function getUPS($key, $user_id = null){

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        return cmsCore::getModel('users')->getUPS($key, $user_id);

    }

    public static function getUPSActual($key, $data, $user_id = null){

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        $umodel = cmsCore::getModel('users');

        $old = $umodel->getUPS($key, $user_id);
        if (!$data) {
            return $old;
        }
        if ($old !== $data) {
            $umodel->setUPS($key, $data, $user_id);
        }

        return $data;

    }

    public static function deleteUPS($key, $user_id = null){

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        return cmsCore::getModel('users')->deleteUPS($key, $user_id);

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

    public static function isAllowed($subject, $permission, $value=true, $is_admin_strict=false){

        $user = self::getInstance();

        if(!$is_admin_strict){
            if ($user->is_admin){ return true; }
        }

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