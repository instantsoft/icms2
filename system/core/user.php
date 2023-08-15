<?php

define('GUEST_GROUP_ID', 1);
define('DEF_GROUP_ID', 3);
#[\AllowDynamicProperties]
class cmsUser {

    const USER_ONLINE_INTERVAL      = 180;
    const AUTH_TOKEN_EXPIRATION_INT = 8640000; // 100 дней

    private static $instance;
    private static $_ip;
    public static $auth_token;
    private static $cached_online = [];

    public $id = 0;
    public $email;
    public $password;
    public $nickname;
    public $date_log;
    public $is_admin   = 0;
    public $is_logged  = false;
    public $friends    = [];
    public $subscribes = [];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get($key) {
        return isset(self::getInstance()->$key) ? self::getInstance()->$key : null;
    }

    public static function getIp() {

        if (self::$_ip === null) {

            $config = cmsConfig::getInstance();

            self::$_ip = isset($_SERVER[$config->detect_ip_key]) ? $_SERVER[$config->detect_ip_key] : '127.0.0.1';

            if (!filter_var(self::$_ip, FILTER_VALIDATE_IP)) {
                self::$_ip = '127.0.0.1';
            }
        }

        return self::$_ip;
    }

    public static function setIp($ip) {
        self::$_ip = $ip;

    }

    public function __construct() {

        $this->groups   = [GUEST_GROUP_ID];
        $this->ip       = self::getIp();
        $this->date_log = date('Y-m-d H:i:s');

        if(PHP_SAPI === 'cli') {
            return;
        }

        if (self::isSessionSet('user:id')) {

            // уже авторизован
            $this->id = self::sessionGet('user:id');

        } elseif (self::hasCookie('auth')) {

            // пробуем авторизовать по кукису
            $this->id = self::autoLogin(self::getCookie('auth'));
        }

        if (!$this->id) {
            $this->id = cmsEventsManager::hook('user_auto_login', 0);
        }

        //
        // если авторизован, заполняем объект данными из базы
        //
        if ($this->id) {
            $this->loadAuthUser($this->id);
        } else {

            // для неавторизованных ставим дату посещения
            $_date_log = self::getCookie('guest_date_log', 'integer');
            if (!$_date_log) {
                $_date_log = time();
            }

            if (!self::isSessionSet('user:date_log') || ((time() - self::USER_ONLINE_INTERVAL) >= $_date_log)) {

                self::setCookie('guest_date_log', time(), 31536000);

                if (!self::isSessionSet('user:date_log')) {
                    self::sessionSet('user:date_log', $_date_log);
                }
            }

            $this->date_log = date('Y-m-d H:i:s', self::sessionGet('user:date_log'));
        }
    }

    public static function restrictSessionToIp($ip = false) {

        if (!$ip) { $ip = self::getIp(); }

        if (!self::isSessionSet('user_ip')) {

            self::sessionSet('user_ip', $ip);

            $octets    = explode('.', $ip);
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

        if (!$strict) {
            return strpos($this->ip, self::sessionGet('user_net')) === 0;
        }

        return $this->ip == self::sessionGet('user_ip');
    }

    /**
     * Загружает данные для авторизованного пользователя
     * @param integer $user_id id пользователя, прошедшего авторизацию
     * @return array
     */
    public function loadAuthUser($user_id) {

        $config = cmsConfig::getInstance();
        $model  = cmsCore::getModel('users');

        $model->filterIsNull('is_deleted');

        $user = $model->getUser($user_id);

        if (!$user) {
            self::logout();
            return [];
        }

        $user = cmsEventsManager::hook('user_preloaded', $user);

        if (!$user) {
            self::logout();
            return [];
        }

        $model->startTransaction();

        // если дата последнего визита еще не сохранена в сессии,
        // значит авторизация была только что
        // сохраним дату в сессии и обновим в базе
        if (!self::isSessionSet('user:date_log') || ((time() - self::USER_ONLINE_INTERVAL) >= strtotime($user['date_log']))) {

            if (!self::isSessionSet('user:date_log')) {
                self::sessionSet('user:date_log', $user['date_log']);
            }

            $model->updateUserDateLog($user_id);
        }

        // создаем online-сессию
        $model->insertOrUpdate('sessions_online', ['user_id' => $user_id], ['date_created' => null]);

        $model->endTransaction(true);

        // заполняем объект данными из базы
        foreach ($user as $field => $value) {
            $this->{$field} = $value;
        }

        // конвертим список аватаров в массив
        // к пути каждого аватара добавляем путь корня
        $this->avatar = cmsModel::yamlToArray($this->avatar);
        if ($this->avatar) {
            foreach ($this->avatar as $size => $path) {
                $this->avatar[$size] = $config->upload_host . '/' . $path;
            }
        }

        // кешируем список друзей в сессию
        $this->recacheFriends();

        // восстанавливаем те поля, которые не должны
        // изменяться в течении сессии
        $this->date_log  = self::sessionGet('user:date_log');
        $this->perms     = self::getPermissions($user['groups']);
        $this->is_logged = true;

        return cmsEventsManager::hook('user_loaded', $user);
    }

//============================================================================//
//============================================================================//

    public static function setUserSession($user, $last_ip = false) {

        self::sessionSet('user', [
            'id'          => $user['id'],
            'slug'        => $user['slug'],
            '2fa'         => !empty($user['2fa']),
            'is_old_auth' => !empty($user['is_old_auth']),
            'groups'      => $user['groups'],
            'time_zone'   => $user['time_zone'],
            'perms'       => isset($user['permissions']) ? $user['permissions'] : self::getPermissions($user['groups']),
            'is_admin'    => $user['is_admin']
        ]);

        self::restrictSessionToIp($last_ip);
    }

    /**
     * Авторизует пользователя по кукису
     * @param str $auth_token
     */
    public static function autoLogin($auth_token) {

        if (!preg_match('/^[0-9a-z]{128}$/i', $auth_token)) {
            return 0;
        }

        $model = cmsCore::getModel('users');

        $user = $model->joinInner('{users}_auth_tokens', 'au', 'au.user_id = i.id')->
                filterEqual('au.auth_token', $auth_token)->
                filterIsNull('is_deleted')->select('au.date_auth')->getUser();

        if (!$user || $user['is_locked']) {
            return 0;
        }
        // проверяем не истек ли срок действия токена
        if ((time() - strtotime($user['date_log'])) > self::AUTH_TOKEN_EXPIRATION_INT) {
            $model->deleteAuthToken($auth_token);
            return 0;
        }

        $model->startTransaction();

        // обновляем дату токена
        $model->filterEqual('auth_token', $auth_token)->updateFiltered('{users}_auth_tokens', [
            'date_log' => null
        ], true);

        $model->updateUserIp($user['id']);

        $model->endTransaction(true);

        $user = cmsEventsManager::hook('user_login', $user);

        self::setUserSession($user, $user['ip']);

        self::sessionRegenerate();

        return intval($user['id']);
    }

    /**
     * Авторизует пользователя
     *
     * @param string $email
     * @param string $password
     * @param boolean $remember
     * @param boolean $complete_login
     * @return integer|array
     */
    public static function login($email, $password, $remember = false, $complete_login = true, $model = null) {

        if (!$email || !$password) {
            return 0;
        }

        if($model === null) {
            $model = cmsCore::getModel('users');
        }

        $user = $model->getUserByAuth($email, $password);

        if (!$user) {
            $user = cmsEventsManager::hook('user_auth_error', ['email' => $email, 'password' => $password]);
        }

        if (empty($user['id'])) {
            return 0;
        }

        $user = cmsEventsManager::hook('user_login', $user);

        $user['permissions'] = self::getPermissions($user['groups']);

        if ($complete_login) {

            self::loginComplete($user, $remember);

            return intval($user['id']);
        }

        return $user;
    }

    /**
     * Завершает авторизацию, устанавливая сессию
     * и другие параметры авторизации
     *
     * @param array $user
     * @param boolean $remember
     * @return boolean
     */
    public static function loginComplete($user, $remember = false) {

        self::setUserSession($user);

        $model = cmsCore::getModel('users');

        if ($remember) {

            $auth_token = hash('sha512', string_random(32, $user['email']));

            self::setCookie('auth', $auth_token, self::AUTH_TOKEN_EXPIRATION_INT);

            $model->setAuthToken($user['id'], $auth_token);

            $model->deleteExpiredToken($user['id'], self::AUTH_TOKEN_EXPIRATION_INT);

            self::$auth_token = $auth_token;
        }

        $model->updateUserIp($user['id']);

        self::getInstance()->id = $user['id'];
        self::getInstance()->is_logged = true;

        self::sessionRegenerate();

        return true;
    }

    /**
     * Выход пользователя
     */
    public static function logout() {

        $model = cmsCore::getModel('users');

        $userSession = self::sessionGet('user');

        if (!empty($userSession['id'])) {

            $model->updateUserDateLog($userSession['id']);

            $model->filterEqual('user_id', $userSession['id'])->deleteFiltered('sessions_online');

            cmsEventsManager::hook('user_logout', $userSession);
        }

        if (self::hasCookie('auth')) {

            $auth_cookie = self::getCookie('auth');

            if (preg_match('/^[0-9a-z]{128}$/i', $auth_cookie)) {
                $model->deleteAuthToken($auth_cookie);
            }

            self::unsetCookie('auth');
        }

        // если login и logout выполняются в пределах
        // одной загрузки страницы
        if (self::$auth_token) {
            $model->deleteAuthToken(self::$auth_token);
            self::$auth_token = null;
        }

        self::sessionUnset('user');

        self::sessionRegenerate();

        return true;
    }

//============================================================================//
//============================================================================//

    public static function userIsOnline($user_id) {

        if (isset(self::$cached_online[$user_id])) {
            return self::$cached_online[$user_id];
        }

        self::$cached_online[$user_id] = false;

        $model = new cmsModel();

        $date_created = $model->filterEqual('user_id', $user_id)->getFieldFiltered('sessions_online', 'date_created');

        if ($date_created && (time() - self::USER_ONLINE_INTERVAL) < strtotime($date_created)) {
            self::$cached_online[$user_id] = true;
        }

        return self::$cached_online[$user_id];
    }

    public static function isLogged() {
        return self::getInstance()->is_logged;
    }

    public static function isAdmin() {
        return self::getInstance()->is_admin;
    }

    public static function goLogin($back_url = '') {
        if (!$back_url) {
            $back_url = str_replace("\r\n", '', $_SERVER['REQUEST_URI']);
        }
        header('Location:' . href_to('auth', 'login') . '?' . http_build_query(['back' => $back_url]));
        exit;
    }

//============================================================================//
//============================================================================//

    public static function setSessionSavePath($save_handler, $path) {

        if (!$path) { return false; }

        if (ini_set('session.save_handler', $save_handler) === false) {
            return false;
        }

        if ($save_handler === 'files') {

            if (!is_dir($path)) {
                if (!mkdir($path, 0755, true)) {
                    return false;
                }
            }

            if (!is_writable($path)) {
                return false;
            }
        }

        session_save_path($path);

        return true;
    }

    /**
     * Стратует сессию PHP
     * и меняет таймзону, если задана у юзера
     *
     * @param cmsConfig $config
     */
    public static function sessionStart(cmsConfig $config) {

        // Устанавливаем директорию сессий
        self::setSessionSavePath($config->session_save_handler, $config->session_save_path);

        session_name($config->session_name);

        $cookie_domain = null;

        if ($config->cookie_domain) {
            $cookie_domain = '.' . $config->cookie_domain;
        }

        session_set_cookie_params(0, '/;SameSite=Lax', $cookie_domain, cmsConfig::isSecureProtocol(), true);

        session_start();

        // таймзона сессии
        $session_time_zone = self::sessionGet('user:time_zone');

        // если таймзона в сессии отличается от дефолтной
        if ($session_time_zone && $session_time_zone !== $config->time_zone) {
            $config->set('time_zone', $session_time_zone);
        }
    }

    public static function sessionRegenerate() {

        session_regenerate_id(false);

        $id = session_id();

        session_write_close();

        session_id($id);

        session_start();
    }

    public static function sessionSet($key, $value) {

        if (strpos($key, ':') === false) {
            $_SESSION[$key] = $value;
        } else {
            list($key, $subkey) = explode(':', $key);
            $_SESSION[$key][$subkey] = $value;
        }

    }

    public static function sessionPush($key, $value) {
        $_SESSION[$key][] = $value;
    }

    public static function sessionGet($key, $is_clean = false) {

        if (!self::isSessionSet($key)) {
            return false;
        }

        if (strpos($key, ':') === false) {
            $value = $_SESSION[$key];
        } else {
            list($key, $subkey) = explode(':', $key);
            $value = $_SESSION[$key][$subkey];
        }

        if ($is_clean) {
            self::sessionUnset($key);
        }

        return $value;
    }

    public static function isSessionSet($key) {
        if (strpos($key, ':') === false) {
            return isset($_SESSION[$key]);
        } else {
            list($key, $subkey) = explode(':', $key);
            return isset($_SESSION[$key][$subkey]);
        }
    }

    public static function sessionUnset($key) {
        if (strpos($key, ':') === false) {
            unset($_SESSION[$key]);
        } else {
            list($key, $subkey) = explode(':', $key);
            unset($_SESSION[$key][$subkey]);
        }
    }

    public static function sessionClear() {
        $_SESSION = [];
    }

    /**
     * Устанавливает куки
     * @param string $key Имя кукиса
     * @param string $value Значение
     * @param int $time Время жизни, в секундах
     * @param string $path Путь на сервере
     * @param bool $http_only Куки недоступны для скриптов
     * @param string $domain Домен действия. пусто - только текущий
     * */
    public static function setCookie($key, $value, $time = 3600, $path = '/', $http_only = true, $domain = '') {

        $cookie_domain = cmsConfig::get('cookie_domain');

        if (!$domain && $cookie_domain) {
            $domain = $cookie_domain;
        }

        if (PHP_VERSION_ID < 70300) {
            return setcookie('icms[' . $key . ']', $value, time() + $time, $path, $domain, false, $http_only);
        } else {
            return setcookie('icms[' . $key . ']', $value, [
                'expires'  => time() + $time,
                'path'     => $path,
                'domain'   => $domain,
                'samesite' => 'Lax',
                'secure'   => cmsConfig::isSecureProtocol(),
                'httponly' => $http_only
            ]);
        }
    }

    public static function setCookiePublic($key, $value, $time = 3600, $path = '/') {
        return self::setCookie($key, $value, $time, $path, false);
    }

    public static function unsetCookie($key, $path = '/', $domain = null) {
        return self::setCookie($key, '', -3600, $path, true, $domain);
    }

    /**
     * Проверяет наличие кукиса и возвращает его значение
     *
     * @param string $key Имя кукиса
     * @param string $var_type Тип переменной, по умолчанию string
     * @param callable $callback
     * @return mixed
     */
    public static function getCookie($key, $var_type = 'string', $callback = false) {

        if (isset($_COOKIE['icms'][$key])) {

            $cookie = $_COOKIE['icms'][$key];

            if ($var_type !== null) {
                @settype($cookie, $var_type);
            }

            if (is_callable($callback)) {
                $cookie = call_user_func_array($callback, array($cookie));
            }

            return $cookie;
        } else {
            return false;
        }
    }

    public static function hasCookie($key) {
        return isset($_COOKIE['icms'][$key]);
    }

//============================================================================//
//============================================================================//

    public static function getSetUPS($key) {
        return cmsCore::getModel('users')->getSetUPS($key);
    }

    /**
     * Устанавливает для пользователя его уникальные персональные настройки
     * User Personal Setting
     *
     * @param str       $key        Ключ настроек
     * @param str|array $data       Данные
     * @param int       $user_id    Ид юзера
     * @return bool
     */
    public static function setUPS($key, $data, $user_id = null) {

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        return (bool) cmsCore::getModel('users')->setUPS($key, $data, $user_id);
    }

    public static function getUPS($key, $user_id = null) {

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        return cmsCore::getModel('users')->getUPS($key, $user_id);
    }

    public static function getUPSActual($key, $data, $user_id = null) {

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

    public static function deleteUPS($key, $user_id = null) {

        if (empty($key) || (!$user_id && !($user_id = self::getInstance()->id))) {
            return false;
        }

        return cmsCore::getModel('users')->deleteUPS($key, $user_id);
    }

    public static function deleteUPSlist($key) {
        return cmsCore::getModel('users')->deleteUPS($key);
    }

//============================================================================//
//============================================================================//

    public static function addSessionMessage($message, $class = 'info', $is_keep = false) {
        self::sessionPush('core_message', ['class' => $class, 'text' => $message, 'is_keep' => $is_keep]);
    }

    public static function getSessionMessages($is_clear = true) {

        if (self::isSessionSet('core_message')) {
            $messages = self::sessionGet('core_message');
        } else {
            $messages = false;
        }
        if ($is_clear) {
            self::clearSessionMessages();
        }

        return $messages;
    }

    public static function clearSessionMessages() {
        self::sessionUnset('core_message');
    }

//============================================================================//
//============================================================================//

    public static function getPermissions($groups) {
        return cmsPermissions::getUserPermissions($groups);
    }

    public static function getPermissionValue($subject, $permission) {

        $user = self::getInstance();

        if (!isset($user->perms[$subject])) {
            return false;
        }
        if (!isset($user->perms[$subject][$permission])) {
            return false;
        }

        return $user->perms[$subject][$permission];
    }

//============================================================================//
//============================================================================//

    public static function isDenied($subject, $permission, $value = true, $is_admin_strict = false) {

        $user = self::getInstance();

        if (!$is_admin_strict) {
            if ($user->is_admin) {
                return false;
            }
        }

        if (!isset($user->perms[$subject])) {
            return false;
        }
        if (!isset($user->perms[$subject][$permission])) {
            return false;
        }

        return $user->perms[$subject][$permission] == $value;
    }

    public static function isAllowed($subject, $permission, $value = true, $is_admin_strict = false) {

        $user = self::getInstance();

        if (!$is_admin_strict) {
            if ($user->is_admin) {
                return true;
            }
        }

        if (!isset($user->perms[$subject])) {
            return false;
        }
        if (!isset($user->perms[$subject][$permission])) {
            return false;
        }
        if ($user->perms[$subject][$permission] != $value) {
            return false;
        }

        return true;
    }

    public static function isPermittedLimitReached($subject, $permission, $current_value = 0, $is_admin_strict = false) {

        $user = self::getInstance();

        if (!$is_admin_strict) {
            if ($user->is_admin) {
                return false;
            }
        }

        if (!isset($user->perms[$subject])) {
            return false;
        }
        if (!isset($user->perms[$subject][$permission])) {
            return false;
        }
        if ((int) $current_value >= $user->perms[$subject][$permission]) {
            return true;
        }

        return false;
    }

    public static function isPermittedLimitHigher($subject, $permission, $current_value = 0, $is_admin_strict = false) {

        $user = self::getInstance();

        if (!$is_admin_strict) {
            if ($user->is_admin) {
                return false;
            }
        }

        if (!isset($user->perms[$subject])) {
            return false;
        }
        if (!isset($user->perms[$subject][$permission])) {
            return false;
        }
        if ((int) $current_value < $user->perms[$subject][$permission]) {
            return true;
        }

        return false;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет членство пользователя в группе
     * @param int $group_id ID группы
     * @return boolean
     */
    public function isInGroup($group_id) {
        return in_array($group_id, $this->groups);
    }

    /**
     * Проверяет членство пользователя в любой группе из списка
     * @param array $groups Список ID групп
     * @return boolean
     */
    public function isInGroups($groups) {
        return self::isUserInGroups($this->groups, $groups);
    }

    public static function isUserInGroups($user_groups, $groups) {

        if (empty($groups) || in_array(0, $groups)) {
            return true;
        }

        foreach ($groups as $group_id) {
            if(in_array($group_id, $user_groups)) {
                return true;
            }
        }

        return false;
    }

//============================================================================//
//============================================================================//

    public function isPrivacyAllowed($profile, $option, $strict = false) {

        if ($this->is_admin && !$strict) {
            return true;
        }

        if ($profile['id'] == $this->id) {
            return true;
        }

        if (!$profile || !$option) {
            return false;
        }

        $options = $profile['privacy_options'];

        if (!isset($options[$option])) {
            return true;
        }

        if ($options[$option] === 'anyone') {
            return true;
        }

        if ($options[$option] === 'friends' && $this->isFriend($profile['id'])) {
            return true;
        }

        return false;
    }

//============================================================================//
//============================================================================//

    public function recacheFriends() {

        $friends = cmsCore::getModel('users')->getFriendsIds($this->id);

        $this->friends    = $friends['friends'];
        $this->subscribes = $friends['subscribes'];

        return $this;
    }

    public function isFriend($friend_id, $type = 'friends') {

        if (!$friend_id || !$this->id) {
            return false;
        }

        if ($friend_id == $this->id) {
            return true;
        }

        if (empty($this->{$type})) {
            return false;
        }

        return in_array($friend_id, $this->{$type});
    }

    public function isSubscribe($friend_id) {
        return $this->isFriend($friend_id, 'subscribes');
    }

    public function hasFriends() {
        return !empty($this->friends);
    }

    public function hasSubscribes() {
        return !empty($this->subscribes);
    }

}
