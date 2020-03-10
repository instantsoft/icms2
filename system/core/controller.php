<?php
class cmsController {

    private static $controllers;
    private static $mapping;

    public $name;
    public $title;
	public $model = null;
    public $request;
    public $current_action;
    public $current_template_name;
    public $current_params;
    public $options;
    public $root_url;
    public $root_path;

    /**
     * Контекст списка записей
     * @var string
     */
    protected $list_type = 'category_view';
    /**
     * Если для контроллера задан ремап
     * и это свойство установлено в true
     * редиректа со старого адреса не будет
     *
     * @var boolean
     */
    public $disallow_mapping_redirect = false;
    /**
     * Флаг, что контроллер должен работать только после
     * регистрации в БД
     * @var boolean
     */
    public $mb_installed = false;

    /**
     * Флаг наличия SEO параметров для index экшена
     * @var boolean
     */
    public $useSeoOptions = false;
    /**
     * Флаг наличия SEO паттернов для страниц записей
     * @var boolean
     */
    public $useItemSeoOptions = false;

    /**
     * Флаг блокировки прямого вызова экшена
     * полезно если название экшена переопределяется
     * а вызов экшена напрямую нужно запретить
     * @var boolean || null
     */
    public $lock_explicit_call = null;

    /**
     * Если необходимо использовать у контроллера
     * не свою модель и/или своя модель
     * будет наследоваться от модели какого-то контроллера
     * укажите его в этом параметре в классе своего контроллера
     *
     * @var string|array
     */
    protected $outer_controller_model = '';

    protected $callbacks = array();
    protected $useOptions = false;

    protected $active_filters = array();

    /**
     * Неизвестные экшены определять
     * как первый параметр экшена index
     * @var boolean
     */
    protected $unknown_action_as_index_param = false;

    public function __construct( cmsRequest $request){

        self::loadControllers();

        $this->name = $this->name ? $this->name : mb_strtolower(get_called_class());

        $this->root_url = $this->name;

        $this->root_path = $this->cms_config->root_path . 'system/controllers/' . $this->name . '/';

        $this->setRequest($request);

        cmsCore::loadControllerLanguage($this->name);

        $title_constant = 'LANG_'.strtoupper($this->name).'_CONTROLLER';

        $this->title = defined($title_constant) ? constant($title_constant) : $this->name;

        if($this->outer_controller_model){
            cmsCore::includeModel($this->outer_controller_model);
        }

        if (cmsCore::isModelExists($this->name)){
            $this->model = cmsCore::getModel($this->name);
        } elseif($this->outer_controller_model) {
            $this->model = cmsCore::getModel($this->outer_controller_model);
        }

        if ($this->useOptions){
            $this->options = $this->getOptions();
        }

        $this->loadCallback();

    }

    public function setRequest( cmsRequest $request) {
        $this->request = $request; return $this;
    }

    /////////////////    Набор методов для коллбэков    ////////////////////////
    /**
     * Этот метод переопределяется в дочерних классах
     * где задается начальный набор функций, которые будут применены в коллбэках
     */
    public function loadCallback() {}
    /**
     * Устанавливает один или множество коллбэков
     * @param string $name Назначение - обычно название метода, где будет применяться
     * @param array $callbacks Массив коллбэков
     * @return \cmsController
     */
    public function setCallback($name, $callbacks) { $this->callbacks[$name] = $callbacks; return $this; }
    /**
     * Применяет коллбэки
     * @param string $name Назначение - обычно __FUNCTION__
     * @param array $params Массив параметров
     * @return \cmsController
     */
    public function processCallback($name, $params) {
        $name = strtolower($name);
        if(!empty($this->callbacks[$name])){
            array_unshift($params, $this);
            foreach ($this->callbacks[$name] as $callback) {
                call_user_func_array($callback, $params);
            }
        }
        return $this;
    }

    protected function loadCmsObj($name) {

        if(strpos($name, 'cms_') === 0){

            $class_name = string_to_camel('_', $name);

            if(method_exists($class_name, 'getInstance')){
                $this->{$name} = call_user_func(array($class_name, 'getInstance'));
            } else {
                $this->{$name} = new $class_name();
            }

            return true;

        }

        return false;

    }

    public function __get($name) {

        if($this->loadCmsObj($name)){
            return $this->{$name};
        }

        if(strpos($name, 'controller_') === 0){
            $this->{$name} = cmsCore::getController(str_replace('controller_', '', $name), $this->request);
            return $this->{$name};
        }

        if(strpos($name, 'model_') === 0){
            $this->{$name} = cmsCore::getModel(str_replace('model_', '', $name));
            return $this->{$name};
        }

        return null;

    }

    public function setRootURL($root_url){
        $this->root_url = $root_url;
    }

//============================================================================//
//============================================================================//

    public function setListContext($list_type) {
        $this->list_type = $list_type; return $this;
    }

    public function getListContext() {
        return $this->list_type;
    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает опции текущего контроллера,
     * @return array
     */
    public function getOptions(){

        return (array)self::loadOptions($this->name);

    }
    public function setOption($key, $val){
        $this->options[$key] = $val; return $this;
    }
    public function getOption($key){
        if(!$this->useOptions){ return null; }
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * Проверяет включен ли текущий контроллер
     * @return boolean
     */
    public function isEnabled() {
        return $this->isControllerEnabled($this->name);
    }

    public function isControllerInstalled($name) {
        return isset(self::$controllers[$name]);
    }

    public function hasSlug() {
        return !empty(self::$controllers[$this->name]['slug']) ? self::$controllers[$this->name]['slug'] : false;
    }

    public function isControllerEnabled($name) {

        // проверяем только те, которые зарегистрированы в базе
        if (isset(self::$controllers[$name]['is_enabled'])){
            return self::$controllers[$name]['is_enabled'];
        }

        return true;

    }

    public static function enabled($name) {
        self::loadControllers();
        if (isset(self::$controllers[$name]['is_enabled'])){
            return self::$controllers[$name]['is_enabled'];
        }
        return true;
    }

    public static function getControllersMapping() {

        if (self::$mapping !== null) { return self::$mapping; }

        self::$mapping = array();

        self::loadControllers();

        foreach (self::$controllers as $controller) {
            if(!empty($controller['slug'])){
                self::$mapping[$controller['name']] = $controller['slug'];
            }
        }

        return self::$mapping;

    }

    private static function loadControllers() {

        if(!isset(self::$controllers)){

            $model = new cmsModel();

            $model->selectList(array(
                'i.id'         => 'id',
                'i.is_enabled' => 'is_enabled',
                'i.options'    => 'options',
                'i.name'       => 'name',
                'i.slug'       => 'slug'
            ), true);

            self::$controllers = $model->useCache('controllers')->get('controllers', function ($item, $model) {
                $item['options'] = cmsModel::yamlToArray($item['options']);
                return $item;
            }, 'name');

        }

    }
    /**
     * Загружает опции контроллера
     * @param string $controller_name
     * @return array
     */
    public static function loadOptions($controller_name){
        self::loadControllers();
        if (isset(self::$controllers[$controller_name]['options'])){
            return self::$controllers[$controller_name]['options'];
        }

        return array();

    }

    /**
     * Сохраняет опции контроллера
     * @param string $controller_name
     * @param array $options
     * @return boolean
     */
    static function saveOptions($controller_name, $options){

        $model = new cmsModel();

        $model->filterEqual('name', $controller_name);

        $model->updateFiltered('controllers', array('options' => $options));

        cmsCache::getInstance()->clean('controllers');

        return true;

    }

//============================================================================//
//============================================================================//

    //
    // ХУКИ
    //

    /**
     * Вызывается до начала работы экшена
     */
    public function before($action_name){

        $this->cms_template->setContext($this);

        if($this->useSeoOptions && $action_name == 'index'){

            if (!empty($this->options['seo_keys'])){ $this->cms_template->setPageKeywords($this->options['seo_keys']); }
            if (!empty($this->options['seo_desc'])){ $this->cms_template->setPageDescription($this->options['seo_desc']); }

        }

        return true;

    }

    /**
     * Вызывается после работы экшена
     */
    public function after($action_name){

        $this->cms_template->restoreContext();

        return true;

    }

    /**
     * Вызывается до начала работы хука
     */
    public function beforeHook($event_name){

        if ($this->useOptions){
            $this->options = $this->getOptions();
        }

        return true;

    }

    /**
     * Вызывается после работы хука
     */
    public function afterHook($event_name){

        return true;

    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет существование экшена
     * @param string $action_name
     * @return boolean
     */
    public function isActionExists($action_name){

        $method_name = 'action' . string_to_camel('_', $action_name);

        if(method_exists($this, $method_name)){
            return true;
        }

        $action_file = $this->getExternalActionPath($action_name);

        if (is_readable($action_file)){
            return true;
        }

        return false;

    }

    /**
     * Запускает требуемый экшен
     * @param string $action_name
     * @param array $params
     * @return mixed
     */
    public function runAction($action_name, $params = array()){

        if ($this->before($action_name) === false) { return false; }

        $this->current_params = $params;

        $action_name = $this->routeAction($action_name);

        $result = $this->executeAction($action_name, $this->current_params);

        $this->after($action_name);

        return $result;

    }

    /**
     * Находит и выполняет требуемый экшен
     * @param string $action_name
     * @param array $params
     * @return mixed
     */
    public function executeAction($action_name, $params = array()) {

        $method_name = 'action' . string_to_camel('_', $action_name);

        // проверяем наличие экшена его в отдельном файле
        $action_file = $this->getExternalActionPath($action_name);

        if(is_readable($action_file)){

            // вызываем экшен из отдельного файла
            $result = $this->runExternalAction($action_name, $params);

        } else {

            // Если файла нет, ищем метод класса
            if (method_exists($this, $method_name)){

                if (!$this->validateParamsCount($this, $method_name, $params)) { cmsCore::error404(); }

                // сохраняем название текущего экшена
                $this->setCurrentAction($action_name);

                // если есть нужный экшен, то вызываем его
                $result = call_user_func_array(array($this, $method_name), $params);

            } else {

                // если нет экшена в отдельном файле,
                // проверяем метод route()
                if(method_exists($this, 'route')){

                    $route_uri = $action_name;
                    if ($params) { $route_uri .= '/' . implode('/', $params); }
                    $result = call_user_func(array($this, 'route'), $route_uri);

                } else {

                    // если метода route() тоже нет,
                    // то 404
                    cmsCore::error404();

                }

            }

        }

        return $result;

    }

    /**
     * Проверяем максимальное число аргументов экшена
     * Возвращает false если переданных количество параметров не соответствует кол-ву аргументов экшена
     * Для отключения проверки, можно переопределить этот метод (например см. в контроллере admin)
     * @param string|object $class Имя класса или текущий объект контроллера $this
     * @param string $method_name Имя метода
     * @param array $params Массив параметров
     * @return bool Результат проверки
     */
    protected function validateParamsCount($class, $method_name, $params) {
        $rf = new ReflectionMethod($class, $method_name);
        // кол-во переданных параметров
        $current_params = count($params);
        // передано больше чем нужно параметров
        if ($rf->getNumberOfParameters() < $current_params) { return false; }
        // передано меньше чем нужно параметров
        if ($rf->getNumberOfRequiredParameters() > $current_params) { return false; }
        return true;
    }

    /**
     * Возвращает путь к файлу экшена (./actions/$action_name.php по умолчанию)
     * @param string $action_name
     * @return string
     */
    public function getExternalActionPath($action_name) {
        return $this->root_path . 'actions/'.$action_name.'.php';
    }

    /**
     * Устанавливает имя текущего экшена
     * и шаблона экшена (если он не будет передан в cmsTemplate->render)
     *
     * @param string $action_name
     * @return $this
     */
    public function setCurrentAction($action_name) {

        $this->current_action = $action_name;
        $this->current_template_name = $action_name;

        return $this;
    }

    /**
     * Выполняет экшен, находящийся в отдельном файле
     * @param string $action_name Название экшена
     * @param array $params Параметры
     * @return mixed
     */
    public function runExternalAction($action_name, $params = array()){

        $action_file = $this->getExternalActionPath($action_name);

        $class_name = 'action' . string_to_camel('_', $this->name) . string_to_camel('_', $action_name);

        if (!is_readable($action_file)){
            cmsCore::error(ERR_FILE_NOT_FOUND . ': '. str_replace(PATH, '', $action_file));
        }

        include_once $action_file;

        if(!class_exists($class_name, false)){
            cmsCore::error(sprintf(ERR_CLASS_NOT_DEFINED, str_replace(PATH, '', $action_file), $class_name));
        }

        if (!$this->validateParamsCount($class_name, 'run', $params)) { cmsCore::error404(); }

        // сохраняем название текущего экшена
        $this->setCurrentAction($action_name);

        $action_object = new $class_name($this, $params);

        // проверяем разрешен ли прямой вызов экшена
        if($action_object->lock_explicit_call === true && $this->lock_explicit_call !== false && !$this->request->isInternal()){
            cmsCore::error404();
        }

        // проверяем параметры если нужно
        $params_error = $this->validateRequestParams($action_object);
        if($params_error !== false){
            if ($this->request->isAjax()){
                return $this->cms_template->renderJSON(array('error' => true, 'errors' => $params_error, 'message' => sprintf(LANG_REQUEST_PARAMS_ERROR, implode(', ', array_keys($params_error)))));
            } else {
                cmsCore::error(LANG_ERROR, sprintf(LANG_REQUEST_PARAMS_ERROR, implode(', ', array_keys($params_error))));
            }
        }

        return call_user_func_array(array($action_object, 'run'), $params);

    }

    /**
     * Проверяет параметры запроса, если они заданы
     * @param object $action_object
     * @return boolean
     */
    public function validateRequestParams($action_object) {

        if(empty($action_object->request_params)){
            return false;
        }

        $errors = array();

        // валидация аналогична валидации форм
        foreach ($action_object->request_params as $param_name => $rules) {

            $value = $this->request->get($param_name, null);

            if (is_null($value) && isset($rules['default'])) {

                $value = $rules['default'];

                $this->request->set($param_name, $value);

            } elseif(!is_null($value) && isset($rules['default'])){

                $value = $this->request->get($param_name, $rules['default']);

                // для применения типизации переменной
                $this->request->set($param_name, $value);

            }

            foreach ($rules['rules'] as $rule) {

                if (!$rule) { continue; }

                $validate_function = "validate_{$rule[0]}";

                $rule[] = $value;

                unset($rule[0]);

                $result = call_user_func_array(array($this, $validate_function), $rule);

                // если получилось false, то дальше не проверяем, т.к.
                // ошибка уже найдена
                if ($result !== true) {
                    $errors[$param_name] = $result;
                    break;
                }

            }

        }

        if (!sizeof($errors)) { return false; }

        return $errors;

    }

//============================================================================//
//============================================================================//

    /**
     * Находит и запускает хук для указанного события
     * @param string $event_name Название события
     * @param array $params Параметры события
     * @param mixed $default Умолчания, если хука нет
     * @return mixed
     */
    public function runHook($event_name, $params = array(), $default = null){

        if ($this->beforeHook($event_name) === false) { return false; }

        $method_name = 'on' . string_to_camel('_', $event_name);

        if(method_exists($this, $method_name)){

            // если есть нужный хук, то вызываем его
            $result = call_user_func_array(array($this, $method_name), $params);

        } else {

            // если метода хука нет, проверяем наличие его в отдельном файле
            $hook_file = $this->root_path . 'hooks/' . $event_name . '.php';

            if (is_readable($hook_file)){

                // вызываем хук из отдельного файла
                $result = $this->runExternalHook($event_name, $params);

            } else {

                // хука нет вообще, возвращаем данные запроса без изменений
                if($default === null){
                    return $this->request->getData();
                } else {
                    return $default;
                }

            }

        }

        $this->afterHook($event_name);

        return $result;

    }

    /**
     * Выполняет хук, находящийся в отдельном файле ./hooks/$event_name.php
     * @param string $event_name Название события
     * @param array $params Параметры события
     * @return mixed
     */
    public function runExternalHook($event_name, $params = array()){

        $class_name = 'on' . string_to_camel('_', $this->name) . string_to_camel('_', $event_name);

        if (!class_exists($class_name, false)){

            $hook_file = $this->root_path . 'hooks/' . $event_name . '.php';

            include_once $hook_file;

        }

        $hook_object = new $class_name($this);

        return call_user_func_array(array($hook_object, 'run'), $params);

    }

//============================================================================//
//============================================================================//

    public function getActiveFiltersQuery() {
        return $this->active_filters ? http_build_query($this->active_filters) : '';
    }

    public function getActiveFilters() {
        return $this->active_filters;
    }

    public function setActiveFilter($key, $value) {

        $this->active_filters[$key] = $value;

        return $this;
    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает описание структуры формы
     * в контексте текущего контроллера и, в свою очередь,
     * его контекста - Frontend или Backend
     *
     * @param string $form_name Название формы
     * @param array $params Параметры формы
     * @param string $path_prefix Префикс путь к файлу формы относительно директории контроллера
     * @return \cmsForm
     */
    public function getForm($form_name, $params = false, $path_prefix = ''){

        $form_file = $this->root_path . $path_prefix . 'forms/form_' . $form_name . '.php';
        $_form_name = $this->name . $form_name;

        $form = cmsForm::getForm($form_file, $_form_name, $params, $this);

        if($form === false){
            return cmsCore::error(ERR_FILE_NOT_FOUND . ': '. str_replace(PATH, '', $form_file));
        }

        if(is_string($form)){
            return cmsCore::error($form);
        }

        list($form, $params) = cmsEventsManager::hook('form_'.$this->name.'_'.$form_name, array($form, $params));

        return $form;

    }

    public function getControllerForm($controller, $form_name, $params = false){

        $form_file = $this->cms_config->root_path.'system/controllers/'.$controller.'/forms/form_'.$form_name.'.php';
        $_form_name = $controller . $form_name;

        $form = cmsForm::getForm($form_file, $_form_name, $params, $this);

        if($form === false){
            cmsCore::error(ERR_FILE_NOT_FOUND . ': '. str_replace(PATH, '', $form_file));
        }

        list($form, $params) = cmsEventsManager::hook('form_'.$controller.'_'.$form_name, array($form, $params));

        return $form;

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает описание структуры grid таблицы
     * @param string $grid_name Название
     * @param array $params Параметры для передачи в функцию описания grid-а
     * @param string $ups_key Ключ UPS
     * @return array || false
     */
    public function loadDataGrid($grid_name, $params = false, $ups_key = ''){

        $default_options = array(
            'order_by'      => 'id',
            'order_to'      => 'asc',
            'show_id'       => true,
            'is_auto_init'  => true,
            'is_sortable'   => true,
            'is_filter'     => true,
            'is_actions'    => true,
            'is_pagination' => true,
            'perpage'       => 30,
            'is_toolbar'    => true,
            'is_draggable'  => false,
            'drag_save_url' => '',
            'is_selectable' => false,
            'load_columns'  => false
        );

        $grid_file = $this->root_path . 'grids/grid_' . $grid_name . '.php';

        if (!is_readable($grid_file)){
            cmsCore::error(ERR_FILE_NOT_FOUND . ': '. str_replace(PATH, '', $grid_file));
        }

        include($grid_file);

        $args = array($this);
        if ($params) {
            if (!is_array($params)){
                $params = [$params];
            }
            foreach ($params as $p) {
                $args[] = $p;
            }
        }

        $grid = call_user_func_array('grid_'.$grid_name, $args);

        if (!isset($grid['options'])) {
            $grid['options'] = $default_options;
        } else {
            $grid['options'] = array_merge($default_options, $grid['options']);
        }

		$grid = cmsEventsManager::hook('grid_'.$this->name.'_'.$grid_name, $grid);
        list($grid, $args) = cmsEventsManager::hook('grid_'.$this->name.'_'.$grid_name.'_args', array($grid, $args));

        if ($this->request->isAjax() && $this->request->has('heads')) {

            $heads = $this->request->get('heads', array());
            natsort($heads);

            $grid_heads = array_keys($grid['columns']);
            if ($grid['actions']) {
                $grid_heads[] = 'dg_actions';
            }
            natsort($grid_heads);

            if ($heads !== $grid_heads) {
                $grid['options']['load_columns'] = true;
            }
        }

        if($ups_key){
            $filter_str = cmsUser::getUPS($ups_key);
            if($filter_str){
                parse_str($filter_str, $filter);
                $grid['filter'] = $filter;
            }
        }

        return $grid;

    }

//============================================================================//
//============================================================================//

    public function loadRoutes(){

        $file = $this->root_path . 'routes.php';

        if (!is_readable($file)){ return array(); }

        include_once($file);

        $routes_func = 'routes_' . $this->name;

        $routes = call_user_func($routes_func);

        if (!is_array($routes)) { return array(); }

        return $routes;

    }

//============================================================================//
//============================================================================//

    public function halt($text='') {
        die((string)$text);
    }

    public function getUniqueKey($params) {
        array_unshift($params, $this->name);
        return implode('.', $params);
    }

//============================================================================//
//============================================================================//

    /**
     * Позволяет переопределить экшен перед вызовом
     * @param string $action_name
     * @return string
     */
    public function routeAction($action_name){

        // Избавляемся от index в url
        if($this->unknown_action_as_index_param){

            if($this->isActionExists($action_name)){
                return $action_name;
            }

            array_unshift($this->current_params, $action_name);

            return 'index';

        }

        return $action_name;

    }

//============================================================================//
//============================================================================//

    /**
     * Определяет экшен, по списку маршрутов из файла routes.php контроллера
     * @param string $uri
     * @return boolean
     */
    public function parseRoute($uri){

        $routes = $this->loadRoutes();

        // Флаг удачного перебора
		$is_found = false;

        // Название найденного экшена
        $action_name = false;

        //перебираем все маршруты
		if($routes){
			foreach($routes as $route){

				//сравниваем шаблон маршрута с текущим URI
				preg_match($route['pattern'], $uri, $matches);

				//Если найдено совпадение
				if ($matches){

                    $action_name = $route['action'];

					// удаляем шаблон и экшен из параметров маршрута,
                    // чтобы не мешали при переборе параметров запроса
					unset($route['pattern']);
					unset($route['action']);

					//перебираем параметры маршрута в виде ключ=>значение
					foreach($route as $key=>$value){
						if (is_integer($key)){

                            //Если ключ - целое число, то значением является сегмент URI
                            $this->request->set($value, $matches[$key]);

						} else {

							//иначе, значение берется из маршрута
                            $this->request->set($key, $value);

						}
					}

					// совпадение есть
					$is_found = true;

					//раз найдено совпадение, прерываем цикл
					break;

				}

			}
		}

		// Если в маршруте нет совпадений
		if(!$is_found) { return false; }

        return $action_name;

    }

//============================================================================//
//============================================================================//

    /**
     * Редирект на указанный адрес
     * @param string $url
     * @param integer $code
     */
    public function redirect($url, $code=303){

        if ($this->request->isAjax()){

            $this->cms_template->renderAsset('ui/redirect_continue', array(
                'redirect_url' => href_to($url)
            ), $this->request);

        } else {

            if ($code == 301){
                header('HTTP/1.1 301 Moved Permanently');
            } else {
                header('HTTP/1.1 303 See Other');
            }
            header('Location: '.$url);

        }

        $this->halt();

    }

    /**
     * Редирект на главную страницу
     */
    public function redirectToHome(){
        $this->redirect(href_to_home());
    }


    /**
     * Редирект на другой контроллер
     * @param string $controller
     * @param string $action
     * @param array $params
     * @param array $query
     */
    public function redirectTo($controller, $action='', $params=array(), $query=array(), $code=303){

        $href_lang = cmsCore::getLanguageHrefPrefix();

        $location = $this->cms_config->root .($href_lang ? $href_lang.'/' : ''). $controller . ($action ? '/'.$action : '');

        if ($params){ $location .= '/' . implode('/', $params); }
        if ($query){ $location .= '?' . http_build_query($query, '', '&'); }

        $this->redirect($location, $code);

    }

    /**
     * Редирект на собственный экшен
     * @param string $action
     * @param array $params
     * @param array $query
     */
    public function redirectToAction($action='', $params=array(), $query=array()){

        if (!$action || $action=='index') {
            $location = href_to($this->root_url);
        } else {
            $location = href_to($this->root_url, $action);
        }

		if ($params){
			if (is_array($params)) { $location .= '/' . implode('/', $params); }
			else { $location .= '/' . $params; }
		}

        if ($query){ $location .= '?' . http_build_query($query); }

        $this->redirect($location);

    }

    /**
     * Возвращает предыдущий URL текущего сайта
     * @return string
     */
    public function getBackURL() {

        $back_url = $this->cms_config->root;

        if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'http') === 0){

            $refer = $_SERVER['HTTP_REFERER'];

            if(strpos($refer, $this->cms_config->protocol.$_SERVER['HTTP_HOST']) === 0) {
                $back_url = $refer;
            }

        }

        return $back_url;

    }

    /**
     * Редирект на предыдущий URL
     */
    public function redirectBack(){
        $url = $this->getBackURL();
        header('Location: '.$url);
        $this->halt();
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает список субъектов к которым применяются права пользователей
     * @return array
     */
    public function getPermissionsSubjects(){
        return array(
            array(
                'name' => $this->name,
                'title' => $this->title
            )
        );
    }

    public function getContentTypeForModeration($name){

        return array(
            'id'    => null,
            'name'  => $this->name,
            'title' => $this->title
        );

    }

//============================================================================//
//============================================================================//

    public function validate_required($value, $disable_empty = true){
        if($value === '0' && !$disable_empty){ return true; }
        if (empty($value)) { return ERR_VALIDATE_REQUIRED; }
        return true;
    }

    public function validate_min($min, $value){

        if (empty($value)) { $value = 0; }

        if (!in_array(gettype($value), array('integer','string','double')) || !preg_match("/^([\-]?)([0-9\.,]+)$/i", $value)){
            return ERR_VALIDATE_NUMBER;
        }

        if ((float)$value < $min) { return sprintf(ERR_VALIDATE_MIN, $min); }

        return true;

    }

    public function validate_max($max, $value){

        if (empty($value)) { $value = 0; }

        if (!in_array(gettype($value), array('integer','string','double')) || !preg_match("/^([\-]?)([0-9\.,]+)$/i", $value)){
            return ERR_VALIDATE_NUMBER;
        }

        if ((float)$value > $max) { return sprintf(ERR_VALIDATE_MAX, $max); }

        return true;

    }

    public function validate_minfloat($min, $value){
        if (empty($value)) { return true; }
        if(bccomp(sprintf('%.8f', $min), sprintf('%.8f', $value), 8) === 1){
            return sprintf(ERR_VALIDATE_MIN, $min);
        }
        return true;
    }

    public function validate_maxfloat($min, $value){
        if (empty($value)) { return true; }
        if(bccomp(sprintf('%.8f', $min), sprintf('%.8f', $value), 8) === -1){
            return sprintf(ERR_VALIDATE_MAX, $min);
        }
        return true;
    }

    public function validate_min_length($length, $value){

        if (empty($value)) { return true; }

        if (is_array($value)){
            return ERR_VALIDATE_INVALID;
        }

        if (mb_strlen($value) < $length) { return sprintf(ERR_VALIDATE_MIN_LENGTH, $length); }

        return true;

    }

    public function validate_max_length($length, $value){

        if (empty($value)) { return true; }

        if (is_array($value)){
            return ERR_VALIDATE_INVALID;
        }

        if (mb_strlen($value) > $length) { return sprintf(ERR_VALIDATE_MAX_LENGTH, $length); }

        return true;

    }

    public function validate_array_key($array, $value){
        if (is_array($value)) {
            $result = true;
            foreach ($value as $val) {
                if(!isset($array[$val])){
                    $result = ERR_VALIDATE_INVALID; break;
                }
            }
            return $result;
        }
        if (!isset($array[$value])) { return ERR_VALIDATE_INVALID; }
        return true;
    }

    public function validate_array_keys($array, $values){
		if (empty($values)) { return true; }
        if (!is_array($values)) { return ERR_VALIDATE_INVALID; }
		foreach($values as $value){
			if (!isset($array[$value])) { return ERR_VALIDATE_INVALID; }
		}
        return true;
    }

    public function validate_in_array($array, $value){
        if (empty($value)) { return true; }
        if (!in_array($value, $array)) { return ERR_VALIDATE_INVALID; }
        return true;
    }

    public function validate_email($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== $value){ return ERR_VALIDATE_EMAIL; }
        return true;
    }

    public function validate_url($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match('_^(?:(?:https?)://)(?:(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?:/[^\s]*)?$_iuS', $value)){ return ERR_VALIDATE_URL; }
        return true;
    }

    public function validate_alphanumeric($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match("/^([a-z0-9]*)$/i", $value)){ return ERR_VALIDATE_ALPHANUMERIC; }
        return true;
    }

    public function validate_localealphanumeric($value){
        if (empty($value)) { return true; }
        $regexp = "/^([\w \.\?\@\,\-]*)$/ui";
        if(defined('LC_LANGUAGE_VALIDATE_REGEXP')){
            $regexp = LC_LANGUAGE_VALIDATE_REGEXP;
        }
        if (!is_string($value) || !preg_match($regexp, $value)){ return ERR_VALIDATE_REGEXP; }
        return true;
    }

    public function validate_sysname($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match("/^([a-z0-9\_]*)$/", $value)){ return ERR_VALIDATE_SYSNAME; }
        return true;
    }

    public function validate_phone($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match("/^([0-9\-\+\(\)\s]*)$/", $value)){ return ERR_VALIDATE_INVALID; }
        return true;
    }

    public function validate_slug($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match("/^([a-z0-9\-\/]*)$/", $value)){ return ERR_VALIDATE_SLUG; }
        return true;
    }

    public function validate_digits($value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string')) || !preg_match("/^([0-9]+)$/i", $value)){ return ERR_VALIDATE_DIGITS; }
        return true;
    }

    public function validate_number($value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string','double')) || !preg_match("/^([\-]?)([0-9\.,]+)$/i", $value)){ return ERR_VALIDATE_NUMBER; }
        return true;
    }

    public function validate_color($value){
        if (empty($value)) { return true; }
        if (!is_string($value)) { return ERR_VALIDATE_INVALID; }
        $value = ltrim($value, '#');
        if (ctype_xdigit($value) && (strlen($value) == 6 || strlen($value) == 3)){
            return true;
        }
        return ERR_VALIDATE_INVALID;
    }

    public function validate_regexp($regexp, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string','double')) || !preg_match($regexp, $value)){ return ERR_VALIDATE_REGEXP; }
        return true;
    }

    public function validate_unique($table_name, $field_name, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string','double'))) { return ERR_VALIDATE_INVALID; }
        $result = $this->cms_core->db->isFieldUnique($table_name, $field_name, $value);
        if (!$result) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_unique_exclude($table_name, $field_name, $exclude_row_id, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string','double'))) { return ERR_VALIDATE_INVALID; }
        $result = $this->cms_core->db->isFieldUnique($table_name, $field_name, $value, $exclude_row_id);
        if (!$result) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_unique_ctype_field($ctype_name, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string'))) { return ERR_VALIDATE_INVALID; }
        $content_model = cmsCore::getModel('content');
        $table_name = $content_model->table_prefix . $ctype_name;
        if ($content_model->db->isFieldExists($table_name, $value)) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_unique_ctype_dataset($ctype_id, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string'))) { return ERR_VALIDATE_INVALID; }
        $value = $this->cms_core->db->escape($value);
        if(is_numeric($ctype_id)){
            $where = "ctype_id='{$ctype_id}' AND name='{$value}'";
        } else {
            $where = "target_controller='{$ctype_id}' AND name='{$value}'";
        }
        $result = !$this->cms_core->db->getRow('content_datasets', $where);
        if (!$result) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_date($value){

        if (empty($value)) { return true; }

        if (!is_array($value)){

            $time = strtotime($value);

            if ($time !== false){
                return true;
            }

        }

        return ERR_VALIDATE_INVALID;

    }

    public function validate_date_range($value){

        if (empty($value)) { return true; }

        if (!empty($value['date']) && !is_array($value['date'])){

            if(isset($value['hours']) && isset($value['mins']) &&
                    !is_array($value['hours']) && !is_array($value['mins'])){
                return $this->validate_date(sprintf('%s %02d:%02d', $value['date'], $value['hours'], $value['mins']));
            }

        } elseif(!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])){

                if(is_array($value['from'])){
                    return ERR_VALIDATE_INVALID;
                }

                if($this->validate_date($value['from']) !== true){
                    return ERR_VALIDATE_INVALID;
                }

            }

            if (!empty($value['to'])){

                if(is_array($value['to'])){
                    return ERR_VALIDATE_INVALID;
                }

                if($this->validate_date($value['to']) !== true){
                    return ERR_VALIDATE_INVALID;
                }

            }

            return true;

        }

        return ERR_VALIDATE_INVALID;

    }

}
