<?php

define('GUEST_GROUP_ID', 1);
define('DEF_GROUP_ID', 3);
define('AUTH_TOKEN_EXPIRATION_INT', 8640000); // 100 дней

class cmsUser {

    private static $instance;
    private static $_ip;
    private static $auth_token;

    public $id = 0;
    public $email;
    public $password;
    public $nickname;
    public $is_admin = 0;
    public $is_logged = false;

    public static $online_users = array();
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

    /**
     * Для var_export
     * @param array $data
     * @return \cmsUser
     */
    public static function __set_state($data) {
        return self::getInstance();
    }

    public function __construct(){

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

        if(!$this->id){
            $this->id = cmsEventsManager::hook('user_auto_login', 0);
        }

        self::deleteOldSessions($this->id);

        //
        // если авторизован, заполняем объект данными из базы
        //
        if ($this->id){
            $this->loadAuthUser($this->id);
        }

    }

    private static function restrictSessionToIp($ip = false) {

        if(!$ip){ $ip = self::getIp(); }

        if (!self::isSessionSet('user_ip')){

            self::sessionSet('user_ip', $ip);

            $octets = explode('.', $ip);
            $end_okets = end($octets);

            self::sessionSet('user_net', rtrim($ip, $end_okets));

        }

    }

    /**
     * Проверяет изменился ли ip адрес сессии
     * @param boolean $strict Если true, то проверяется целый ip адрес, иначе проверка по подсети
     * @return boolean
     */
    public function checkSpoofingSession($strict = false) {

        if(!$strict){
            return mb_strstr($this->ip, self::sessionGet('user_net'));
        }

		return $this->ip === self::sessionGet('user_ip');

    }

    /**
     * Загружает данные для авторизованного пользователя
     * @param integer $user_id id пользователя, прошедшего авторизацию
     * @return array
     */
    public function loadAuthUser($user_id) {

        $config = cmsConfig::getInstance();
        $model  = cmsCore::getModel('users');

        $user = $model->getUser($user_id);

        if (!$user){
            self::logout();
            return array();
        }

        // если дата последнего визита еще не сохранена в сессии,
        // значит авторизация была только что
        // сохраним дату в сессии и обновим в базе
        if (!self::isSessionSet('user:date_log')){
            self::sessionSet('user:date_log', $user['date_log']);
            $model->update('{users}', $user_id, array('date_log' => null), true);
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
        self::createSession($user_id);

        // восстанавливаем те поля, которые не должны
        // изменяться в течении сессии
        $this->date_log  = self::sessionGet('user:date_log');
        $this->perms     = self::getPermissions($user['groups']);
        $this->is_logged = true;

        return cmsEventsManager::hook('user_loaded', $user);

    }

//============================================================================//
//============================================================================//

    public static function setUserSession($user, $last_ip = false){

        self::sessionSet('user', array(
            'id'        => $user['id'],
            'groups'    => $user['groups'],
            'time_zone' => $user['time_zone'],
            'perms'     => self::getPermissions($user['groups']),
            'is_admin'  => $user['is_admin']
        ));

        self::restrictSessionToIp($last_ip);

    }

    /**
     * Авторизует пользователя по кукису
     * @param str $auth_token
     */
    public static function autoLogin($auth_token){

        if (!preg_match('/^[0-9a-f]{32}$/i', $auth_token)){ return 0; }

        $model = cmsCore::getModel('users');

        $user = $model->joinInner('{users}_auth_tokens', 'au', 'au.user_id = i.id')->
                filterEqual('au.auth_token', $auth_token)->filterIsNull('is_deleted')->select('au.date_auth')->getUser();
        if (!$user || $user['is_locked']){ return 0; }
        // проверяем не истек ли срок действия токена
        if((time() - strtotime($user['date_auth'])) > AUTH_TOKEN_EXPIRATION_INT){
            $model->deleteAuthToken($auth_token);
            return 0;
        }
        // обновляем дату токена
        $model->filterEqual('auth_token', $auth_token)->updateFiltered('{users}_auth_tokens', array(
            'date_log' => null
        ));

        $model->update('{users}', $user['id'], array(
            'pass_token' => null,
            'ip' => self::getIp()
        ), true);

        $user = cmsEventsManager::hook('user_login', $user);

        self::setUserSession($user, $user['ip']);

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

        if (!preg_match("/^([a-z0-9\._-]+)@([a-z0-9\._-]+)\.([a-z]{2,6})$/i", $email)){
            return 0;
        }

        $model = cmsCore::getModel('users');

        $model->filterIsNull('is_deleted');
        $model->filterEqual('email', $email);
        $model->filterFunc('password', "MD5(CONCAT(MD5('{$password}'), i.password_salt))");

        $user = $model->getUser();

        if(!$user) {
            $user = cmsEventsManager::hook('user_auth_error', array('email'=>$email,'password'=>$password));
        }

        if (empty($user['id'])) { return 0; }

        $user = cmsEventsManager::hook('user_login', $user);

        self::setUserSession($user);

        if ($remember){

            $auth_token = string_random(32, $email);
            self::setCookie('auth', $auth_token, AUTH_TOKEN_EXPIRATION_INT); //100 дней
            $model->setAuthToken($user['id'], $auth_token);
            $model->deleteExpiredToken($user['id'], AUTH_TOKEN_EXPIRATION_INT);

            self::$auth_token = $auth_token;

        }

        $model->update('{users}', $user['id'], array(
            'ip' => self::getIp()
        ), true);

        return $user['id'];

    }

    /**
     * Выход пользователя
     */
    public static function logout() {

        $model = cmsCore::getModel('users');

        $userSession = self::sessionGet('user');

        $model->update('{users}', $userSession['id'], array(
            'date_log' => null
        ), true);

        cmsEventsManager::hook('user_logout', $userSession);

        if (self::getCookie('auth')) {

            if (preg_match('/^[0-9a-f]{32}$/i', self::getCookie('auth'))){
                $model->deleteAuthToken(self::getCookie('auth'));
            }

            self::unsetCookie('auth');

        }

        // если login и logout выполняются в пределах
        // одной загрузки страницы
        if(self::$auth_token){
            $model->deleteAuthToken(self::$auth_token);
            self::$auth_token = null;
        }

        self::sessionUnset('user');

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
     * @param string $domain Домен действия. null - только текущий
     * */
    public static function setCookie($key, $value, $time=3600, $path='/', $http_only=true, $domain = null){

        $cookie_domain = cmsConfig::get('cookie_domain');

        if(!$domain && $cookie_domain){
            $domain = $cookie_domain;
        }

        return setcookie('icms['.$key.']', $value, time()+$time, $path, $domain, false, $http_only);

    }

    public static function setCookiePublic($key, $value, $time=3600, $path='/'){
        return self::setCookie($key, $value, $time, $path, false);
    }

    public static function unsetCookie($key, $path='/', $domain = null){
        return self::setCookie($key, '', -3600, $path, true, $domain);
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

    public static function getPermissions($groups){

        return cmsPermissions::getUserPermissions($groups);

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
     * @param integer $user_id
     * @return boolean
     */
    public function increaseFilesCount($user_id = 0){

        if(!$user_id){
            $this->files_count++;
            $user_id = $this->id;
        }

        $model = new cmsModel();

        $model->filterEqual('id', $user_id);

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

        if (empty($groups) || $groups == array(0)){ return true; }
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