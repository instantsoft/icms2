<?php
/**
 * Класс для работы с запросами/параметрами,
 * передаваемыеми в контроллеры
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
     * @param array $data Параметры для контроллера
     * @param integer $context Контекст (если не указан, определяется автоматически)
     */
    public function __construct($data, $context = cmsRequest::CTX_AUTO_DETECT) {

        $this->setData($data);

        if ($context == cmsRequest::CTX_AUTO_DETECT) {
            $this->context = $this->detectContext();
        } else {
            $this->context = $context;
        }
    }

//============================================================================//
//============================================================================//

    /**
     * Определяет контекст текущего запроса (стандартный или ajax)
     * @return integer
     */
    private function detectContext() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            return cmsRequest::CTX_AJAX;
        } else {
            return cmsRequest::CTX_STANDARD;
        }
    }

    /**
     * Возвращает текущий контекст использования
     * @return integer
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * Устанавливает текущий контекст использования
     * @param integer $context
     * @return $this
     */
    public function setContext($context) {

        $this->context = $context;

        return $this;
    }

    /**
     * Возвращает true, если запрос вызван через URL
     * @return boolean
     */
    public function isStandard() {
        return ($this->context == cmsRequest::CTX_STANDARD);
    }

    /**
     * Возвращает true, если запрос вызван другим контроллером
     * @return boolean
     */
    public function isInternal() {
        return ($this->context == cmsRequest::CTX_INTERNAL);
    }

    /**
     * Возвращает true, если запрос вызван через AJAX
     * @return boolean
     */
    public function isAjax() {
        return ($this->context == cmsRequest::CTX_AJAX);
    }

    /**
     * Возвращает тип контекста запроса
     * @return integer
     */
    public function getType() {
        if (isset($_SERVER['HTTP_ICMS_REQUEST_TYPE']) && is_numeric($_SERVER['HTTP_ICMS_REQUEST_TYPE'])) {
            return $_SERVER['HTTP_ICMS_REQUEST_TYPE'];
        } else {
            return self::CTX_TYPE_STANDARD;
        }
    }

    /**
     * Возвращает true, если тип контекста запроса для модального окна
     * @return boolean
     */
    public function isTypeModal() {
        return $this->getType() == self::CTX_TYPE_MODAL;
    }

    /**
     * Возвращает true, если тип контекста запроса для API
     * @return boolean
     */
    public function isTypeApi() {
        return $this->getType() == self::CTX_TYPE_API;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет наличие переменной запроса
     * @param string $var Название переменной
     * @return boolean
     */
    public function has($var) {
        return isset($this->data[$var]);
    }

    /**
     * Проверяет наличие переменной по переданной вложенности
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
     * @param string $var Название переменной
     * @return boolean
     */
    public function hasInQuery($var) {

        $query = cmsCore::getInstance()->uri_query;

        if (!$query) {
            return false;
        }

        return isset($query[$var]);
    }

    /**
     * Возвращает параметр из запроса
     * @param string $var Название переменной
     * @param mixed $default Значение по умолчанию, если в запросе переменной нет
     * @param string $var_type Тип переменной
     * @return mixed
     */
    public function get($var, $default = false, $var_type = null) {

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
            } else {
                settype($value, $var_type);
            }
        }

        return $value;
    }

    /**
     * Возвращает все имеющиеся параметры
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    public function getData() {
        return $this->getAll();
    }

    /**
     * Устанавливает параметры текущего запроса
     * @param array $data
     */
	public function setData($data) {

        $this->data = $data;

        return $this;
    }

    /**
     * Устанавливает значение параметра текущего запроса
     * @param string $name Название параметра
     * @param mixed $value Значение параметра
     * @return $this
     */
    public function set($name, $value) {

        $this->data[$name] = $value;

        return $this;
    }

//============================================================================//
//============================================================================//

    private static function loadDeviceType() {

        $device_type = cmsUser::getCookie('device_type', 'string', function ($cookie) {
            return trim(strip_tags($cookie));
        });

        if (!$device_type || !in_array($device_type, self::$device_types, true)) {

            cmsCore::loadLib('mobile_detect.class');

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
