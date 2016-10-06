<?php
class cmsController {

    private static $controllers;

    public $name;
    public $title;
	public $model = null;
    public $request;
    public $current_action;
    public $current_params;
    public $options;
    public $root_url;
    public $root_path;

    /**
     * Флаг наличия SEO параметров для index экшена
     * @var bool
     */
    public $useSeoOptions = false;

    /**
     * Флаг блокировки прямого вызова экшена
     * полезно если название экшена переопределяется
     * а вызов экшена напрямую нужно запретить
     * @var bool || null
     */
    public $lock_explicit_call = null;

    protected $callbacks = array();
    protected $useOptions = false;

    function __construct($request){

        self::loadControllers();

        $this->name = $this->name ? $this->name : mb_strtolower(get_called_class());

        $this->root_url = $this->name;

        $this->root_path = $this->cms_config->root_path . 'system/controllers/' . $this->name . '/';

        $this->request = $request;

        cmsCore::loadControllerLanguage($this->name);

        $title_constant = 'LANG_'.strtoupper($this->name).'_CONTROLLER';

        $this->title = defined($title_constant) ? constant($title_constant) : $this->name;

        if (cmsCore::isModelExists($this->name)){
            $this->model = cmsCore::getModel($this->name);
        }

        if ($this->useOptions){
            $this->options = $this->getOptions();
        }

        $this->loadCallback();

    }

    /////////////////    Набор методов для коллбэков    ////////////////////////
    /**
     * Этот метод переопределяется в дочерних классах
     * где задается начальный набор функций, которые будут применены в коллбэках
     */
    public function loadCallback() { $this->callbacks = array(); }
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

        if(strpos($name, 'cms') === 0){

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

        return null;

    }

    public function setRootURL($root_url){
        $this->root_url = $root_url;
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

    /**
     * Проверяет включен ли текущий контроллер
     * @return boolean
     */
    public function isEnabled() {
        return $this->isControllerEnabled($this->name);
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

    private static function loadControllers() {

        if(!isset(self::$controllers)){

            $model = new cmsModel();

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

        $action_file = $this->root_path . 'actions/' . $action_name.'.php';

        if (is_readable($action_file)){
            return true;
        }

        return false;

    }

    /**
     * Находит и запускает требуемый экшен
     * @param string $action_name
     * @param array $params
     */
    public function runAction($action_name, $params = array()){

        if ($this->before($action_name) === false) { return false; }

        $this->current_params = $params;

        $action_name = $this->routeAction($action_name);

        $method_name = 'action' . string_to_camel('_', $action_name);

        // проверяем наличие экшена его в отдельном файле
        $action_file = $this->root_path . 'actions/' . $action_name.'.php';

        if(is_readable($action_file)){

            // вызываем экшен из отдельного файла
            $result = $this->runExternalAction($action_name, $this->current_params);

        } else {

            // Если файла нет, ищем метод класса
            if (method_exists($this, $method_name)){

                // проверяем максимальное число аргументов экшена
                if ($this->name != 'admin'){
                    $rf = new ReflectionMethod($this, $method_name);
                    $max_params = $rf->getNumberOfParameters();
                    if ($max_params < count($this->current_params)) { cmsCore::error404(); }
                }

                // если есть нужный экшен, то вызываем его
                $result = call_user_func_array(array($this, $method_name), $this->current_params);

            } else {

                // если нет экшена в отдельном файле,
                // проверяем метод route()
                if(method_exists($this, 'route')){

                    $route_uri = $action_name;
                    if ($this->current_params) { $route_uri .= '/' . implode('/', $this->current_params); }
                    $result = call_user_func(array($this, 'route'), $route_uri);

                } else {

                    // если метода route() тоже нет,
                    // то 404
                    cmsCore::error404();

                }

            }

        }

        $this->after($action_name);

        return $result;

    }

//============================================================================//
//============================================================================//

    /**
     * Выполняет экшен, находящийся в отдельном файле ./actions/$action_name.php
     * @param str $action_name
     */
    public function runExternalAction($action_name, $params = array()){

        $action_file = $this->root_path . 'actions/'.$action_name.'.php';

        $class_name = 'action' . string_to_camel('_', $this->name) . string_to_camel('_', $action_name);

        include_once $action_file;

        // проверяем максимальное число аргументов экшена
        if ($this->name != 'admin'){
            $rf = new ReflectionMethod($class_name, 'run');
            $max_params = $rf->getNumberOfParameters();
            if ($max_params < count($params)) { cmsCore::error404(); }
        }

        $action_object = new $class_name($this, $params);

        // проверяем разрешен ли прямой вызов экшена
        if($action_object->lock_explicit_call === true && $this->lock_explicit_call !== false && !$this->request->isInternal()){
            cmsCore::error404();
        }

        return call_user_func_array(array($action_object, 'run'), $params);

    }

//============================================================================//
//============================================================================//

    /**
     * Находит и запускает хук для указанного события
     * @param string $event_name
     */
    public function runHook($event_name, $params = array()){

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
                return $this->request->getData();

            }

        }

        $this->afterHook($event_name);

        return $result;

    }

//============================================================================//
//============================================================================//

    /**
     * Выполняет хук, находящийся в отдельном файле ./hooks/$event_name.php
     * @param str $event_name
     */
    public function runExternalHook($event_name, $params = array()){

        $class_name = 'on' . string_to_camel('_', $this->name) . string_to_camel('_', $event_name);

        if (!class_exists($class_name)){

            $hook_file = $this->root_path . 'hooks/' . $event_name . '.php';

            include_once $hook_file;

        }

        $hook_object = new $class_name($this);

        return call_user_func_array(array($hook_object, 'run'), $params);

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает описание структуры формы
     * @param type $form_name
     * @param type $params
     * @return cmsForm
     */
    public function getForm($form_name, $params=false, $path_prefix=''){

        $form_file = $this->root_path . $path_prefix . 'forms/form_' . $form_name . '.php';
        $form_name = $this->name . $form_name;

        return cmsForm::getForm($form_file, $form_name, $params);

    }

//============================================================================//
//============================================================================//

    /**
     * Загружает и возвращает описание структуры таблицы
     * @param string $grid_name
     */
    public function loadDataGrid($grid_name, $params = false, $ups_key = ''){

        $default_options = array(
            'order_by' => 'id',
            'order_to' => 'asc',
            'show_id' => true,
            'is_auto_init' => true,
            'is_sortable' => true,
            'is_filter' => true,
            'is_actions' => true,
            'is_pagination' => true,
            'is_toolbar' => true,
            'is_draggable' => false,
            'is_selectable' => false,
            'load_columns' => false
        );

        $grid_file = $this->root_path . 'grids/grid_' . $grid_name . '.php';

        if (!is_readable($grid_file)){ return false; }

        include($grid_file);

        $args = array($this);
        if ($params) {
            if (is_array($params)){ $args = array($this) + $params; }
            else { $args[] = $params; }
        }

        $grid = call_user_func_array('grid_'.$grid_name, $args);

        if (!isset($grid['options'])) {
            $grid['options'] = $default_options;
        } else {
            $grid['options'] = array_merge($default_options, $grid['options']);
        }

		$grid = cmsEventsManager::hook('grid_'.$this->name.'_'.$grid_name, $grid);

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

//============================================================================//
//============================================================================//

    /**
     * Позволяет переопределить экшен перед вызовом
     * @param string $action_name
     * @return string
     */
    public function routeAction($action_name){

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
     * @param str $url
     */
    public function redirect($url, $code=303){
        if ($code == 301){
            header('HTTP/1.1 301 Moved Permanently');
        } else {
            header('HTTP/1.1 303 See Other');
        }
        header('Location: '.$url);
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
     * @param str $controller
     * @param str $action
     * @param array $params
     * @param array $query
     */
    public function redirectTo($controller, $action='', $params=array(), $query=array()){

        $href_lang = cmsCore::getLanguageHrefPrefix();

        $location = $this->cms_config->root .($href_lang ? $href_lang.'/' : ''). $controller . '/' . $action;

        if ($params){ $location .= '/' . implode('/', $params); }
        if ($query){ $location .= '?' . http_build_query($query); }

        $this->redirect($location);

    }

    /**
     * Редирект на собственный экшен
     * @param str $controller
     * @param str $action
     * @param array $params
     * @param array $query
     */
    public function redirectToAction($action, $params=array(), $query=array()){

        if ($action=='index') {
            $location = $this->root_url;
        } else {
            $location = $this->root_url . '/' . $action;
        }

		if ($params){
			if (is_array($params)) { $location .= '/' . implode('/', $params); }
			else { $location .= '/' . $params; }
		}

        if ($query){ $location .= '?' . http_build_query($query); }

        $this->redirect(href_to($location));

    }

    /**
     * Возвращает предыдущий URL текущего сайта
     * @return str
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

//============================================================================//
//============================================================================//

    public function validate_required($value){
        if($value === '0'){ return true; }
        if (empty($value)) { return ERR_VALIDATE_REQUIRED; }
        return true;
    }

    public function validate_min($min, $value){
        if ((int)$value < $min) { return sprintf(ERR_VALIDATE_MIN, $min); }
        return true;
    }

    public function validate_max($max, $value){
        if ((int)$value > $max) { return sprintf(ERR_VALIDATE_MAX, $max); }
        return true;
    }

    public function validate_min_length($length, $value){
        if (empty($value)) { return true; }
        if (mb_strlen($value)<$length) { return sprintf(ERR_VALIDATE_MIN_LENGTH, $length); }
        return true;
    }

    public function validate_max_length($length, $value){
        if (empty($value)) { return true; }
        if (mb_strlen($value)>$length) { return sprintf(ERR_VALIDATE_MAX_LENGTH, $length); }
        return true;
    }

    public function validate_array_key($array, $value){
        if (is_array($value)) { return ERR_VALIDATE_INVALID; }
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
        if (!is_string($value) || !preg_match("/^([a-z0-9\._-]+)@([a-z0-9\._-]+)\.([a-z]{2,6})$/i", $value)){ return ERR_VALIDATE_EMAIL; }
        return true;
    }

    public function validate_alphanumeric($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match("/^([a-z0-9]*)$/i", $value)){ return ERR_VALIDATE_ALPHANUMERIC; }
        return true;
    }

    public function validate_sysname($value){
        if (empty($value)) { return true; }
        if (!is_string($value) || !preg_match("/^([a-z0-9\_]*)$/", $value)){ return ERR_VALIDATE_SYSNAME; }
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
        $ctype_id = (int)$ctype_id;
        $value = $this->cms_core->db->escape($value);
        $result = !$this->cms_core->db->getRow('content_datasets', "ctype_id='{$ctype_id}' AND name='{$value}'");
        if (!$result) { return ERR_VALIDATE_UNIQUE; }
        return true;
    }

//============================================================================//
//============================================================================//

}