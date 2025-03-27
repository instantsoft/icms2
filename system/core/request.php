<?php
/**
 * Класс для работы с запросами/параметрами,
 * передаваемыеми в контроллеры
 *
 * @method bool hasHeader(string $key) Проверяет наличие заголовка запроса
 * @method bool hasServer(string $key) Проверяет наличие значение ключа в массиве $this->server
 * @method mixed getHeader(string $key, mixed $default) Возвращает значение заголовка запроса
 * @method mixed getServer(string $key, mixed $default) Возвращает значение в массиве $this->server
 * @method void setHeader(string $key, mixed $value) Устанавливает заголовок запроса
 * @method void setServer(string $key, mixed $value) Устанавливает значение в $this->server
 */
class cmsRequest {

    /**
     * Константы контекста запроса
     * @var integer
     */
    const CTX_AUTO_DETECT = 0;
    const CTX_STANDARD    = 1;
    const CTX_INTERNAL    = 2;
    const CTX_AJAX        = 3;

    /**
     * Константы типа контекста запроса
     * @var integer
     */
    const CTX_TYPE_STANDARD = 0;
    const CTX_TYPE_MODAL    = 1;
    const CTX_TYPE_API      = 2;

    /**
     * Массив данных запроса
     * @var array
     */
    private $data = [];

    /**
     * Массив данных сервера
     * @var array
     */
    private $server = [];

    /**
     * Заголовки запроса (берутся из $_SERVER)
     * @var array
     */
    private $header = [];

    /**
     * Необработанные данные тела запроса
     * @var string|false|null
     */
    private $content = null;

    /**
     * HTTP метод запроса
     * @var ?string
     */
    protected $method = null;

    /**
     * Текущий контекст запроса
     * @var integer
     */
    private $context = 0;

    /**
     * Общесистемное определение типа устройства,
     * с которого зашли на сайт
     * @var string
     */
    private static $device_type = null;

    /**
     * Возможные типы устройств
     * @var array
     */
    public static $device_types = ['desktop', 'mobile', 'tablet'];

    /**
     * Создает объект запроса
     *
     * @param array $data Массив параметров, например $_REQUEST
     * @param integer $context Контекст (если не указан, определяется автоматически)
     * @param array $server Массив параметров сервера, например $_SERVER
     * @param ?string $content Необработанные данные тела запроса
     */
    public function __construct(array $data, int $context = self::CTX_AUTO_DETECT, array $server = [], $content = null) {

        $this->content = $content;

        $this->setData($data);

        $this->setServerData($server);

        if ($context == self::CTX_AUTO_DETECT) {
            $this->context = $this->detectContext();
        } else {
            $this->context = $context;
        }
    }

    /**
     * Ловим методы, работающие со свойствами
     * get|set|hasHeader, get|set|hasServer
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments) {

        $action = substr($name, 0, 3);

        if (!in_array($action, ['get', 'set', 'has'], true)) {
            throw new BadMethodCallException('No such method exists: ' . $name);
        }

        $key = strtolower(substr($name, 3));

        array_unshift($arguments, $key);

        return call_user_func_array([$this, $action . 'Property'], $arguments);
    }

//============================================================================//
//============================================================================//

    /**
     * Определяет контекст текущего запроса (стандартный или ajax)
     * @return integer
     */
    private function detectContext() {

        if ($this->getHeader('X_REQUESTED_WITH') === 'XMLHttpRequest') {
            return cmsRequest::CTX_AJAX;
        } else {
            return cmsRequest::CTX_STANDARD;
        }
    }

    /**
     * Возвращает текущий контекст использования
     *
     * @return integer
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * Устанавливает текущий контекст использования
     *
     * @param integer $context
     * @return $this
     */
    public function setContext($context) {

        $this->context = $context;

        return $this;
    }

    /**
     * Возвращает true, если запрос вызван через URL
     *
     * @return boolean
     */
    public function isStandard() {
        return ($this->context == cmsRequest::CTX_STANDARD);
    }

    /**
     * Возвращает true, если запрос вызван другим контроллером
     *
     * @return boolean
     */
    public function isInternal() {
        return ($this->context == cmsRequest::CTX_INTERNAL);
    }

    /**
     * Возвращает true, если запрос вызван через AJAX
     *
     * @see https://wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return boolean
     */
    public function isAjax() {
        return ($this->context == cmsRequest::CTX_AJAX);
    }

    /**
     * Возвращает тип контекста запроса
     *
     * @return integer
     */
    public function getType() {

        $type = $this->getHeader('ICMS_REQUEST_TYPE');

        if ($type && is_numeric($type)) {
            return $type;
        } else {
            return self::CTX_TYPE_STANDARD;
        }
    }

    /**
     * Возвращает true, если тип контекста запроса для модального окна
     *
     * @return boolean
     */
    public function isTypeModal() {
        return $this->getType() == self::CTX_TYPE_MODAL;
    }

    /**
     * Возвращает true, если тип контекста запроса для API
     *
     * @return boolean
     */
    public function isTypeApi() {
        return $this->getType() == self::CTX_TYPE_API;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет наличие переменной запроса
     *
     * @param string $var Название переменной
     * @return boolean
     */
    public function has(string $var) {
        return isset($this->data[$var]);
    }

    /**
     * Проверяет наличие переменной по переданной вложенности
     *
     * @return boolean
     */
    public function hasInArray() {

        $keys = func_get_args();

        if (count($keys) === 1) {
            $keys = $keys[0];
        }

        return (bool) array_value_recursive($keys, $this->data);
    }

    /**
     * Проверяет наличие переменной в GET запросе
     *
     * @param string $var Название переменной
     * @return boolean
     */
    public function hasInQuery(string $var) {

        $query = cmsCore::getInstance()->uri_query;

        if (!$query) {
            return false;
        }

        return isset($query[$var]);
    }

    /**
     * Возвращает параметр из запроса
     *
     * @param string $var Название переменной путь до ключа через двоеточие
     * @param mixed $default Значение по умолчанию, если в запросе переменной нет
     * @param string $var_type Тип переменной
     * @return mixed
     */
    public function get(string $var, $default = false, $var_type = null) {

        //если значение не определено, возвращаем умолчание

        if (strpos($var, ':') === false) {

            if (!$this->has($var)) {
                return $default;
            }

            $value = $this->data[$var];

        } else {

            $value = array_value_recursive($var, $this->data);
            if ($value === null) {
                return $default;
            }
        }

        if ($var_type === null) {

            // типизируем, основываясь на значении по умолчанию
            // берем во внимание не все типы
            $default_type = gettype($default);

            if (in_array($default_type, ['integer', 'string', 'double', 'array'])) {
                $var_type = $default_type;
            }
        }

        if ($var_type !== null) {

            if (is_array($value) && $var_type !== 'array') {
                $value = '';
            }

            settype($value, $var_type);
        }

        return $value;
    }

    /**
     * Возвращает содержимое тела запроса
     *
     * @return string|false
     */
    public function getContent() {

        if ($this->content === null) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * Возвращает все имеющиеся параметры
     *
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * Псевдоним getAll
     *
     * @return array
     */
    public function getData() {
        return $this->getAll();
    }

    /**
     * Устанавливает параметры текущего запроса
     *
     * @param array $data
     */
    public function setData(array $data) {

        $this->data = $data;

        return $this;
    }

    /**
     * Устанавливает значение параметра текущего запроса
     *
     * @param string $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function set(string $name, $value) {

        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Возвращает значение ключа в свойстве
     *
     * @param string $prop Имя свойства
     * @param string $key Ключ в массиве
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    private function getProperty(string $prop, string $key, $default = null) {
        return $this->hasProperty($prop, $key) ? $this->{$prop}[$key] : $default;
    }

    /**
     * Устанавливает значение ключа в свойстве
     *
     * @param string $prop Имя свойства
     * @param string $key Ключ в массиве
     * @param mixed $value Значение
     */
    private function setProperty(string $prop, string $key, $value) {
        $this->{$prop}[$key] = $value;
    }

    /**
     * Проверяет наличие ключа в свойстве
     *
     * @param string $prop Имя свойства
     * @param string $key Ключ в массиве
     * @return bool
     */
    private function hasProperty(string $prop, string $key) {
        return array_key_exists($key, $this->{$prop});
    }

    /**
     * Устанавливает массив параметров сервера и заголовки
     *
     * @param array $server
     */
    public function setServerData(array $server) {

        $this->server = $server;

        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $this->header[substr($key, 5)] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true) && $value !== '') {
                $this->header[$key] = $value;
            }
        }
    }

    /**
     * Проверяет, является ли метод запроса указанного типа
     *
     * @param string $method Метод запроса в верхнем регистре (GET, POST и т.д.)
     */
    public function isMethod(string $method) {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * Проверяет, является ли метод безопасным
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.1
     * @return bool
     */
    public function isMethodSafe() {
        return in_array($this->getMethod(), ['GET', 'HEAD', 'OPTIONS', 'TRACE']);
    }

    /**
     * Проверяет, является ли метод идемпотентным
     *
     * @return bool
     */
    public function isMethodIdempotent() {
        return in_array($this->getMethod(), ['HEAD', 'GET', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PURGE']);
    }

    /**
     * Проверяет, является ли метод кэшируемым
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.3
     * @return bool
     */
    public function isMethodCacheable() {
        return in_array($this->getMethod(), ['GET', 'HEAD']);
    }

    /**
     * Возвращает текущий HTTP метод запроса
     *
     * @return string
     */
    public function getMethod() {

        if ($this->method === null) {

            $this->method = strtoupper($this->getServer('REQUEST_METHOD', 'GET'));
        }

        return $this->method;
    }

    /**
     * Возвращает ip адрес клиента
     *
     * @staticvar ?string $ip
     * @return string
     */
    public function getClientIp() {

        static $ip = null;

        if ($ip === null) {

            $ip = $this->getServer(cmsConfig::get('detect_ip_key'), '127.0.0.1');

            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $ip = '127.0.0.1';
            }
        }

        return $ip;
    }

    /**
     * Проверяет, является ли запрос по протоколу HTTPS
     *
     * @return bool
     */
    public function isSecure() {

        return (($https = $this->getServer('HTTPS')) && strtolower($https) !== 'off') ||
                $this->getServer('SERVER_PORT') == 443 ||
                $this->getServer('X_FORWARDED_PROTO') === 'https' ||
                $this->getServer('X_FORWARDED_SSL') === 'on';
    }

    /**
     * Возвращает схему запроса
     *
     * @return string https или http
     */
    public function getScheme() {
        return $this->isSecure() ? 'https' : 'http';
    }

//============================================================================//
//============================================================================//

    private static function loadDeviceType() {

        $device_type = cmsUser::getCookie('device_type', 'string');

        if (!$device_type || !in_array($device_type, self::$device_types, true)) {

            $detect = new Mobile_Detect();

            $device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');

            cmsUser::setCookie('device_type', $device_type, 31536000); // на 1 год
        }

        self::$device_type = $device_type;
    }

    /**
     * Возвращает тип устройства, используемое в текущем запросе
     * @return string
     */
    public static function getDeviceType() {

        if (self::$device_type === null) {
            self::loadDeviceType();
        }

        return self::$device_type;
    }

}
