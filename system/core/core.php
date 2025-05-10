<?php
/**
 * Основной класс InstantCMS
 */
class cmsCore {

    private static $instance;

    /**
     * Текущий URI страницы без корня
     * @var string
     */
    public $uri = '';

    /**
     * Текущий URI страницы без корня
     * Без учёта применения псевдонима
     * @var string
     */
    public $uri_before_remap = '';

    /**
     * Полный текущий URI
     * @var string
     */
    public $uri_absolute = '';

    /**
     * Имя контроллера, определённое из URI
     * @var string
     */
    public $uri_controller = '';

    /**
     * Имя контроллера, определённое из URI
     * Без учёта применения псевдонима
     * @var string
     */
    public $uri_controller_before_remap = '';

    /**
     * Имя действия контроллера, определённое из URI
     * @var string
     */
    public $uri_action = '';

    /**
     * Параметры действия контроллера, определённые из URI
     * @var array
     */
    public $uri_params = [];

    /**
     * Массив GET параметров
     * @var array
     */
    public $uri_query = [];

    /**
     * Массив страниц (cms_widgets_pages), которые совпадают
     * по маске с текущей страницей
     * @var ?array
     */
    private $matched_pages = null;

    /**
     * Все страницы (cms_widgets_pages)
     * @var ?array
     */
    private $widgets_pages = null;

    /**
     * Язык, определённый по браузеру пользователя
     * @var ?string
     */
    private static $browser_language = null;

    /**
     * Текущий язык (необязательно тот, что в конфигурации)
     * @var string
     */
    private static $language = 'ru';

    /**
     * Текущий языковой префикс для формирования url
     * @var string
     */
    private static $language_href_prefix = '';

    /**
     * Информация о версии InstantCMS
     * @var ?array
     */
    private static $core_version = null;

    /**
     * Массив объектов контроллеров
     * @var array
     */
    private static $controllers_instance = [];

    /**
     * Массив шаблонов, см. self::getTemplates()
     * @var ?array
     */
    private static $templates = null;

    /**
     * Массив подключенных файлов, см. self::includeFile()
     * @var array
     */
    private static $includedFiles = [];

    /**
     * Имя текущего основного контроллера
     * @var string
     */
    public $controller = '';

    /**
     * Вспомогательный массив объявленных контроллеров по URI
     * для исключения повторной инициализации
     * @var array
     */
    private $defined_controllers = [];

    /**
     * Объект запроса
     * @var \cmsRequest
     */
    public $request;

    /**
     * Объект ответа
     * @var \cmsResponse
     */
    public $response;

    /**
     * DB link
     * @var \cmsDatabase
     */
    public $db;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {

        $this->request = new cmsRequest($_REQUEST, cmsRequest::CTX_AUTO_DETECT, $_SERVER);

        $this->response = cmsResponse::getInstance();

        self::$language = cmsConfig::get('language');
    }

    /**
     * Выполняет все действия в ответ на HTTP запрос
     *
     * @param string $request_uri Строка запроса
     */
    public function runHttp($request_uri) {

        $this->response->setHeader('Content-type', 'text/html; charset=utf-8');

        // Имитация задержки для отладки
        if (cmsConfig::get('emulate_lag')) {
            usleep(350000);
        }

        // Выясняем язык браузера
        $this->detectBrowserLanguage();

        //Запускаем роутинг
        $this->route($request_uri);

        // Локализация
        $this->initLanguage();

        // Выясняем какой шаблон надо показать по умолчанию
        $this->detectHttpTemplate();

        // Инициализируем шаблонизатор
        $template = cmsTemplate::getInstance();

        cmsEventsManager::hook('engine_start');

        // загружаем и устанавливаем страницы для текущего URI
        $this->loadMatchedPages();

        // Проверяем доступ
        if (cmsEventsManager::hook('page_is_allowed', true)) {

            //Запускаем контроллер
            $this->runController();
        }

        // Проверяем заголовок If-Modified-Since и
        // ответ Last-Modified, его могут установить только контроллеры
        if (!$this->response->isNotModified($this->request)) {

            // формируем виджеты
            $this->runWidgets();

            // Добавляем готовую страницу в ответ
            $this->response->setContent($template->getRenderedPage());
        }

        cmsEventsManager::hook('engine_stop');

        return $this->response;
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает информацию о версии CMS в виде строки
     *
     * @param boolean $show_date Показывать дату версии
     * @return string
     */
    public static function getVersion($show_date = false) {

        $version = self::getVersionArray();

        if (!$show_date && isset($version['date'])) { unset($version['date']); }

        return $version['version'] . ($show_date ? ' ' . LANG_FROM . ' ' . $version['date'] : '');
    }

    /**
     * Возвращает информацию о версии CMS
     * в виде массива с ключами:
     *  - date Дата релиза
     *  - version Полная версия CMS
     *  - raw
     *  -- major
     *  -- minor
     *  -- build
     *  -- date
     * @return array
     */
    public static function getVersionArray() {

        if (self::$core_version === null) {

            $file = cmsConfig::get('root_path') . ltrim(ICMS_CONFIG_DIR, '/') . 'version.ini';
            if (!is_readable($file)) {
                die('system/config/version.ini not found');
            }

            $version = parse_ini_file($file);

            self::$core_version = [
                'date'      => $version['date'],
                'is_custom' => $version['is_custom']??0,
                'version'   => $version['major'] . '.' . $version['minor'] . '.' . $version['build'],
                'raw'       => $version
            ];
        }

        return self::$core_version;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет директорию на возможность записи в неё
     *
     * @param string $path Путь до директории
     * @param boolean $is_force_mkdir Создавать, если нет
     * @return boolean
     */
    public static function isWritable($path, $is_force_mkdir = true) {

        if ($is_force_mkdir && !file_exists($path)) {
            @mkdir($path);
        }

        return is_writable($path);
    }

    /**
     * Подключает файл через include_once
     *
     * @param string $file Путь относительно корня сайта без начального слеша
     * @return mixed
     */
    public static function includeFile($file) {

        $file = cmsConfig::get('root_path') . $file;

        if (isset(self::$includedFiles[$file])) {
            return self::$includedFiles[$file];
        }

        if (!is_readable($file)) {
            self::$includedFiles[$file] = false;
            return false;
        }

        $result = include_once $file;

        if (is_null($result)) {
            $result = true;
        }

        self::$includedFiles[$file] = $result;

        return $result;
    }

    /**
     * Подключает файл через require
     *
     * @param string $file Путь относительно корня сайта без начального слеша
     * @return mixed
     */
    public static function requireFile($file) {

        $file = cmsConfig::get('root_path') . $file;

        if (!is_readable($file)) {
            return false;
        }

        $result = require $file;

        if (is_null($result)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Подключает файл и вызывает функцию в нём,
     * возвращая её результат
     *
     * @param string $file Путь относительно корня сайта без начального слеша
     * @param string|array $function_name Имя функции
     * @param array $params Параметры, передаваемые в функцию
     * @return mixed
     */
    public static function includeAndCall($file, $function_name, $params = []) {

        if (!self::includeFile($file)) {
            return false;
        }

        if (!function_exists($function_name)) {
            return false;
        }

        return call_user_func_array($function_name, $params);
    }

    /**
     * Загружает внешнюю библиотеку из папки /system/libs
     *
     * @param string $library Название библиотеки в /system/libs (без расширения)
     * @param string $class Название загружаемого класса (для предотвращения повторной загрузки)
     */
     public static function loadLib($library, $class = false) {

        if ($class && class_exists($class, false)) {
            return true;
        }

        $lib_file = cmsConfig::get('root_path') . 'system/libs/' . $library . '.php';

        if (!is_readable($lib_file)) {
            self::error(ERR_LIBRARY_NOT_FOUND . ': ' . $library);
        }

        include_once $lib_file;

        return true;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет существование модели
     *
     * @param string $controller Название контроллера
     * @return bool
     */
    public static function isModelExists($controller) {

        $model_file = cmsConfig::get('root_path') . 'system/controllers/' . $controller . '/model.php';

        return file_exists($model_file);
    }

    /**
     * Возвращает объект модели из указанного файла (без расширения)
     *
     * @param string $controller Имя контроллера модели
     * @param string $delimitter Разделитель слов в названии класса
     * @param bool $display_error Завершать с ошибокой, если не найден
     * @return ?cmsModel
     */
    public static function getModel($controller, $delimitter = '_', $display_error = true) {

        if (is_array($controller)) {
            $controller = end($controller);
        }

        $model_class = 'model' . string_to_camel($delimitter, $controller);

        try {
            $model = new $model_class();
        } catch (Exception $e) {
            if ($display_error) {
                self::error(ERR_MODEL_NOT_FOUND . ': ' . $e->getMessage());
            }
        } finally {
            return isset($model) ? $model : null;
        }
    }

    /**
     * @deprecated
     *
     * С версии 2.14.0 работает в autoLoad
     */
    public static function includeModel($controllers, $quiet = false) {
        return true;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет существования контроллера
     *
     * @param string $controller_name Имя контроллера
     * @return bool
     */
    public static function isControllerExists($controller_name) {
        return is_dir(cmsConfig::get('root_path') . 'system/controllers/' . $controller_name);
    }

    /**
     * Создает и возвращает объект контроллера
     *
     * @param string $controller_name Имя контроллера
     * @param ?cmsRequest $request Объект cmsRequest
     * @param bool $display_error Завершать с ошибокой, если не найден
     * @return void|cmsController
     */
    public static function getController($controller_name, $request = null, $display_error = true) {

        if (!$request) {
            $request = new cmsRequest([], cmsRequest::CTX_INTERNAL);
        }

        $config = cmsConfig::getInstance();

        $ctrl_file = $config->root_path . 'system/controllers/' . $controller_name . '/frontend.php';

        if (!class_exists($controller_name, false)) {
            if (!is_readable($ctrl_file)) {
                if (!$display_error) {
                    return null;
                }
                if (!$request->isInternal()) {
                    return self::error404();
                }
                return self::error(ERR_COMPONENT_NOT_FOUND . ': ' . str_replace($config->root_path, '', $ctrl_file));
            }
            include_once($ctrl_file);
        }

        $custom_file = $config->root_path . 'system/controllers/' . $controller_name . '/custom.php';

        if (!is_readable($custom_file)) {
            $controller_class = $controller_name;
        } else {
            $controller_class = $controller_name . '_custom';
            if (!class_exists($controller_class, false)) {
                include_once($custom_file);
            }
        }

        if (!class_exists($controller_class, false)) {
            if (!$display_error) {
                return null;
            }
            return self::error(ERR_COMPONENT_NOT_FOUND . ': ' . str_replace($config->root_path, '', $ctrl_file));
        }

        return new $controller_class($request);
    }

    /**
     * Создаёт, запоминает и возвращает объект контроллера
     *
     * @param string $controller_name Имя контроллера
     * @param ?cmsRequest $request Объект cmsRequest
     * @return cmsController
     */
    public static function getControllerInstance($controller_name, $request = null) {

        if (!isset(self::$controllers_instance[$controller_name])) {

            self::$controllers_instance[$controller_name] = self::getController($controller_name, $request);
        }

        return self::$controllers_instance[$controller_name];
    }

    /**
     * Возвращает имя контроллера по псевдониму
     *
     * @param string $controller_alias Псевдоним контроллера
     * @return string|false
     */
    public static function getControllerNameByAlias($controller_alias) {

        $config_mapping      = cmsConfig::getControllersMapping();
        $controllers_mapping = cmsController::getControllersMapping();

        $mapping = array_merge($controllers_mapping, $config_mapping);
        if (!$mapping) {
            return false;
        }

        return array_search($controller_alias, $mapping, true);
    }

    /**
     * Возвращает псевдоним контроллера по его имени
     *
     * @param string $controller_name Имя контроллера
     * @return bool|string
     */
    public static function getControllerAliasByName($controller_name) {

        if (!$controller_name) {
            return false;
        }

        $mapping = cmsConfig::getControllersMapping();

        if (!empty($mapping[$controller_name])) {
            return $mapping[$controller_name];
        }

        $cmapping = cmsController::getControllersMapping();

        if (!empty($cmapping[$controller_name])) {
            return $cmapping[$controller_name];
        }

        return false;
    }

    /**
     * Возвращает массив хуков контроллеров
     *
     * @param boolean $is_enabled Только включенные хуки
     * @return array
     */
    public static function getControllersEvents($is_enabled = true) {

        $events = [];

        $db = cmsDatabase::getInstance();

        if (!$db->ready()) {
            return $events;
        }

        if ($is_enabled) {
            $controllers_events = $db->getRows('events', '`is_enabled` = 1', '*', 'ordering ASC', true);
        } else {
            $controllers_events = $db->getRows('events', '1', '*', 'ordering ASC', true);
        }

        if ($controllers_events) {
            foreach ($controllers_events as $event) {
                // для сохранения сортировки + совместимость
                if (!isset($events[$event['listener']][$event['ordering']])) {
                    $events[$event['listener']][$event['ordering']] = $event['event'];
                } else {
                    // если вдруг по какой-то причине порядок одинаковый
                    $events[$event['listener']][(int)($event['ordering'] . '00' . $event['id'])] = $event['event'];
                }
            }
        }

        return $events;
    }

//============================================================================//
//============================================================================//

    /**
     * Инициализирует локализацию
     */
    public function initLanguage() {

        // Загружаем базовую локализацию
        self::loadLanguage();

        // устанавливаем локаль языка
        if (function_exists('lang_setlocale')) {
            lang_setlocale();
        }

        // устанавливаем локаль MySQL
        $this->db->setLcMessages();
    }

    /**
     * Возвращает префикс текущего языка
     * Язык по умолчанию имеет пустой префикс
     *
     * @return string
     */
    public static function getLanguageHrefPrefix() {
        return self::$language_href_prefix;
    }

    /**
     * Возвращает текущий язык сайта
     *
     * @return string
     */
    public static function getLanguageName() {
        return self::$language;
    }

    /**
     * Возвращает язык браузера
     * @return string
     */
    public static function getBrowserLanguage() {
        return self::$browser_language;
    }

    /**
     * Меняет текущий язык
     *
     * @param string $new_lang
     */
    public static function changeLanguage($new_lang) {
        self::$language = $new_lang;
    }

    /**
     * Подключает указанный языковой файл.
     * Если файл не указан, то подключаются все PHP-файлы из папки текущего языка
     *
     * @param ?string $file Относительный путь к файлу
     * @param string $default Язык по умолчанию, если в текущем не найдено
     * @return boolean|array
     */
    public static function loadLanguage($file = null, $default = 'ru') {

        $lang_dir = 'system/languages/' . self::$language;

        if (!$file) {

            // Если файл не указан, то подключаем все php-файлы
            // из папки с текущим языком
            return self::getFilesList($lang_dir, '*.php', true, true);
        } else {

            // Если файл указан, то подключаем только его
            $lang_file = $lang_dir . '/' . $file . '.php';

            $result = self::includeFile($lang_file);

            if (!$result && $default !== self::$language) {
                $result = self::includeFile('system/languages/' . $default . '/' . $file . '.php');
            }

            return $result;
        }
    }

    /**
     * Возвращает содержимое текстового файла из директории с текущим языком
     *
     * @param string $file Имя файла без расширения
     * @return string
     */
    public static function getLanguageTextFile($file) {

        $lang_file_path = self::getLanguageTextFileExistsPath($file);

        return $lang_file_path ? file_get_contents($lang_file_path) : '';
    }

    /**
     * Возвращает путь к текстовому файлу из директории с текущим языком
     *
     * @param string $file Имя файла без расширения
     * @return bool|string
     */
    public static function getLanguageTextFileExistsPath($file) {

        $lang_path = cmsConfig::get('root_path') . 'system/languages/' . self::$language. '/' . $file . '.txt';

        return !file_exists($lang_path) ? false : $lang_path;
    }

    /**
     * Проверяет наличие текстового файла в директории с текущим языком
     *
     * @param string $file Имя файла без расширения
     * @return bool
     */
    public static function isLanguageTextFileExists($file) {
        return self::getLanguageTextFileExistsPath($file) ? true : false;
    }

    /**
     * Подключает языковой файл контроллера
     * @param string $controller_name Имя контроллера
     * @return boolean
     */
    public static function loadControllerLanguage($controller_name) {
        return self::loadLanguage("controllers/{$controller_name}/{$controller_name}");
    }

    /**
     * Подключает языковой файл поля
     * @param string $field_name
     * @return boolean
     */
    public static function loadFieldLanguage($field_name) {
        return self::loadLanguage("fields/{$field_name}/{$field_name}");
    }

    /**
     * Подключает языковой файл виджета
     * И его контроллера, если есть
     *
     * @param string $widget_name Имя виджета
     * @param ?string $controller_name Контроллер виджета
     * @return boolean
     */
    public static function loadWidgetLanguage($widget_name, $controller_name = null) {

        $path = self::getWidgetPath($widget_name, $controller_name);

        if($controller_name){
            self::loadControllerLanguage($controller_name);
        }

        return self::loadLanguage($path);
    }

    /**
     * Подключает языковой файл шаблона
     *
     * @param string|array $template_names Имя шаблона или массив цепочки шаблонов
     * @return boolean
     */
    public static function loadTemplateLanguage($template_names) {

        if (!is_array($template_names)) {
            $template_names = [$template_names];
        }

        $result = false;

        foreach ($template_names as $template_name) {
            $result = self::loadLanguage("templates/{$template_name}");
            if ($result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Подключает языковые файлы всех контроллеров
     *
     * @param string $file
     * @return bool
     */
    public static function loadAllControllersLanguages() {

        $controllers = self::getDirsList('system/controllers');

        foreach ($controllers as $controller_name) {
            self::loadControllerLanguage($controller_name);
        }
    }

//============================================================================//
//============================================================================//

    /**
     * Определяет шаблон HTTP ответа
     *
     * @return void
     */
    private function detectHttpTemplate() {

        if (cmsUser::isAdmin()) {
            $dev_template = cmsConfig::get('template_dev');
            if ($dev_template) {

                $allow_ips = cmsConfig::get('template_dev_allow_ips');

                if (!$allow_ips || string_in_mask_list(cmsUser::getIp(), $allow_ips)) {

                    cmsConfig::getInstance()->set('http_template', $dev_template);
                    return;
                }
            }
        }

        $device_type = cmsRequest::getDeviceType();

        // шаблон в зависимости от девайса
        if ($device_type !== 'desktop') {
            $device_template = cmsConfig::get('template_' . $device_type);
            if ($device_template) {
                cmsConfig::getInstance()->set('http_template', $device_template);
            }
        }

        return;
    }

    /**
     * Определяет язык браузера
     *
     * @return void
     */
    private function detectBrowserLanguage() {

        $accept_language = (string) $this->request->getHeader('ACCEPT_LANGUAGE', '');

        if (!$accept_language) {
            return;
        }

        $config = cmsConfig::getInstance();

        if (empty($config->is_user_change_lang) ||
                empty($config->is_browser_auto_lang)) {
            return;
        }

        $user_lang = strtolower(substr($accept_language, 0, 2));

        if (preg_match('/^[a-z]{2}$/', $user_lang)) {

            self::$browser_language = $user_lang;
        }

        return;
    }

    /**
     * Определяет язык, контроллер, действие
     * и параметры для запуска по полученному URI
     *
     * @param string $uri
     * @return void
     */
    public function route(string $uri) {

        $config = cmsConfig::getInstance();

        $uri = trim(urldecode($uri));
        $uri = mb_substr($uri, mb_strlen($config->root));

        if (!$uri) {
            return;
        }

        // если в URL присутствует знак вопроса, значит в нём
        // есть GET-параметры которые нужно распарсить
        if (($pos_que = mb_strpos($uri, '?')) !== false) {

            parse_str(mb_substr($uri, $pos_que + 1), $this->uri_query);
            $uri = mb_substr($uri, 0, $pos_que);
        }

        // Разделение URI на сегменты
        $segments = explode('/', $uri);

        // Обработка смены языка
        if (!empty($config->is_user_change_lang)) {
            // язык может быть только двухбуквенный, определяем его по первому сегменту
            // язык по умолчанию без префиксов, дубли нам не нужны
            if (!empty($segments[0]) && preg_match('/^[a-z]{2}$/i', $segments[0])) {

                $lang_path = $config->root_path . 'system/languages/' . $segments[0] . '/';

                if (is_dir($lang_path) && $segments[0] !== $config->language) {

                    self::$language = self::$language_href_prefix = array_shift($segments);

                    $uri = mb_substr($uri, 3);

                    $config->findLocalizedOn();
                }
            }
        }

        // Установка свойств URI
        $this->uri          = $this->uri_before_remap = $uri;
        $this->uri_absolute = $config->root . $uri;

        // Определяем контроллер из первого сегмента
        $this->uri_controller = $segments[0] ?? '';
        // Определяем действие из второго сегмента
        $this->uri_action     = $segments[1] ?? '';
        // Определяем параметры действия из всех остальных сегментов
        $this->uri_params     = array_slice($segments, 2);

        return;
    }

    /**
     * Возвращает массив данных, полученных из URI
     * Имя контроллера, действие и параметры действия
     *
     * @return array
     */
    public function getUriData() {
        return [
            'controller' => $this->uri_controller,
            'action'     => $this->uri_action,
            'params'     => $this->uri_params
        ];
    }

    /**
     * Определяет контроллер для запуска по текущему URI
     *
     * @return $this
     */
    public function defineController() {

        $key = $this->uri_controller . ':' . $this->uri_action;

        if (isset($this->defined_controllers[$key])) {
            return $this;
        }

        $this->defined_controllers[$key] = true;

        $config = cmsConfig::getInstance();

        // контроллер и экшен по умолчанию
        if (!$this->uri_controller) {
            $this->uri_controller = $config->ct_autoload;
        }
        if (!$this->uri_action) {
            $this->uri_action = 'index';
        }

        // проверяем ремаппинг контроллера
        $remap_to = self::getControllerNameByAlias($this->uri_controller);
        if ($remap_to) {
            // в uri также меняем
            if ($this->uri) {
                $seg                = explode('/', $this->uri);
                $seg[0]             = $remap_to;
                $this->uri          = implode('/', $seg);
                $this->uri_absolute = str_replace($this->uri_before_remap, $this->uri, $this->uri_absolute);
            }
            $this->uri_controller_before_remap = $this->uri_controller;
            $this->uri_controller              = $remap_to;
        }

        if (!self::isControllerExists($this->uri_controller)) {

            if ($this->uri_action !== 'index') {
                array_unshift($this->uri_params, $this->uri_action);
            }

            $this->uri_action     = $this->uri_controller;
            $this->uri_controller = $config->ct_default;
        }

        $this->controller = $this->uri_controller;

        return $this;
    }

    /**
     * Запускает контроллер и выбранное действие контроллера
     */
    public function runController() {

        $this->defineController();

        if (!preg_match('/^[a-z]{1}[a-z0-9_]*$/', $this->controller)) {
            return self::error404();
        }

        // загружаем контроллер
        $controller = self::getController($this->controller, $this->request);

        // контроллер включен?
        if (!$controller->isEnabled()) {
            return self::error404();
        }

        // редирект 301, если настроен ремап
        if (!$this->uri_controller_before_remap && $slug = $controller->hasSlug()) {

            // если контроллер запрещает редирект, то 404
            if ($controller->disallow_mapping_redirect) {
                return self::error404();
            }

            return $controller->redirectTo($slug, ($this->uri_action === 'index' ? '' : $this->uri_action), $this->uri_params, $this->uri_query, 301);
        }

        // запускаем действие
        $controller->runAction($this->uri_action, $this->uri_params);
    }

    /**
     * Определяет и загружает страницы, которые определены для текущего uri
     * @return \cmsCore
     */
    public function loadMatchedPages() {

        if ($this->matched_pages !== null) {
            return $this;
        }

        if ($this->widgets_pages === null) {
            $this->widgets_pages = self::getModel('widgets')->getPages();
        }

        $this->matched_pages = $this->detectMatchedWidgetPages($this->widgets_pages);

        return $this;
    }

    /**
     * Возвращает все загруженные страницы (cms_widgets_pages)
     * @return array
     */
    public function getWidgetsPages() {
        return $this->widgets_pages;
    }

    /**
     * Возвращает массив id страниц, которые определены для текущего uri
     * @return array
     */
    public function getMatchedPagesIds() {
        return $this->matched_pages ? array_keys($this->matched_pages) : [];
    }

    /**
     * Возвращает массив страниц, которые определены для текущего uri
     * @return array
     */
    public function getMatchedPages() {
        return $this->matched_pages;
    }

    /**
     * Устанавливает массив страниц для текущего uri
     * @param array $matched_pages
     * @return $this
     */
    public function setMatchedPages($matched_pages) {
        $this->matched_pages = $matched_pages;
        return $this;
    }

    /**
     * Запускает все виджеты, привязанные к текущей странице
     *
     * @return $this
     */
    public function runWidgets() {

        $template = cmsTemplate::getInstance();

        if ($template->widgets_rendered) {
            return $this;
        }

        $template->widgets_rendered = true;

        $controllers_without_widgets = cmsConfig::get('controllers_without_widgets');

        if ($controllers_without_widgets && in_array($this->controller, $controllers_without_widgets)) {
            return $this;
        }

        $matched_pages = $this->loadMatchedPages()->getMatchedPages();
        if (!$matched_pages) {
            return $this;
        }

        $widgets_list = self::getModel('widgets')->getWidgetsForPages(array_keys($matched_pages), $template->getName());

        $full_uri = $this->uri . ($this->uri_query ? '?' . http_build_query($this->uri_query) : '');

        // Для исключения главной страницы
        if (!$full_uri) {
            $full_uri = '/';
        }
        // Для 404 страниц единый URI
        if ($this->response->getStatusCode() === 404) {
            $full_uri = '404';
        }

        $device_type = cmsRequest::getDeviceType();
        $layout      = $template->getLayout();
        $user        = cmsUser::getInstance();

        if ($user->is_admin) {
            self::loadControllerLanguage('admin');
        }

        foreach ($widgets_list as $widget) {

            // Проверяем отрицательные маски виджета
            if (!empty($widget['url_mask_not'])) {
                if (string_matches_mask_list($widget['url_mask_not'], $full_uri)) {
                    continue;
                }
            }

            // не выводим виджеты контроллеров, которые отключены
            if (!empty($widget['controller']) && !cmsController::enabled($widget['controller'])) {
                continue;
            }

            // проверяем доступ для виджетов
            if (!$user->isInGroups($widget['groups_view'])) {
                continue;
            }
            if (!empty($widget['groups_hide']) && $user->isInGroups($widget['groups_hide']) && !$user->is_admin) {
                continue;
            }

            // проверяем для каких устройств показывать
            if ($widget['device_types'] && !in_array($device_type, $widget['device_types'])) {
                continue;
            }

            // проверяем для каких макетов показывать
            if ($widget['template_layouts'] && !in_array($layout, $widget['template_layouts'])) {
                continue;
            }

            // проверяем для каких языков показывать
            if ($widget['languages'] && !in_array(self::getLanguageName(), $widget['languages'])) {
                continue;
            }

            cmsDebugging::pointStart('widgets');

            $this->runWidget($widget);

            cmsDebugging::pointProcess('widgets', function () use ($widget) {
                return [
                    'data'    => $widget['title'] . ' => /system/' . self::getWidgetPath($widget['name'], $widget['controller']) . '/widget.php',
                    'context' => [
                        'target'  => $widget['controller'],
                        'subject' => $widget['name']
                    ]
                ];
            }, 0);
        }

        return $this;
    }

    /**
     * Возвращает относительный путь к виджету
     *
     * @param string $widget_name Имя виджета
     * @param ?string $controller_name Имя контроллера
     * @return string
     */
    public static function getWidgetPath($widget_name, $controller_name = null) {

        if ($controller_name) {
            $path = "controllers/{$controller_name}/widgets/{$widget_name}";
        } else {
            $path = "widgets/{$widget_name}";
        }

        return $path;
    }

    /**
     * Возвращает объект виджета
     *
     * @param array $widget Массив данных виджета
     * @return cmsWidget
     */
    public static function getWidgetObject($widget) {

        $file = 'system/' . self::getWidgetPath($widget['name'], $widget['controller']) . '/widget.php';

        $class = 'widget' .
                ($widget['controller'] ? string_to_camel('_', $widget['controller']) : '') .
                string_to_camel('_', $widget['name']);

        if (!class_exists($class, false)) {

            self::includeFile($file);
        }

        return new $class($widget);
    }

    /**
     * Запускает выполнение виджета
     * Результат (готовый HTML) сохраняется в cmsTemplate
     *
     * @param array $widget Массив данных виджета
     * @return cmsTemplate
     */
    public function runWidget($widget) {

        $result = false;

        $widget_object = self::getWidgetObject($widget);

        $cache_key = 'widgets' . $widget['id'];
        $cache     = cmsCache::getInstance();

        if ($widget_object->isCacheable()) {

            $result = $cache->get($cache_key);
        }

        if ($result === false) {

            $result = call_user_func_array([$widget_object, 'run'], []);

            if ($result !== false) {
                // Отдельно кешируем имя шаблона виджета, заголовок и враппер, поскольку они могли быть
                // изменены внутри виджета, а в кеш у нас попадает только тот массив
                // который возвращается кодом виджета (без самих свойств $widget_object)
                $result['_wd_template'] = $widget_object->getTemplate();
                $result['_wd_title']    = $widget_object->title;
                $result['_wd_wrapper']  = $widget_object->getWrapper();
            }

            if ($widget_object->isCacheable()) {
                $cache->set($cache_key, $result);
            }
        }

        if ($result === false) {
            return false;
        }

        if (isset($result['_wd_template'])) {
            $widget_object->setTemplate($result['_wd_template']);
        }
        if (isset($result['_wd_title'])) {
            $widget_object->title = $result['_wd_title'];
        }
        if (isset($result['_wd_wrapper'])) {
            $widget_object->setWrapper($result['_wd_wrapper']);
        }

        return cmsTemplate::getInstance()->renderWidget($widget_object, $result);
    }

    /**
     * Определяет какие из списка страниц виджетов
     * совпадают по маске с текущей страницей
     *
     * @param array $pages
     * @param ?string $uri
     * @return array
     */
    public function detectMatchedWidgetPages($pages, $uri = null) {

        if ($uri === null) {
            $uri       = $this->uri;
            $uri_query = $this->uri_query;
        }

        if ($uri === '') {
            return [0, 1];
        }

        $matched_pages = [];

        $_full_uri = $uri . (!empty($uri_query) ? '?' . http_build_query($uri_query) : '');

        //
        // Перебираем все точки привязок и проверяем совпадение
        // маски URL с текущим URL
        //
        foreach ($pages as $page) {

            if (!$page['url_mask'] && $page['id']) {
                continue;
            }

            $is_mask_match = !$page['id'] || string_matches_mask_list($page['url_mask'], $uri);
            $is_stop_match = string_matches_mask_list($page['url_mask_not'], $_full_uri);

            if ($is_mask_match && !$is_stop_match) {
                $matched_pages[$page['id']] = $page;
            }
        }

        return $matched_pages;
    }

//============================================================================//
//============================================================================//

    /**
     * Показывает сообщение об ошибке 503 и завершает работу
     *
     * @param string $message
     * @param string $details
     * @return void
     */
    public static function error($message, $details = '') {

        $is_debug = cmsConfig::get('debug');

        self::getInstance()->displayError(
            503,
            cmsTemplate::getInstance()->getRenderedAsset('errors/error', [
                'is_debug' => $is_debug,
                'message'  => !$is_debug ? LANG_ERROR_503 : $message,
                'details'  => !$is_debug ? LANG_ERROR_503_HINT : $details
            ], false, true)
        );
    }

    /**
     * Показывает сообщение об ошибке 403 и завершает работу
     *
     * @param string $message Текстовое сообщение к ошибке
     * @param boolean $show_login_link Показывать ссылку на авторизацию
     * @return void
     */
    public static function errorForbidden($message = '', $show_login_link = false) {

        self::getInstance()->displayError(
            403,
            cmsTemplate::getInstance()->getRenderedAsset('errors/forbidden', [
                'message'         => $message,
                'show_login_link' => $show_login_link
            ], false, true),
            'error_403'
        );
    }

    /**
     * Показывает сообщение об ошибке 404 и завершает работу
     *
     * @return void
     */
    public static function error404() {

        $self = self::getInstance();

        if (!cmsConfig::get('is_404_layout')) {

            return $self->displayError(
                404,
                cmsTemplate::getInstance()->getRenderedAsset('errors/notfound', [], false, true),
                'error_404'
            );
        }

        cmsEventsManager::hook('error_404', $self->uri);

        $self->response->setStatusCode(404);

        self::getController('error404', $self->request)->runAction('index');

        $self->runWidgets();

        $self->response->setContent(cmsTemplate::getInstance()->getRenderedPage())->sendAndExit();
    }

    /**
     * Показывает сообщение о том что сайт отключен и завершает работу
     *
     * @return void
     */
    public static function errorMaintenance() {

        self::getInstance()->displayError(
            503,
            cmsTemplate::getInstance()->getRenderedAsset('errors/offline', [
                'reason' => cmsConfig::get('off_reason')
            ], false, true)
        );
    }

    /**
     * Выводит ошибку с соответствующим кодом ответа
     * И завершает работу
     *
     * @param int $http_code HTTP код
     * @param string $html Тело страницы
     * @param ?string $hook_name Имя хука
     * @return void
     */
    public function displayError(int $http_code, string $html, $hook_name = null) {

        if ($hook_name) {

            $result = cmsEventsManager::hook($hook_name, $this->uri);

            if ($result === true) {
                return;
            }
        }

        if (ob_get_length()) { ob_end_clean(); }

        $this->response->
                setStatusCode($http_code)->
                setContent($html)->
                sendAndExit();
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает массив со списком всех шаблонов
     *
     * @return array
     */
    public static function getTemplates() {

        if (self::$templates !== null) {
            return self::$templates;
        }

        if (cmsTemplate::TEMPLATE_BASE_PATH) {
            return self::$templates = self::getDirsList(cmsTemplate::TEMPLATE_BASE_PATH);
        }

        $root_path = cmsConfig::get('root_path');
        $all_dirs  = self::getDirsList('');
        $result    = [];

        foreach ($all_dirs as $dir) {
            // В папке шаблона в обязательном порядке должны быть как минимум эти файлы
            if (file_exists($root_path . $dir . '/main.tpl.php') &&
                    (file_exists($root_path . $dir . '/scheme.html') || file_exists($root_path . $dir . '/scheme.php'))) {
                $result[] = $dir;
            }
        }

        return self::$templates = $result;
    }

    /**
     * Возвращает массив со списком всех языков
     *
     * @return array
     */
    public static function getLanguages() {

        $default_lang = cmsConfig::get('language');

        $langs = self::getDirsList('system/languages', true);

        $current_lang_key = array_search(self::$language, $langs);

        if ($current_lang_key !== 0) {

            list($langs[0], $langs[$current_lang_key]) = [$langs[$current_lang_key], $langs[0]];
        }

        if ($default_lang !== self::$language) {

            $default_lang_key = array_search($default_lang, $langs);

            if ($default_lang_key !== 1) {

                list($langs[1], $langs[$default_lang_key]) = [$langs[$default_lang_key], $langs[1]];
            }
        }

        return $langs;
    }

    /**
     * Возвращает массив со списком всех визуальных редакторов
     *
     * @return array
     */
    public static function getWysiwygs() {
        return self::getDirsList('wysiwyg');
    }

    /**
     * Возвращает список директорий внутри указанной
     *
     * @param string $root_dir Путь к директории, относительно корня установки CMS
     * @param boolean $asc_sort Сортировать по алфавиту, по умолчанию false
     * @return array
     */
    public static function getDirsList($root_dir, $asc_sort = false) {
        return files_get_dirs_list(cmsConfig::get('root_path') . $root_dir, $asc_sort);
    }

    /**
     * Возвращает список файлов из указанной директории по нужной маске
     *
     * @param string $root_dir Директория
     * @param string $pattern Маска файлов
     * @param boolean $is_strip_ext Отрезать расширения?
     * @param boolean $is_include Подключать каждый файл?
     * @return array
     */
    public static function getFilesList($root_dir, $pattern = '*.*', $is_strip_ext = false, $is_include = false) {

        $directory = cmsConfig::get('root_path') . $root_dir;
        $pattern   = $directory . '/' . $pattern;

        $list = [];

        $files = glob($pattern);

        if (!$files) { return $list; }

        foreach ($files as $file) {

            if ($is_include && !isset(self::$includedFiles[$file])) {
                include_once $file;
                self::$includedFiles[$file] = true;
            }

            $file = basename($file);

            if ($is_strip_ext) {
                $file = pathinfo($file, PATHINFO_FILENAME);
            }

            $list[] = $file;
        }

        return $list;
    }

    /**
     * Устанавливает соединение с БД
     */
    public function connectDB(){
        $this->db = cmsDatabase::getInstance();
    }

}

/**
 * В случае, если отладка отключена, не загружаем файл класса
 */
if(!class_exists('cmsDebugging', false)){
    class cmsDebugging {
        public static function __callStatic($name, $arguments) {}
    }
}
