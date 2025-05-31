<?php
/**
 * Основной класс всех контроллеров
 *
 * @property \cmsConfig $cms_config
 * @property \cmsCore $cms_core
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 * @property \cmsRequest $request
 */
#[\AllowDynamicProperties]
class cmsController {

    use icms\traits\corePropertyLoadable;

    private static $controllers;
    private static $mapping;

    private static $lang_init = [];

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

    protected $useOptions = false;

    protected $active_filters = [];

    /**
     * Неизвестные экшены определять
     * как первый параметр экшена index
     * @var boolean
     */
    protected $unknown_action_as_index_param = false;

    public function __construct(cmsRequest $request) {

        self::loadControllers();

        $this->name = $this->name ? $this->name : strtolower(get_called_class());

        $this->root_url = $this->name;

        $this->root_path = $this->cms_config->root_path . 'system/controllers/' . $this->name . '/';

        $this->setRequest($request)->loadModel()->loadLanguage();

        if ($this->useOptions) {
            $this->options = $this->getOptions();
        }
    }

    protected function loadLanguage() {

        // Если вдруг сменили язык в хуках, а ланг файл подключили ранее
        if (empty(self::$lang_init[$this->name])) {

            cmsCore::loadControllerLanguage($this->name);

            self::$lang_init[$this->name] = true;
        }

        $this->title = string_lang('LANG_' . $this->name . '_CONTROLLER', $this->name);

        return $this;
    }

    protected function loadModel() {

        // Контроллер сам решает, какая модель ему нужна
        if ($this->outer_controller_model) {

            $this->model = cmsCore::getModel($this->outer_controller_model);

            return $this;
        }

        // Мы в бэкенде?
        if ($this instanceof cmsBackend) {
            if (cmsCore::isModelExists($this->name . '/backend')) {

                $this->model = cmsCore::getModel('backend_' . $this->name);

                return $this;
            }
        }

        if ($this->model === null && cmsCore::isModelExists($this->name)) {
            $this->model = cmsCore::getModel($this->name);
        } else {
            unset($this->model);
        }

        return $this;
    }

    public function setRequest(cmsRequest $request) {

        $this->request = $request;

        return $this;
    }

    public function setRootURL($root_url) {

        $this->root_url = $root_url;

        return $this;
    }

//============================================================================//
//============================================================================//

    public function setListContext($list_type) {

        $this->list_type = $list_type;

        return $this;
    }

    public function getListContext() {
        return $this->list_type;
    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает опции текущего контроллера
     *
     * @return array
     */
    public function getOptions() {
        return self::loadOptions($this->name);
    }

    /**
     * Устанавливает опцию контроллера
     *
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function setOption($key, $val) {
        $this->options[$key] = $val;
        return $this;
    }

    /**
     * Возвращает значение опции контроллера
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key) {
        if (!$this->useOptions) {
            return null;
        }
        return array_key_exists($key, $this->options) ? $this->options[$key] : null;
    }

    /**
     * Проверяет включен ли текущий контроллер
     *
     * @return boolean
     */
    public function isEnabled() {
        return $this->isControllerEnabled($this->name);
    }

    /**
     * Проверяет, установлен ли контроллер записью в БД
     *
     * @param string $name Имя контроллера
     * @return boolean
     */
    public function isControllerInstalled($name) {
        return isset(self::$controllers[$name]);
    }

    /**
     * Проверяет, есть ли у контроллера псевдоним
     * если есть, возвращает его
     *
     * @return mixed
     */
    public function hasSlug() {
        return !empty(self::$controllers[$this->name]['slug']) ? self::$controllers[$this->name]['slug'] : false;
    }

    /**
     * Проверяет, включен ли контроллер
     *
     * @param string $name Имя контроллера
     * @return boolean
     */
    public function isControllerEnabled($name) {

        // проверяем только те, которые зарегистрированы в базе
        if (isset(self::$controllers[$name]['is_enabled'])) {
            return self::$controllers[$name]['is_enabled'];
        }

        return cmsCore::isControllerExists($name);
    }

    /**
     * Проверяет, включен ли контроллер
     *
     * @param string $name Имя контроллера
     * @return boolean
     */
    public static function enabled($name) {
        self::loadControllers();
        return self::$controllers[$name]['is_enabled'] ?? cmsCore::isControllerExists($name);
    }

    /**
     * Загружает и возвращает массив соответствий
     * имён контроллеров и их псевдонимов
     *
     * @return array
     */
    public static function getControllersMapping() {

        if (self::$mapping !== null) {
            return self::$mapping;
        }

        self::$mapping = [];

        self::loadControllers();

        if (self::$controllers) {
            foreach (self::$controllers as $controller) {
                if (!empty($controller['slug'])) {
                    self::$mapping[$controller['name']] = $controller['slug'];
                }
            }
        }

        return self::$mapping;
    }

    /**
     * Загружает данные всех контроллеров
     *
     * @return boolean
     */
    private static function loadControllers() {

        if (!isset(self::$controllers)) {

            $model = new cmsModel();

            $model->selectList([
                'i.id'         => 'id',
                'i.is_enabled' => 'is_enabled',
                'i.options'    => 'options',
                'i.name'       => 'name',
                'i.slug'       => 'slug'
            ], true);

            self::$controllers = $model->useCache('controllers')->get('controllers', function ($item, $model) {
                $item['options'] = cmsModel::yamlToArray($item['options']);
                return $item;
            }, 'name') ?: [];

            return true;
        }

        return false;
    }

    /**
     * Загружает опции контроллера
     *
     * @param string $controller_name Имя контроллера
     * @return array
     */
    public static function loadOptions($controller_name) {

        self::loadControllers();

        if (isset(self::$controllers[$controller_name]['options'])) {
            return (array)self::$controllers[$controller_name]['options'];
        }

        return [];
    }

    /**
     * Сохраняет опции контроллера
     *
     * @param string $controller_name Имя контроллера
     * @param array $options Массив опция для сохранения
     * @return boolean
     */
    public static function saveOptions($controller_name, $options) {

        $model = new cmsModel();

        $model->filterEqual('name', $controller_name);

        $result = $model->updateFiltered('controllers', ['options' => $options]);

        cmsCache::getInstance()->clean('controllers');

        return $result;
    }

//============================================================================//
//============================================================================//

    //
    // ХУКИ
    //

    /**
     * Вызывается до начала работы экшена
     */
    public function before($action_name) {

        $this->cms_template->setContext($this);

        return true;
    }

    /**
     * Вызывается после работы экшена
     */
    public function after($action_name) {

        $this->cms_template->restoreContext();

        return true;
    }

    /**
     * Вызывается до начала работы хука
     */
    public function beforeHook($event_name) {

        if ($this->useOptions) {
            $this->options = $this->getOptions();
        }

        return true;
    }

    /**
     * Вызывается после работы хука
     */
    public function afterHook($event_name) {
        return true;
    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет существование экшена
     *
     * @param string $action_name Имя экшена
     * @return boolean
     */
    public function isActionExists($action_name) {

        $method_name = 'action' . string_to_camel('_', $action_name);

        if (method_exists($this, $method_name)) {
            return true;
        }

        $action_file = $this->getExternalActionPath($action_name);

        if (is_readable($action_file)) {
            return true;
        }

        return false;
    }

    /**
     * Запускает требуемый экшен
     *
     * @param string $action_name Имя экшена
     * @param array $params Параметры в метод запуска экшена
     * @return mixed
     */
    public function runAction($action_name, $params = []) {

        if ($this->before($action_name) === false) {
            return false;
        }

        $this->current_params = $params;

        $action_name = $this->routeAction($action_name);

        $result = $this->executeAction($action_name, $this->current_params);

        $this->after($action_name);

        return $result;
    }

    /**
     * Находит и выполняет требуемый экшен
     *
     * @param string $action_name Имя экшена
     * @param array $params Параметры в метод запуска экшена
     * @return mixed
     */
    public function executeAction($action_name, $params = []) {

        $method_name = 'action' . string_to_camel('_', $action_name);

        // проверяем наличие экшена его в отдельном файле
        $action_file = $this->getExternalActionPath($action_name);

        if (is_readable($action_file)) {

            // вызываем экшен из отдельного файла
            $result = $this->runExternalAction($action_name, $params);
        } else {

            // Если файла нет, ищем метод класса
            if (method_exists($this, $method_name)) {

                if (!$this->validateParamsCount($this, $method_name, $params)) {
                    return cmsCore::error404();
                }

                // сохраняем название текущего экшена
                $this->setCurrentAction($action_name);

                // если есть нужный экшен, то вызываем его
                $result = call_user_func_array([$this, $method_name], $params);
            } else {

                // если нет экшена в отдельном файле,
                // проверяем метод route()
                if (method_exists($this, 'route')) {

                    $route_uri = $action_name;
                    if ($params) {
                        $route_uri .= '/' . implode('/', $params);
                    }
                    $result = call_user_func([$this, 'route'], $route_uri);
                } else {

                    // если метода route() тоже нет,
                    // то 404
                    return cmsCore::error404();
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
     *
     * @param string $action_name Название экшена
     * @param array $params Параметры
     * @return mixed
     */
    public function runExternalAction($action_name, $params = []) {

        $action_file = $this->getExternalActionPath($action_name);

        $class_name = 'action' . string_to_camel('_', $this->name) . string_to_camel('_', $action_name);

        if (!is_readable($action_file)) {
            return cmsCore::error(ERR_FILE_NOT_FOUND . ': ' . str_replace(PATH, '', $action_file));
        }

        include_once $action_file;

        if (!class_exists($class_name, false)) {
            return cmsCore::error(sprintf(ERR_CLASS_NOT_DEFINED, str_replace(PATH, '', $action_file), $class_name));
        }

        if (!$this->validateParamsCount($class_name, 'run', $params)) {
            return cmsCore::error404();
        }

        // сохраняем название текущего экшена
        $this->setCurrentAction($action_name);

        $action_object = new $class_name($this, $params);

        // проверяем разрешен ли прямой вызов экшена
        if ($action_object->lock_explicit_call === true && $this->lock_explicit_call !== false && !$this->request->isInternal()) {
            return cmsCore::error404();
        }

        // проверяем параметры если нужно
        $params_error = $this->validateRequestParams($action_object);
        if ($params_error !== false) {

            if ($this->request->isAjax()) {

                return $this->cms_template->renderJSON([
                    'error'   => true,
                    'errors'  => $params_error,
                    'message' => sprintf(LANG_REQUEST_PARAMS_ERROR, implode(', ', array_keys($params_error)))
                ]);

            } else {
                return cmsCore::error(LANG_ERROR, sprintf(LANG_REQUEST_PARAMS_ERROR, implode(', ', array_keys($params_error))));
            }
        }

        if ($action_object->before() === false) {
            return false;
        }

        $result = call_user_func_array([$action_object, 'run'], $params);

        $action_object->after();

        return $result;
    }

    /**
     * Проверяет параметры запроса, если они заданы
     *
     * @param cmsAction $action_object
     * @return false|array False или массив ошибок
     */
    public function validateRequestParams($action_object) {

        if (empty($action_object->request_params)) {
            return false;
        }

        $errors = [];

        // валидация аналогична валидации форм
        foreach ($action_object->request_params as $param_name => $rules) {

            $value = $this->request->get($param_name, null);

            if (is_null($value) && isset($rules['default'])) {

                $value = $rules['default'];

                $this->request->set($param_name, $value);

            } elseif (!is_null($value) && isset($rules['default'])) {

                $value = $this->request->get($param_name, $rules['default']);

                // для применения типизации переменной
                $this->request->set($param_name, $value);
            }

            if (empty($rules['rules'])) {
                continue;
            }

            foreach ($rules['rules'] as $rule) {

                if (!$rule) { continue; }

                $validate_function = "validate_{$rule[0]}";

                $rule[] = $value;

                unset($rule[0]);

                $result = call_user_func_array([$action_object, $validate_function], $rule);

                if ($result !== true) {
                    $errors[$param_name] = $result;
                    break;
                }
            }
        }

        if (!$errors) { return false; }

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
    public function runHook($event_name, $params = [], $default = null) {

        if ($this->beforeHook($event_name) === false) {
            return false;
        }

        $method_name = 'on' . string_to_camel('_', $event_name);

        if (method_exists($this, $method_name)) {

            // если есть нужный хук, то вызываем его
            $result = call_user_func_array([$this, $method_name], $params);

        } else {

            // если метода хука нет, проверяем наличие его в отдельном файле
            $hook_file = $this->root_path . 'hooks/' . $event_name . '.php';

            if (is_readable($hook_file)) {

                // вызываем хук из отдельного файла
                $result = $this->runExternalHook($event_name, $params);

            } else {

                // хука нет вообще, возвращаем данные запроса без изменений
                if ($default === null) {
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
    public function runExternalHook($event_name, $params = []) {

        $class_name = 'on' . string_to_camel('_', $this->name) . string_to_camel('_', $event_name);

        if (!class_exists($class_name, false)) {

            $hook_file = $this->root_path . 'hooks/' . $event_name . '.php';

            if (!is_readable($hook_file)) {
                cmsCore::error(ERR_FILE_NOT_FOUND . ': ' . str_replace(PATH, '', $hook_file));
            }

            include_once $hook_file;
        }

        $hook_object = new $class_name($this);

        return call_user_func_array([$hook_object, 'run'], $params);
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
     * Собирает форму из коллбэка
     * И применяет хук form_make
     *
     * @param callable $callback
     * @return cmsForm
     */
    public function makeForm(callable $callback) : cmsForm {

        $form = $callback(new cmsForm());

        return cmsEventsManager::hook('form_make', $form);
    }

    /**
     * Загружает и возвращает описание структуры формы
     * в контексте текущего контроллера
     * Для бэкенда метод переопределён в cmsBackend
     *
     * @param string $form_name Название формы
     * @param array $params Параметры формы
     * @param string $path_prefix Префикс путь к файлу формы относительно директории контроллера
     * @return \cmsForm
     */
    public function getForm($form_name, $params = false, $path_prefix = '') {
        return $this->getControllerForm($this->name, $form_name, $params, $path_prefix);
    }

    /**
     * Загружает и возвращает описание структуры формы
     *
     * @param string $controller_name Название контроллера
     * @param string $form_name Название формы
     * @param array $params Параметры формы
     * @param string $path_prefix Префикс путь к файлу формы относительно директории контроллера
     * @return \cmsForm
     */
    public function getControllerForm($controller_name, $form_name, $params = false, $path_prefix = '') {

        $form_file  = $this->cms_config->root_path . 'system/controllers/' . $controller_name . '/'.$path_prefix.'forms/form_' . $form_name . '.php';
        $_form_name = $controller_name . $form_name;

        // $this всё равно передаётся текущего контроллера
        // $this->name и $controller_name могут быть различные
        $form = cmsForm::getForm($form_file, $_form_name, $params, $this);

        if ($form === false) {
            cmsCore::error(ERR_FILE_NOT_FOUND . ': ' . str_replace(PATH, '', $form_file));
        }

        if (is_string($form)) {
            return cmsCore::error($form);
        }

        list($form_context, $form, $params) = cmsEventsManager::hook('form_get', [[$this->name, $form_name], $form, $params]);
        list($form, $params) = cmsEventsManager::hook('form_' . $controller_name . '_' . $form_name, [$form, $params]);

        return $form;
    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает описание структуры grid таблицы
     *
     * @param string $grid_name Название
     * @param ?array $params Параметры для передачи в функцию описания грида
     * @param type $ups_key Ключ UPS
     * @return \cmsGrid
     */
    public function loadDataGrid($grid_name, $params = null, $ups_key = '') : cmsGrid {

        $grid = new cmsGrid($this, $grid_name, $params);

        if (!$grid->isLoaded()) {

            return cmsCore::error($this->grid->getError());
        }

        if ($ups_key) {

            $filter = [];

            $pre_filter = cmsUser::getUPSActual($ups_key, $this->request->get('filter', ''));

            if ($pre_filter) {
                parse_str($pre_filter, $filter);
            }

            if ($filter) {
                $grid->addToFilter($filter);
            }
        }

        return $grid;
    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает массив маршрутов контроллера
     *
     * @return array
     */
    public function loadRoutes() {

        $file = $this->root_path . 'routes.php';

        if (!is_readable($file)) {
            return [];
        }

        include_once($file);

        $routes = call_user_func('routes_' . $this->name);

        if (!is_array($routes)) {
            return [];
        }

        return cmsEventsManager::hook($this->name . '_routes', $routes);
    }

//============================================================================//
//============================================================================//

    public function halt($text = '') {
        $this->cms_core->response->setContent($text)->sendAndExit();
    }

    public function getUniqueKey($params) {
        array_unshift($params, $this->name);
        return implode('.', $params);
    }

    public function prepareItemSeo($item, $fields, $ctype) {

        // Вызываем хуки и обновляем данные
        list($ctype, $fields, $item) = cmsEventsManager::hook(
                ['prepare_item_seo', 'prepare_item_' . $ctype['name'] . '_seo'],
                [$ctype, $fields, $item]
            );

        $_item = $item;

        foreach ($fields as $field) {

            $field_name = $field['name'];

            $field_value = $item[$field_name] ?? null;

            if (is_empty_value($field_value)) {

                $_item[$field_name] = null;
                continue;
            }

            // Преобразуем значение поля в строку
            $str_value = strip_tags($field['string_value'] ?? $field['handler']->setItem($item)->getStringValue($field_value));

            // Убираем шорткоды
            $_item[$field_name] = preg_replace('#{[a-z]{1}[a-z0-9_]*\:[a-z0-9\:_]+}#i', '', $str_value);
        }

        // Объединяем теги в строку, если они заданы
        if (!empty($item['tags']) && is_array($item['tags'])) {
            $_item['tags'] = implode(', ', $item['tags']);
        }

        if(!isset($item['category']['title']) && !empty($item['category_id'])){
            $item['category'] = $this->model->getCategory($ctype['name'], $item['category_id']);
        }

        $_item['category'] = $item['category']['title'] ?? null;

        return $_item;
    }

//============================================================================//
//============================================================================//

    /**
     * Позволяет переопределить экшен перед вызовом
     *
     * @param string $action_name
     * @return string
     */
    public function routeAction($action_name) {

        // Избавляемся от index в url
        if ($this->unknown_action_as_index_param && !$this->isActionExists($action_name)) {

            array_unshift($this->current_params, $action_name);

            $action_name = 'index';
        }

        return $action_name;
    }

//============================================================================//
//============================================================================//

    /**
     * Определяет экшен, по списку маршрутов из файла routes.php контроллера
     *
     * @param string $uri Проверяемый URI
     * @return string|false Имя экшена или false
     */
    public function parseRoute($uri) {

        $routes = $this->loadRoutes();

        //перебираем все маршруты
        foreach ($routes as $route) {

            $matches = [];

            if (preg_match($route['pattern'], $uri, $matches)) {

                $action_name = $route['action'];

                // удаляем шаблон и экшен из параметров маршрута,
                // чтобы не мешали при переборе параметров запроса
                unset($route['pattern'], $route['action']);

                // Заполняем параметры запроса
                foreach ($route as $key => $value) {
                    //Если ключ - целое число, то значением является сегмент URI
                    if (is_int($key)){

                        $this->request->set($value, $matches[$key] ?? null);

                    //иначе, значение берется из маршрута
                    } else {
                        $this->request->set($key, $value);
                    }
                }

                return $action_name;
            }
        }

        // В маршруте нет совпадений
        return false;
    }

//============================================================================//
//============================================================================//

    /**
     * Редирект на указанный адрес
     *
     * @param string $url URL для редиректа
     * @param integer $code HTTP код
     */
    public function redirect($url, $code = 303) {

        list($url, $code) = cmsEventsManager::hook('redirect', [$url, $code]);

        if ($this->request->isAjax()) {

            $this->cms_core->response->setContent($this->cms_template->getRenderedAsset('ui/redirect_continue', [
                'redirect_url' => $url
            ]));

        } else {

            $this->cms_core->response->redirect($url, $code);
        }

        return $this->cms_core->response->sendAndExit();
    }

    /**
     * Выполняет редирект на страницу авторизации
     *
     * @param string $back_url
     * @return void
     */
    public function redirectToLogin(string $back_url = '') {

        if (!$back_url) {
            $back_url = rel_to_href($this->cms_core->uri_before_remap) . ($this->cms_core->uri_query ? '?' . http_build_query($this->cms_core->uri_query) : '');
        }

        return $this->redirectTo('auth', 'login', [], ['back' => $back_url]);
    }

    /**
     * Редирект на главную страницу
     */
    public function redirectToHome() {
        $this->redirect(href_to_home());
    }

    /**
     * Редирект на другой контроллер
     *
     * @param string $controller Имя контроллера
     * @param string $action Имя экшена
     * @param array|string $params Дополнительные параметры
     * @param array $query Параметры строки запроса
     * @param integer $code Код редиректа
     */
    public function redirectTo($controller, $action = '', $params = [], $query = [], $code = 303) {

        if($action === 'index'){
            $action = '';
        }

        $location = href_to($controller, $action, $params, $query);

        $this->redirect($location, $code);
    }

    /**
     * Редирект на собственный экшен
     * текущего контроллера
     *
     * @param string $action Имя экшена
     * @param array|string $params Дополнительные параметры
     * @param array $query Параметры строки запроса
     */
    public function redirectToAction($action = '', $params = [], $query = []) {
        $this->redirectTo($this->root_url, $action, $params, $query);
    }

    /**
     * Возвращает предыдущий URL текущего сайта
     * @return string
     */
    public function getBackURL() {

        $back_url = href_to_home();

        $referer = $this->request->getHeader('REFERER');

        if ($referer && strpos($referer, 'http') === 0) {

            if (strpos($referer, $this->cms_config->host) === 0 &&
                    strpos($referer, '"') === false && strpos($referer, "'") === false) {
                $back_url = $referer;
            }
        }

        return $back_url;
    }

    /**
     * Возвращает значение параметра back из запроса
     * Очищает и валидирует
     *
     * @param string $default URL, если параметр пустой
     * @return string
     */
    public function getRequestBackUrl($default = '') {

        $back_url = trim(strip_tags($this->request->get('back', '')));

        // Первый символ должен быть слэш. Только внутренние страницы
        if (!preg_match('/^\/[a-z0-9_]*/i', $back_url)) {
            $back_url = '';
        }

        return $back_url ? $back_url : $default;
    }

    /**
     * Редирект на предыдущий URL
     * по HTTP_REFERER
     */
    public function redirectBack() {
        return $this->redirect($this->getBackURL());
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает список субъектов к которым применяются права пользователей
     * @return array
     */
    public function getPermissionsSubjects() {
        return [
            [
                'name'  => $this->name,
                'title' => $this->title
            ]
        ];
    }

    public function getContentTypeForModeration($name) {
        return [
            'id'    => null,
            'name'  => $this->name,
            'title' => $this->title
        ];
    }

//============================================================================//
//============================================================================//

    public function validate_required($value) {
        if(is_empty_value($value)){
            return ERR_VALIDATE_REQUIRED;
        }
        return true;
    }

    public function validate_min($min, $value) {

        if (($result = $this->validate_number($value)) !== true) {
            return $result;
        }

        if (floatval($value) < $min) {
            return sprintf(ERR_VALIDATE_MIN, $min);
        }

        return true;
    }

    public function validate_max($max, $value) {

        if (($result = $this->validate_number($value)) !== true) {
            return $result;
        }

        if (floatval($value) > $max) {
            return sprintf(ERR_VALIDATE_MAX, $max);
        }

        return true;
    }

    /**
     * Валидация float чисел: минимум
     * Требуется библиотека bcmath
     *
     * @param float $min Минимальное число
     * @param mixed $value Значение для валидации
     * @return boolean
     */
    public function validate_minfloat($min, $value) {

        if (($result = $this->validate_number($value)) !== true) {
            return $result;
        }

        $value = bc_format(str_replace(',', '.', strval($value)));
        $min   = bc_format($min);

        if (bccomp($min, $value) === 1) {
            return sprintf(ERR_VALIDATE_MIN, $min);
        }

        return true;
    }

    /**
     * Валидация float чисел: максимум
     * Требуется библиотека bcmath
     *
     * @param float $max Максимальное число
     * @param mixed $value Значение для валидации
     * @return boolean
     */
    public function validate_maxfloat($max, $value) {

        if (($result = $this->validate_number($value)) !== true) {
            return $result;
        }

        $value = bc_format(str_replace(',', '.', strval($value)));
        $max   = bc_format($max);

        if (bccomp($max, $value) === -1) {
            return sprintf(ERR_VALIDATE_MAX, $max);
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
        if ($value === null || $value === false) { return true; }
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

    public function validate_sysname($value) {
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match('/^([a-z0-9\_]*[a-z]+[a-z0-9\_]*)$/', $value)) {
            return ERR_VALIDATE_SYSNAME;
        }
        return true;
    }

    public function validate_phone($value) {
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match('/^([0-9\-\+\(\)\s]*)$/', $value)) {
            return ERR_VALIDATE_INVALID;
        }
        return true;
    }

    public function validate_slug_segment($value) {
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match('/^([a-z0-9\-]*[a-z]+[a-z0-9\-]*)$/', $value)) {
            return ERR_VALIDATE_SLUGS;
        }
        return true;
    }

    public function validate_slug($value) {
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match('/^([a-z0-9\-\/]*)$/', $value)) {
            return ERR_VALIDATE_SLUG;
        }
        return true;
    }

    public function validate_digits($value) {
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), ['integer', 'string', 'double']) || !preg_match('/^([0-9]+)$/', strval($value))) {
            return ERR_VALIDATE_DIGITS;
        }
        return true;
    }

    public function validate_number($value) {

        if (!$value) {
            return true;
        }

        $type = gettype($value);

        if (!in_array($type, ['integer', 'string', 'double'])) {
            return ERR_VALIDATE_INVALID;
        }

        if ($type === 'string' && !preg_match('/^-?[0-9]+([.,][0-9]+)?$/', $value)) {
            return ERR_VALIDATE_NUMBER;
        }

        return true;
    }

    public function validate_color($value) {
        if (empty($value)) { return true; }
        if (!is_string($value)) { return ERR_VALIDATE_INVALID; }
        if (strlen($value) <= 7) {
            $value = ltrim($value, '#');
            if (ctype_xdigit($value) && (strlen($value) == 6 || strlen($value) == 3)) {
                return true;
            }
        } else {
            if (preg_match('/^rgba\((\s*\d+\s*,){3} [\d\.]+\)$/i', $value)) {
                return true;
            }
        }
        return ERR_VALIDATE_INVALID;
    }

    public function validate_regexp($regexp, $value, $set_error_text = null) {

        $error_text = false;

        // Если передан, значит $value это $set_error_text, а $set_error_text это $value
        // Сделано для совместимости, т.к. в валидаторы
        // Значения передются последним аргументом
        if($set_error_text !== null){
            $error_text = $value;
            $value = $set_error_text;
        }

        if (empty($value)) {
            return true;
        }

        if (!in_array(gettype($value), ['integer', 'string', 'double']) || !preg_match($regexp, strval($value))) {
            return $error_text ? $error_text : ERR_VALIDATE_REGEXP;
        }

        return true;
    }

    public function validate_unique($table_name, $field_name, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), ['integer', 'string', 'double'])) { return ERR_VALIDATE_INVALID; }
        $result = $this->cms_core->db->isFieldUnique($table_name, $field_name, $value);
        if (!$result) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_unique_exclude($table_name, $field_name, $exclude_row_id, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), ['integer', 'string', 'double'])) { return ERR_VALIDATE_INVALID; }
        $result = $this->cms_core->db->isFieldUnique($table_name, $field_name, $value, $exclude_row_id);
        if (!$result) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_unique_ctype_field($ctype_name, $value){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), ['integer', 'string'])) { return ERR_VALIDATE_INVALID; }
        $content_model = cmsCore::getModel('content');
        $table_name = $content_model->table_prefix . $ctype_name;
        if ($content_model->db->isFieldExists($table_name, $value)) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

    public function validate_unique_ctype_dataset($ctype_id, $exclude_row_id = null, $value = null){
        if (empty($value)) { return true; }
        if (!in_array(gettype($value), ['integer', 'string'])) { return ERR_VALIDATE_INVALID; }
        $value = $this->cms_core->db->escape($value);
        if(is_numeric($ctype_id)){
            $where = "ctype_id='{$ctype_id}' AND name='{$value}'";
        } else {
            $where = "target_controller='{$ctype_id}' AND name='{$value}'";
        }
        if ($exclude_row_id) { $where .= " AND (id <> '{$exclude_row_id}')"; }
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
