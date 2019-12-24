<?php

class cmsCore {

    private static $instance;

	public $uri            = '';
	public $uri_before_remap = '';
    public $uri_absolute   = '';
    public $uri_controller = '';
    public $uri_controller_before_remap = '';
    public $uri_action     = '';
    public $uri_params     = array();
    public $uri_query      = array();
    private $matched_pages = null;
    private $widgets_pages = null;

    private static $language = 'ru';
    private static $language_href_prefix = '';
    private static $core_version = null;
    private static $controllers_instance = array();
    private static $templates = null;

    public $controller = '';
    private $defined_controllers = array();

	public $link;
	public $request;

    public $db;

    private static $includedFiles = array();

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){

        $this->request = new cmsRequest($_REQUEST);

        self::detectLanguage();

    }

    /*
     * deprecated, use cmsDebugging
     */
    private static $start_time;
    public static function startTimer() {
        self::$start_time = microtime(true);
    }
    public static function getTime() {
        return microtime(true) - self::$start_time;
    }

//============================================================================//
//============================================================================//

    private static function detectLanguage() {

        $config = cmsConfig::getInstance();

        self::$language = $config->language;

        if(!empty($_SERVER['REQUEST_URI']) && !empty($config->is_user_change_lang)){

            $segments = explode('/', mb_substr($_SERVER['REQUEST_URI'], mb_strlen($config->root)));

            // язык может быть только двухбуквенный, определяем его по первому сегменту
            if (!empty($segments[0]) && preg_match('/^[a-z]{2}$/i', $segments[0])) {
                if(is_dir($config->root_path.'system/languages/'.$segments[0].'/')){
                    // язык по умолчанию без префиксов, дубли нам не нужны
                    if($segments[0] != $config->language){

                        // включаем для моделей поддержку
                        cmsModel::globalLocalizedOn();

                        self::$language = self::$language_href_prefix = $segments[0]; unset($segments[0]);

                        $_SERVER['REQUEST_URI'] = $config->root.implode('/', $segments);

                    }
                }
            }

        }

    }

    public static function getLanguageHrefPrefix() {
        return self::$language_href_prefix;
    }

    public static function getLanguageName() {
        return self::$language;
    }

    public static function changeLanguage($new_lang){
        self::$language = $new_lang;
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает информацию о версии CMS
     * в виде строки
     * @param boolean $show_date Показывать дату версии
     * @return string
     */
    public static function getVersion($show_date = false){

        $version = self::getVersionArray();

        if (!$show_date && isset($version['date'])) { unset($version['date']); }

        return $version['version'].($show_date ? ' '.LANG_FROM.' '.$version['date'] : '');

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
    public static function getVersionArray(){

        if(self::$core_version === null){

            $file = cmsConfig::get('root_path') . 'system/config/version.ini';
            if (!is_readable($file)){ die('system/config/version.ini not found'); }

            $version = parse_ini_file($file);

            self::$core_version = array(
                'date'    => $version['date'],
                'version' => $version['major'] .'.'. $version['minor'] .'.'. $version['build'],
                'raw'     => $version
            );

        }

        return self::$core_version;

    }

//============================================================================//
//============================================================================//

    public static function isWritable($path, $is_force_mkdir=true) {

        if ($is_force_mkdir && (!file_exists($path))) { @mkdir($path); }

        return (is_writable($path));

    }

//============================================================================//
//============================================================================//

    /**
     * Подключает файл
     * @param string $file Путь относительно корня сайта без начального слеша
     * @return boolean
     */
    public static function includeFile($file) {

        $file = cmsConfig::get('root_path') . $file;

        if (isset(self::$includedFiles[$file])){
            return self::$includedFiles[$file];
        }

        if (!is_readable($file)){
            self::$includedFiles[$file] = false;
            return false;
        }

        $result = include_once $file;

        if (is_null($result)) { $result = true; }

        self::$includedFiles[$file] = $result;

        return $result;

    }

    public static function requireFile($file) {

        $file = cmsConfig::get('root_path') . $file;

        if (!is_readable($file)){ return false; }

        $result = require $file;

        if (is_null($result)) { $result = true; }

        return $result;

    }

    public static function includeAndCall($file, $function_name, $params=array()){

        if (!self::includeFile($file)){ return false; }

        if (!function_exists($function_name)){ return false; }

        return call_user_func_array($function_name, $params);

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает внешнюю библиотеку из папки /system/libs
     *
     * @param string $library Название библиотеки в /system/libs (без расширения)
     * @param string $class Название загружаемого класса (для предотвращения повторной загрузки)
     */
     public static function loadLib($library, $class=false){

        if ($class && class_exists($class, false)){ return true; }

        $lib_file = cmsConfig::get('root_path').'system/libs/'.$library.'.php';

        if (!is_readable($lib_file)){ self::error(ERR_LIBRARY_NOT_FOUND . ': '. $library); }

        include_once $lib_file;

        return true;

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает класс ядра из папки /system/core
     * @param string $class
     */
    public static function loadCoreClass($class){

        $class_file = cmsConfig::get('root_path') . 'system/core/'.$class.'.class.php';

        if (!is_readable($class_file)){
            self::error(ERR_CLASS_NOT_FOUND . ': '. $class);
        }

        include_once $class_file;

        return true;

    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет существование модели
     * @param str $controller Название контроллера
     * @return bool
     */
    public static function isModelExists($controller){

        $model_file = cmsConfig::get('root_path').'system/controllers/'.$controller.'/model.php';

        return file_exists($model_file);

    }

    /**
     * Возвращает объект модели из указанного файла (без расширения)
     * @param string $controller Контроллер модели
     * @param string $delimitter Разделитель слов в названии класса
     */
    public static function getModel($controller, $delimitter='_'){

        if(is_array($controller)){

            self::includeModel($controller);

            $controller = end($controller);

        }

        $model_class = 'model' . string_to_camel($delimitter, $controller);

        if (!class_exists($model_class, false)) {
            self::includeModel($controller);
        }

        return new $model_class();

    }

    /**
     * Инклюдит файл модели контроллера (контроллеров)
     * @param string $controllers Контроллер модели или массив контроллеров
     * @param boolean $quiet
     * @return boolean
     */
    public static function includeModel($controllers, $quiet = false) {

        if(!is_array($controllers)){
            $controllers = array($controllers);
        }

        foreach ($controllers as $controller) {

            $model_file = cmsConfig::get('root_path').'system/controllers/'.$controller.'/model.php';

            if (is_readable($model_file)){

                include_once($model_file);

            } else {

                if($quiet){ return false; }

                return self::error(ERR_MODEL_NOT_FOUND . ': '. str_replace(cmsConfig::get('root_path'), '', $model_file));

            }

        }

        return true;

    }


//============================================================================//
//============================================================================//

    /**
     * Проверяет существования контроллера
     * @param string $controller_name
     * @return bool
     */
    public static function isControllerExists($controller_name){

        return is_dir(cmsConfig::get('root_path').'system/controllers/'.$controller_name);

    }

    /**
     * Создает и возвращает объект контроллера
     * @param string $controller_name
     * @param cmsRequest $request
     * @return controller_class
     */
    public static function getController($controller_name, $request = null){

        $config = cmsConfig::getInstance();

        $ctrl_file = $config->root_path . 'system/controllers/'.$controller_name.'/frontend.php';

        if (!class_exists($controller_name, false)) {
            if(!is_readable($ctrl_file)){
                return self::error404();
            }
            include_once($ctrl_file);
        }

        $custom_file = $config->root_path . 'system/controllers/'.$controller_name.'/custom.php';

        if(!is_readable($custom_file)){
            $controller_class = $controller_name;
        } else {
            $controller_class = $controller_name . '_custom';
            if (!class_exists($controller_class, false)){
                include_once($custom_file);
            }
        }

        if (!class_exists($controller_class, false)) {
            return self::error(ERR_COMPONENT_NOT_FOUND . ': '. str_replace($config->root_path, '', $ctrl_file));
        }

        if (!$request) { $request = new cmsRequest(array(), cmsRequest::CTX_INTERNAL); }

        return new $controller_class($request);

    }

    public static function getControllerInstance($controller_name, $request=null){

        if(!isset(self::$controllers_instance[$controller_name])){

            self::$controllers_instance[$controller_name] = self::getController($controller_name, $request);

        }

        return self::$controllers_instance[$controller_name];

    }

    public static function getControllerNameByAlias($controller_alias){

        $config_mapping      = cmsConfig::getControllersMapping();
        $controllers_mapping = cmsController::getControllersMapping();

        $mapping = array_merge($controllers_mapping, $config_mapping);
        if (!$mapping) { return false; }

        foreach($mapping as $name=>$alias){
            if ($alias == $controller_alias) { return $name; }
        }

        return false;

    }

    public static function getControllerAliasByName($controller_name){

        if(!$controller_name){ return false; }

        $mapping = cmsConfig::getControllersMapping();

        if (!empty($mapping[$controller_name])){ return $mapping[$controller_name]; }

        $cmapping = cmsController::getControllersMapping();

        if (!empty($cmapping[$controller_name])){ return $cmapping[$controller_name]; }

        return false;

    }

    /**
     * Возвращает массив хуков контроллеров
     * @param boolean $is_debug Если передано true, то массив формируется из файлов манифестов, иначе - из БД
     * @param boolean $is_enabled
     * @return array
     */
    public static function getControllersManifests($is_debug = false, $is_enabled = true){

        if ($is_debug){
            return self::getManifestsEvents();
        }

        $events = array();

        $db = cmsDatabase::getInstance();

        if(!$db->ready()){
            return $events;
        }

        if($is_enabled){
            $controllers_events = $db->getRows('events FORCE INDEX (is_enabled)', '`is_enabled` = 1', '*', 'ordering ASC', true);
        } else {
            $controllers_events = $db->getRows('events', '1', '*', 'ordering ASC', true);
        }

        if($controllers_events){
            foreach($controllers_events as $event){
                // для сохранения сортировки + совместимость
                if(!isset($events[ $event['listener'] ][$event['ordering']])){
                    $events[ $event['listener'] ][$event['ordering']] = $event['event'];
                } else {
                    // если вдруг по какой-то причине порядок одинаковый
                    $events[ $event['listener'] ][intval($event['ordering'].'00'.$event['id'])] = $event['event'];
                }
            }
        }

        return $events;

    }

    /**
     * Возвращает массив хуков контроллеров
     * из файлов манифестов
     * @return array
     */
    public static function getManifestsEvents(){

        $manifests_events = array();

        $controllers = cmsCore::getDirsList('system/controllers', true);

        $index = 0;

        foreach($controllers as $controller_name){

            $manifest_file = cmsConfig::get('root_path') . 'system/controllers/' . $controller_name . '/manifest.php';

            if (!is_readable($manifest_file)){ continue; }

            $manifest = include $manifest_file;

            if (empty($manifest['hooks']) || !is_array($manifest['hooks'])) { continue; }

            foreach ($manifest['hooks'] as $hook) {

                $manifests_events[ $controller_name ][$index] = $hook;

                $index++;

            }

        }

        return $manifests_events;

    }

//============================================================================//
//============================================================================//

    public static function getWidgetPath($widget_name, $controller_name=false){

        if ($controller_name){
            $path = "controllers/{$controller_name}/widgets/{$widget_name}";
        } else {
            $path = "widgets/{$widget_name}";
        }

        return $path;

    }

//============================================================================//
//============================================================================//

    /**
     * Подключает указанный языковой файл.
     * Если файл не указан, то подключаются все PHP-файлы из папки текущего языка
     *
     * @param string $file Относительный путь к файлу
     * @param string $default Язык по умолчанию, если в текущем не найдено
     * @return boolean
     */
    public static function loadLanguage($file = false, $default = 'ru') {

        $lang_dir = 'system/languages/'. self::$language;

        if (!$file){

            // Если файл не указан, то подключаем все php-файлы
            // из папки с текущим языком
            return self::getFilesList($lang_dir, '*.php', true, true);

        } else {

            // Если файл указан, то подключаем только его
            $lang_file = $lang_dir .'/'.$file.'.php';

            $result = self::includeFile($lang_file);

            if(!$result && $default !== self::$language){
                $result = self::includeFile('system/languages/'. $default .'/'.$file.'.php');
            }

            return $result;

        }

    }

    /**
     * Возвращает содержимое текстового файла из папки с текущим языком
     * @param string $file
     * @return string
     */
    public static function getLanguageTextFile($file){

        $lang_dir = cmsConfig::get('root_path').'system/languages/'.self::$language;

        $lang_file = $lang_dir .'/' . $file . '.txt';

        if (!file_exists($lang_file)) { return false; }

        return file_get_contents($lang_file);

    }

    /**
     * Подключает языковой файл контроллера
     * @param string $controller_name
     * @return bool
     */
    public static function loadControllerLanguage($controller_name){
        return self::loadLanguage("controllers/{$controller_name}/{$controller_name}");
    }

    /**
     * Подключает языковой файл виджета
     * @param string $widget_name
     * @param string $controller_name
     * @return bool
     */
    public static function loadWidgetLanguage($widget_name, $controller_name=false){

        $path = self::getWidgetPath($widget_name, $controller_name);

        return self::loadLanguage($path);

    }

    /**
     * Подключает языковой файл шаблона
     * @param string $template_name
     * @return bool
     */
    public static function loadTemplateLanguage($template_name){
        return self::loadLanguage("templates/{$template_name}");
    }

    /**
     * Подключает языковые файлы всех контроллеров
     *
     * @param string $file
     * @return bool
     */
    public static function loadAllControllersLanguages(){

        $controllers = self::getDirsList('system/controllers');

        foreach($controllers as $controller_name){
            self::loadControllerLanguage($controller_name);
        }

    }

//============================================================================//
//============================================================================//

    /**
     * Определяет контроллер, действие и параметры для запуска по полученному URI
     * @param string $uri
     */
    public function route($uri){

		$config = cmsConfig::getInstance();

        $uri = trim(urldecode($uri));
		$uri = mb_substr($uri, mb_strlen( $config->root ));

        if (!$uri) { return; }

        // если в URL присутствует знак вопроса, значит есть
        // в нем есть GET-параметры которые нужно распарсить
        // и добавить в массив $_REQUEST
        $pos_que  = mb_strpos($uri, '?');
        if ($pos_que !== false){

            // получаем строку запроса
            $query_data = array();
            $query_str  = mb_substr($uri, $pos_que+1);

            // удаляем строку запроса из URL
            $uri = mb_substr($uri, 0, $pos_que);

            // парсим строку запроса
            parse_str($query_str, $query_data);

            $this->uri_query = $query_data;

            // добавляем к полученным данным $_REQUEST
            // именно в таком порядке, чтобы POST имел преимущество над GET
            $_REQUEST = array_merge($query_data, $_REQUEST);

        }

        $this->uri = $this->uri_before_remap = $uri;
        $this->uri_absolute = $config->root . $uri;

        // разбиваем URL на сегменты
        $segments = explode('/', $uri);

        // Определяем контроллер из первого сегмента
        if (isset($segments[0])) { $this->uri_controller = $segments[0]; }

        // Определяем действие из второго сегмента
        if (isset($segments[1])) { $this->uri_action = $segments[1]; }

        // Определяем параметры действия из всех остальных сегментов
        if (sizeof($segments)>2){
            $this->uri_params = array_slice($segments, 2);
        }

        return true;

    }

    public function getUriData() {
        return array(
            'controller' => $this->uri_controller,
            'action'     => $this->uri_action,
            'params'     => $this->uri_params
        );
    }

//============================================================================//
//============================================================================//

    public function defineController() {

        $hash = md5($this->uri_controller.$this->uri_action);

        if(isset($this->defined_controllers[$hash])){
            return $this;
        }

        $this->defined_controllers[$hash] = true;

        $config = cmsConfig::getInstance();

        // контроллер и экшен по умолчанию
        if (!$this->uri_controller){ $this->uri_controller = $config->ct_autoload;	}
        if (!$this->uri_action) { $this->uri_action = 'index'; }

        // проверяем ремаппинг контроллера
        $remap_to = self::getControllerNameByAlias($this->uri_controller);
        if ($remap_to) {
            // в uri также меняем
            if($this->uri){
                $seg = explode('/', $this->uri);
                $seg[0] = $remap_to;
                $this->uri = implode('/', $seg);
                $this->uri_absolute = str_replace($this->uri_before_remap, $this->uri, $this->uri_absolute);
            }
            $this->uri_controller_before_remap = $this->uri_controller;
            $this->uri_controller = $remap_to;
        }

        if (!self::isControllerExists($this->uri_controller)) {
            if($this->uri_action !== 'index'){
                array_unshift($this->uri_params, $this->uri_action);
            }
            $this->uri_action     = $this->uri_controller;
            $this->uri_controller = $config->ct_default;
        }

        $this->controller = $this->uri_controller;

        return $this;

    }

    /**
     * Запускает выбранное действие контроллера
     */
    public function runController(){

        $this->defineController();

        if (!preg_match('/^[a-z]{1}[a-z0-9_]*$/', $this->controller)){
            self::error404();
        }

        // загружаем контроллер
        $controller = self::getController($this->controller, $this->request);

        // контроллер включен?
        if(!$controller->isEnabled()){
            return self::error404();
        }

        // редирект 301, если настроен ремап
        if(!$this->uri_controller_before_remap && $slug = $controller->hasSlug()){

            // если контроллер запрещает редирект, то 404
            if($controller->disallow_mapping_redirect){
                return self::error404();
            }

            $controller->redirectTo($slug, ($this->uri_action === 'index' ? '' : $this->uri_action), $this->uri_params, $this->uri_query, 301);
        }

        // запускаем действие
        $controller->runAction($this->uri_action, $this->uri_params);

    }

    /**
     * Определяет и загружает id страниц, которые определены для текущего uri
     * @return \cmsCore
     */
    public function loadMatchedPages() {

        if($this->matched_pages !== null){
            return $this;
        }

        if($this->widgets_pages === null){
            $this->widgets_pages = cmsCore::getModel('widgets')->getPages();
        }

        $this->matched_pages = $this->detectMatchedWidgetPages($this->widgets_pages);

        return $this;

    }

    public function getMatchedPages() {
        return $this->matched_pages;
    }

    public function setMatchedPages($matched_pages) {
        $this->matched_pages = $matched_pages;
        return $this;
    }

//============================================================================//
//============================================================================//

    /**
     * Запускает все виджеты, привязанные к текущей странице
     */
    public function runWidgets(){

        $template = cmsTemplate::getInstance();

        if($template->widgets_rendered){
            return $this;
        }

        $template->widgets_rendered = true;

        $controllers_without_widgets = cmsConfig::get('controllers_without_widgets');

        if ($controllers_without_widgets && in_array($this->controller, $controllers_without_widgets)) { return; }

        $matched_pages = $this->loadMatchedPages()->getMatchedPages();
        if (!$matched_pages) { return; }

        $widgets_list = cmsCore::getModel('widgets')->getWidgetsForPages(array_keys($matched_pages), $template->getName());

        if (is_array($widgets_list)){

            $device_type = cmsRequest::getDeviceType();
            $layout = $template->getLayout();
            $user = cmsUser::getInstance();

            if($user->is_admin){
                $template->addTplJSName('widgets');
                cmsCore::loadControllerLanguage('admin');
            }

            foreach ($widgets_list as $widget){

                // не выводим виджеты контроллеров, которые отключены
                if(!empty($widget['controller']) && !cmsController::enabled($widget['controller'])){
                    continue;
                }

                // проверяем доступ для виджетов
                if (!$user->isInGroups($widget['groups_view'])) { continue; }
                if (!empty($widget['groups_hide']) && $user->isInGroups($widget['groups_hide']) && !$user->is_admin) {
                    continue;
                }

                // проверяем для каких устройств показывать
                if($widget['device_types'] && !in_array($device_type, $widget['device_types'])){
                    continue;
                }

                // проверяем для каких макетов показывать
                if($widget['template_layouts'] && !in_array($layout, $widget['template_layouts'])){
                    continue;
                }

                // проверяем для каких языков показывать
                if($widget['languages'] && !in_array(cmsCore::getLanguageName(), $widget['languages'])){
                    continue;
                }

                cmsDebugging::pointStart('widgets');

                $this->runWidget($widget);

                cmsDebugging::pointProcess('widgets', array(
                    'data' => $widget['title'].' => /system/'.cmsCore::getWidgetPath($widget['name'], $widget['controller']).'/widget.php'
                ), 0);

            }

        }

        return $this;

    }

    public static function getWidgetObject($widget) {

        $file = 'system/'.cmsCore::getWidgetPath($widget['name'], $widget['controller']).'/widget.php';

        $class = 'widget' .
                    ($widget['controller'] ? string_to_camel('_', $widget['controller']) : '') .
                    string_to_camel('_', $widget['name']);

        if (!class_exists($class, false)) {
            cmsCore::includeFile($file);
            cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);
        }

        return new $class($widget);

    }

    public function runWidget($widget){

        $result = false;

        $widget_object = cmsCore::getWidgetObject($widget);

        $cache_key = 'widgets'.$widget['id'];
        $cache = cmsCache::getInstance();

        if($widget_object->isCacheable()){
            $result = $cache->get($cache_key);
        }

        if ($result === false){
            $result = call_user_func_array(array($widget_object, 'run'), array());
            if ($result !== false){
                // Отдельно кешируем имя шаблона виджета, заголовок и враппер, поскольку они могли быть
                // изменены внутри виджета, а в кеш у нас попадает только тот массив
                // который возвращается кодом виджета (без самих свойств $widget_object)
                $result['_wd_template'] = $widget_object->getTemplate();
                $result['_wd_title']    = $widget_object->title;
                $result['_wd_wrapper']  = $widget_object->getWrapper();
            }
            if($widget_object->isCacheable()){
                $cache->set($cache_key, $result);
            }
        }

        if ($result === false) { return false; }

        if (isset($result['_wd_template'])) { $widget_object->setTemplate($result['_wd_template']); }
        if (isset($result['_wd_title'])) { $widget_object->title = $result['_wd_title']; }
        if (isset($result['_wd_wrapper'])) { $widget_object->setWrapper($result['_wd_wrapper']); }

        return cmsTemplate::getInstance()->renderWidget($widget_object, $result);

    }

    /**
     * Определяет какие из списка страниц виджетов
     * совпадают по маске с текущей страницей
     *
     * @param type $pages
     * @return type
     */
    private function detectMatchedWidgetPages($pages){

        if ($this->uri == '') {
            return array(0, 1);
        }

        $matched_pages = array();

        $_full_uri = $this->uri.($this->uri_query ? '?'.http_build_query($this->uri_query) : '');

        //
        // Перебираем все точки привязок и проверяем совпадение
        // маски URL с текущим URL
        //
        foreach($pages as $page){

            if (empty($page['url_mask']) && !empty($page['id'])) { continue; }

            $is_mask_match = empty($page['id']);
            $is_stop_match = false;

            if (!empty($page['url_mask'])) {
                foreach($page['url_mask'] as $mask){
                    $regular = string_mask_to_regular($mask);
                    $regular = "/^{$regular}$/iu";
                    $is_mask_match = $is_mask_match || preg_match($regular, $this->uri);
                }
            }

            if (!empty($page['url_mask_not'])) {
                foreach($page['url_mask_not'] as $mask){
                    $regular = string_mask_to_regular($mask);
                    $regular = "/^{$regular}$/iu";
                    $is_stop_match = $is_stop_match || preg_match($regular, $_full_uri);
                }
            }

            if ($is_mask_match && !$is_stop_match){
                $matched_pages[$page['id']] = $page;
            }

        }

        return $matched_pages;

    }

//============================================================================//
//============================================================================//

    /**
     * Показывает сообщение об ошибке и завершает работу
     * @param string $message
     */
    public static function error($message, $details=''){

        if(ob_get_length()) { ob_end_clean(); }

        http_response_code(503);

        if (cmsConfig::get('debug')){
            cmsTemplate::getInstance()->renderAsset('errors/error', array(
                'message'=>$message,
                'details'=>$details
            ));
        } else {
            echo '<h1>503 Service Unavailable</h1>';
            echo '<h2>Please, enable debug mode in the site settings</h2>';
        }

        die();

    }

    /**
     * Показывает сообщение об ошибке 403 и завершает работу
     * @param string $message Текстовое сообщение к ошибке
     * @param boolean $show_login_link Показывать ссылку на авторизацию
     * @return die
     */
    public static function errorForbidden($message = '', $show_login_link = false){

		$result = cmsEventsManager::hook('error_403', self::getInstance()->uri);

        if($result === true){ return false; }

        if(ob_get_length()) { ob_end_clean(); }

        http_response_code(403);

        cmsTemplate::getInstance()->renderAsset('errors/forbidden', array(
            'message'         => $message,
            'show_login_link' => $show_login_link
        ));

        die();

    }

    /**
     * Показывает сообщение об ошибке 404 и завершает работу
     */
    public static function error404(){

		$result = cmsEventsManager::hook('error_404', self::getInstance()->uri);

        if($result === true){ return false; }

        if(ob_get_length()) { ob_end_clean(); }

        http_response_code(404);

        cmsTemplate::getInstance()->renderAsset('errors/notfound');
        die();

    }

    /**
     * Показывает сообщение о том что сайт отключен и завершает работу
     */
    public static function errorMaintenance(){

        if(ob_get_length()) { ob_end_clean(); }

        http_response_code(503);

        cmsTemplate::getInstance()->renderAsset('errors/offline', array(
            'reason' => cmsConfig::get('off_reason')
        ));
        die();

    }

    public static function respondIfModifiedSince($lastmod) {

        $last_modified_unix = strtotime($lastmod);

        $last_modified = gmdate("D, d M Y H:i:s \G\M\T", $last_modified_unix);

        $if_modified_since = false;

        if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
            $if_modified_since = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $if_modified_since = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
        }

        if ($if_modified_since && $if_modified_since >= $last_modified_unix) {

            if (ob_get_length()) {
                ob_end_clean();
            }

            http_response_code(304);

            die();

        }

        header('Last-Modified: ' . $last_modified);

    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает массив со списком всех шаблонов
     * @return array
     */
    public static function getTemplates(){

        if(self::$templates !== null){
            return self::$templates;
        }

        if(cmsTemplate::TEMPLATE_BASE_PATH){
            return self::$templates = self::getDirsList(cmsTemplate::TEMPLATE_BASE_PATH);
        }

        $root_path = cmsConfig::get('root_path');
        $all_dirs = self::getDirsList('');
        $result = [];

        foreach($all_dirs as $dir){
            // В папке шаблона в обязательном порядке должны быть два файла
            if(file_exists($root_path.$dir.'/main.tpl.php') && file_exists($root_path.$dir.'/scheme.html')){
                $result[] = $dir;
            }
        }

        return self::$templates = $result;

    }

    /**
     * Возвращает массив со списком всех языков
     * @return array
     */
    public static function getLanguages(){

        $default_lang = cmsConfig::get('language');

        $langs = self::getDirsList('system/languages', true);

        $current_lang_key = array_search(self::$language, $langs);

        if($current_lang_key !== 0){

            list($langs[0], $langs[$current_lang_key]) = [$langs[$current_lang_key], $langs[0]];

        }

        if($default_lang !== self::$language){

            $default_lang_key = array_search($default_lang, $langs);

            if($default_lang_key !== 1){

                list($langs[1], $langs[$default_lang_key]) = [$langs[$default_lang_key], $langs[1]];

            }

        }

        return $langs;

    }

    /**
     * Возвращает массив со списком всех визуальных редакторов
     * @return array
     */
    public static function getWysiwygs(){

        return self::getDirsList('wysiwyg');

    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает список директорий внутри указанной
     * @param string $root_dir
     * @param bool $asc_sort Сортировать по алфавиту, по умолчанию false
     * @return array
     */
    public static function getDirsList($root_dir, $asc_sort=false){

        $dir = cmsConfig::get('root_path') . $root_dir;
        $dir_context = opendir($dir);

        $list = array();

        while ($next = readdir($dir_context)){

            if (in_array($next, array('.', '..'))){ continue; }
            if (strpos($next, '.') === 0){ continue; }
            if (!is_dir($dir.'/'.$next)) { continue; }

            $list[] = $next;

        }

        if($asc_sort){
            sort($list);
        }

        return $list;

    }

    /**
     * Возвращает список файлов из указанной директории по нужной маске
     * @param string $root_dir Директория
     * @param string $pattern Маска файлов
     * @param bool $is_strip_ext Отрезать расширения?
     * @param bool $is_include Подключать каждый файл?
     * @return array
     */
    public static function getFilesList($root_dir, $pattern='*.*', $is_strip_ext=false, $is_include=false){

        $config = cmsConfig::getInstance();

        $directory = $config->root_path . $root_dir;
        $pattern = $directory . '/' . $pattern;

        $list = array();

        $files = @glob($pattern);

        if (!$files) { return $list; }

        foreach ($files as $file) {

            if ($is_include && !isset(self::$includedFiles[$file])) {
                include_once $file;
                self::$includedFiles[$file] = true;
            }

            $file = basename($file);

            if ($is_strip_ext){ $file = pathinfo($file, PATHINFO_FILENAME); }

            $list[] = $file;

        }

        return $list;

    }

//============================================================================//
//============================================================================//

    /**
     * Устанавливает соединение с БД
     *
     */
    public function connectDB(){
        $this->db = cmsDatabase::getInstance();
    }

//============================================================================//
//============================================================================//

    public static function getTimeZones(){
        self::loadLib('timezones');
        $_zones = getTimeZones();
        $zones = array();
        foreach($_zones as $zone){
            $zones[ $zone ] = $zone;
        }
        return $zones;
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
