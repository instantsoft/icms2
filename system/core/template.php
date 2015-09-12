<?php

class cmsTemplate {

    private static $instance;

    public $name;
    public $path;
    private $layout;
    private $output;
    private $options;

	private $head = array();
	private $head_main_css = array();
	private $head_css = array();
	private $head_main_js = array();
	private $head_js = array();
	private $head_js_no_merge = array();
	private $title;
	private $metadesc;
	private $metakeys;

    private $breadcrumbs = array();
    private $menus = array();

    private $widgets = array();
    private $widgets_group_index = 0;

    private $controller;
    private $controllers_queue;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ========================================================================== //
// ========================================================================== //

	function __construct($name=''){

		$config = cmsConfig::getInstance();

        $name = $name ? $name : $config->template;

        $this->setLayout('main');

		$this->head = array();
		$this->title = $config->sitename;

		$is_no_def_meta = isset($config->is_no_meta) ? $config->is_no_meta : false;

		if (!$is_no_def_meta){
			$this->metakeys = $config->metakeys;
			$this->metadesc = $config->metadesc;
		}

        $this->name = $name;
        $this->path = $config->root_path . 'templates/' . $name;

        $this->options = $this->getOptions();

	}

// ========================================================================== //
// ========================================================================== //

    public function isBody(){
        return !empty($this->output);
    }

	/**
	 * Выводит тело страницы
	 *
	 */
	public function body(){
		echo $this->output;
	}

	/**
	 * Выводит головные теги страницы
	 *
	 */
	public function head($is_seo_meta=true){

        if ($is_seo_meta){
			if (!empty($this->metakeys)){
				echo "\t". '<meta content="'.htmlspecialchars($this->metakeys).'" name="keywords">' . "\n";
			}
			if (!empty($this->metadesc)){
				echo "\t". '<meta content="'.htmlspecialchars($this->metadesc).'" name="description">' ."\n";
			}
        }

		foreach ($this->head as $id=>$tag){	echo "\t". $tag . "\n";	}

        if (!cmsConfig::get('merge_css')){
            foreach ($this->head_main_css as $id=>$file){	echo "\t". $this->getCSSTag($file) . "\n";	}
            foreach ($this->head_css as $id=>$file){	echo "\t". $this->getCSSTag($file) . "\n";	}
        } else {
            $tag = "\t". $this->getCSSTag( $this->getMergedCSSPath() ) . "\n";
            echo $tag;
        }

        if (!cmsConfig::get('merge_js')){
            foreach ($this->head_main_js as $id=>$file){	echo "\t". $this->getJSTag($file) . "\n";	}
            foreach ($this->head_js as $id=>$file){	echo "\t". $this->getJSTag($file) . "\n";	}
        } else {
            $tag = "\t". $this->getJSTag( $this->getMergedJSPath() ) . "\n";
            echo $tag;
            foreach ($this->head_js_no_merge as $id=>$file){	echo "\t". $this->getJSTag($file) . "\n";	}
        }

	}

	/**
	 * Выводит заголовок текущей страницы
	 * @param string $title
	 */
	public function title(){
		$config = cmsConfig::getInstance();
		if ($this->title){
			echo htmlspecialchars($this->title);
		} else {
			echo htmlspecialchars($config->sitename);
		}
	}

	/**
	 * Выводит название сайта
	 */
	public function sitename(){
		$config = cmsConfig::getInstance();
		echo htmlspecialchars($config->sitename);
	}

    /**
     * Выводит глобальный тулбар
     */
    public function toolbar(){
        if (!$this->isToolbar()){ return; }
        $this->menu('toolbar', false);
    }

    /**
     * Выводит виджеты на указанной позиции
     * @param string $position Название позиции
     * @param boolean $is_titles Выводить заголовки
     * @param string $wrapper Название шаблона обертки
     * @return boolean
     */
	public function widgets($position, $is_titles=true, $wrapper=''){

        if (!$this->hasWidgetsOn($position)){ return false; }

        foreach($this->widgets[$position] as $group){

            if (sizeof($group)==1){

                $widget = $group[0];
                if ($wrapper){ $widget['wrapper'] = $wrapper; }
                $tpl_file = $this->getTemplateFileName('widgets/' . $widget['wrapper']);
                include($tpl_file);

            } else {

                $widgets = $group;
                $tpl_file = $this->getTemplateFileName('widgets/wrapper_tabbed');
                include($tpl_file);

            }

        }

	}

    public function hasWidgetsOn($position){

        if (func_num_args() > 1){
            $positions = func_get_args();
        } else {
            $positions = array($position);
        }

        $has = false;

        foreach($positions as $pos){
            $has = $has || !empty($this->widgets[$pos]);
        }

        return $has;

    }

    /**
     * Выводит меню
     */
    public function menu($menu_name, $detect_active_id=true, $css_class='menu', $max_items=0, $is_allow_multiple_active=false){

        $core = cmsCore::getInstance();
        $config = cmsConfig::getInstance();

        if (!isset($this->menus[$menu_name])) {
            $menu_model = cmsCore::getModel('menu');
            $menu = $menu_model->getMenu($menu_name, 'name');
            if (!$menu){ return; }
            $items = $menu_model->getMenuItemsTree($menu['id']);
            if (!$items){ return; }
            $this->addMenuItems($menu_name, $items);
        }

        $active_ids = array();

        if ($detect_active_id){

            $current_url = trim($core->uri, '/');

            if ($menu_name == 'main'){
//                dump($this->menus[$menu_name]);
            }

            //перебираем меню в поисках текущего пункта
            foreach($this->menus[$menu_name] as $id=>$item){

                if (!isset($item['url']) && !isset($item['controller'])) { continue; }

                if (!isset($item['url'])) {
                    if (!isset($item['action'])) { $item['action'] = ''; }
                    if (!isset($item['params'])) { $item['params'] = array(); }
                    $item['url'] = href_to($item['controller'], $item['action'], $item['params']);
                    $this->menus[$menu_name][$id]['url'] = $item['url'];
                    $menu[$id] = $item;
                }

                $url = isset($item['url_mask']) ? $item['url_mask'] : $item['url'];
                $url = mb_substr($url, mb_strlen($config->root));
                $url = trim($url, '/');

                if (!$url) { continue; }

                //полное совпадение ссылки и адреса?
                if ($current_url == $url){
                    $active_ids[] = $id;
                    $is_strict = true;
                } else {

                    //частичное совпадение ссылки и адреса (по началу строки)?
                    $url_first_part = mb_substr($current_url, 0, mb_strlen($url));
                    if ($url_first_part == $url){
                        $active_ids[] = $id;
                        $is_strict = false;
                    }

                }

            }

        }

		if (!$is_allow_multiple_active && (count($active_ids)>1)){
			$active_ids = array($active_ids[count($active_ids)-1]);
		}

        $this->renderMenu($this->menus[$menu_name], $active_ids, $css_class, $max_items);

    }

    /**
     * Выводит глубиномер
     * @return <type>
     */
    public function breadcrumbs($options=array()){

        $config = cmsConfig::getInstance();

        $default_options = array(
            'home_url' => $config->host,
            'strip_last' => true
        );

        $options = array_merge($default_options, $options);

        if ($this->breadcrumbs){
            if ($options['strip_last']){
                unset($this->breadcrumbs[sizeof($this->breadcrumbs)-1]);
            } else {
                $this->breadcrumbs[sizeof($this->breadcrumbs)-1]['is_last'] = true;
            }
        }

        $this->renderAsset('ui/breadcrumbs', array(
            'breadcrumbs' => $this->breadcrumbs,
            'options' => $options
        ));

    }

    public function href_to($action, $params=false){

        if (!isset($this->controller->root_url)){
            return href_to($this->controller->name, $action, $params);
        } else {
            return href_to($this->controller->root_url, $action, $params);
        }

    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет переданный код к выводу
     * @param str $html
     */
    public function addOutput($html){
        $this->output .= $html;
    }

    /**
     * Принудительно печатает вывод
     */
    public function printOutput() {
        echo $this->output;
    }

// ========================================================================== //
// ========================================================================== //

	/**
	 * Устанавливает заголовок страницы
     * Если передано несколько аргументов, склеивает их в одну строку
     * через разделитель
     *
	 * @param string $pagetitle Заголовок
	 */
	public function setPageTitle($pagetitle){
		$config = cmsConfig::getInstance();
        if (func_num_args() > 1){ $pagetitle = implode(' - ', func_get_args()); }
        $this->title = $pagetitle;
		$this->title .= ' - '.$config->sitename;
	}

	public function setFrontPageTitle($pagetitle){
		$this->title = $pagetitle;
	}

	/**
	 * Устанавливает ключевые слова и описание страницы
	 * @param str $keywords
	 * @param str $description
	 */
	public function setMeta($keywords, $description){
		$this->metakeys = $keywords;
		$this->metadesc = $description;
	}

	/**
	 * Устанавливает ключевые слова страницы
	 * @param str $keywords
	 */
    public function setPageKeywords($keywords){
        $this->metakeys = $keywords;
    }

	/**
	 * Устанавливает описание страницы
	 * @param str $description
	 */
    public function setPageDescription($description){
        $this->metadesc = $description;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет кнопку на глобальный тулбар
     * @param array $button
     */
    public function addToolButton($button){

        $item = array(
            'title' => $button['title'],
            'url' => isset($button['href']) ? $button['href'] : '',
            'level' => 1,
            'counter' => isset($button['counter']) ? $button['counter'] : null,
            'options' => array(
                'class' => isset($button['class']) ? $button['class'] : null,
                'target' => isset($button['target']) ? $button['target'] : '',
                'onclick' => isset($button['onclick']) ? $button['onclick'] : null,
                'confirm' => isset($button['confirm']) ? $button['confirm'] : null,
            ),
            'data' => isset($button['data']) ? $button['data'] : '',
        );

        $this->addMenuItem('toolbar', $item);

    }

    /**
     * Проверяет наличие кнопок на тулбаре
     * @return bool
     */
    public function isToolbar(){
        if (!isset($this->menus['toolbar'])){ return false; }
        return (bool)sizeof($this->menus['toolbar']);
    }

// ========================================================================== //
// ========================================================================== //

    public function addMenuItem($menu_name, $item){

        if (!isset($this->menus[$menu_name])){
            $this->menus[$menu_name] = array();
        }

        array_push($this->menus[$menu_name], $item);

    }

    public function addMenuItems($menu_name, $items){

        if (!isset($this->menus[$menu_name])){
            $this->menus[$menu_name] = array();
        }

        foreach($items as $item){
            if (!isset($item['level'])) { $item['level'] = 1; }
            array_push($this->menus[$menu_name], $item);
        }

    }

    public function setMenuItems($menu_name, $items){

        if (!$items) { return; }

        $this->menus[$menu_name] = $items;

    }

// ========================================================================== //
// ========================================================================== //

    public function addBreadcrumb($title, $href=''){

        if (!$href) { $href = $_SERVER['REQUEST_URI']; }

        $this->breadcrumbs[] = array('title'=>$title, 'href'=>$href);

    }

    /**
     * Проверяет наличие пунктов в глубиномере
     * @return bool
     */
    public function isBreadcrumbs(){
        return (bool)$this->breadcrumbs;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет тег в головной раздел страницы
     * @param string $tag
     * @param bool $is_include_once
     */
	public function addHead($tag, $is_include_once=true){
        if($is_include_once){
        	$hash = md5($tag);
        } else {
            $hash = count($this->head);
        }
		$this->head[$hash] = $tag;
	}

    /**
     * Возвращает тег <link rel="stylesheet"> для указанного файла
     * @param string $file
     * @return string
     */
    public function getCSSTag($file){
        $file = (strpos($file, '://') !== false) ? $file : cmsConfig::get('root') . $file;
        return '<link rel="stylesheet" type="text/css" href="'.$file.'">';
    }

    /**
     * Возвращает тег <script> для указанного файла
     * @param string $file
     * @return string
     */
    public function getJSTag($file, $comment=''){
        $file = (strpos($file, '://') !== false) ? $file : cmsConfig::get('root') . $file;
        $comment = $comment ? "<!-- {$comment} !-->" : '';
        return '<script type="text/javascript" src="'.$file.'">'.$comment.'</script>';
    }

	/**
	 * Добавляет CSS файл в головной раздел страницы выше остальных CSS-тегов
	 * @param string $file
	 */
    public function addMainCSS($file){
        $hash = md5($file);
        if (isset($this->head_main_css[$hash])) { return false; }
		$this->head_main_css[$hash] = $file;
        return true;
    }

	/**
	 * Добавляет CSS файл в головной раздел страницы
	 * @param string $file
	 */
	public function addCSS($file){
        $hash = md5($file);
        if (isset($this->head_css[$hash])) { return false; }
		$this->head_css[$hash] = $file;
        return true;
	}

	/**
	 * Добавляет JS файл в головной раздел страницы выше остальных JS-тегов
	 * @param string $file
	 */
	public function addMainJS($file, $comment=''){
        $hash = md5($file);
        if (isset($this->head_main_js[$hash])) { return false; }
		$this->head_main_js[$hash] = $file;
        return true;
	}

	/**
	 * Добавляет JS файл в головной раздел страницы
	 * @param string $file
	 */
	public function addJS($file, $comment='', $allow_merge = true){
        $hash = md5($file);
        if (isset($this->head_js[$hash])) { return false; }
		$this->head_js[$hash] = $file;
        if (!$allow_merge){
            $this->head_js_no_merge[$hash] = $file;
        }
        return true;
	}
	
    public function addControllerJS($path, $cname = '', $comment='', $allow_merge = true){
        if(!$cname){$cname = $this->controller->name;}
        $path = "/controllers/{$cname}/js/{$path}.js";
        $path = 'templates/'.(file_exists(cmsConfig::getInstance()->root_path.'templates/'.$this->name.$path) ? $this->name : 'default').$path;
        return $this->addJS($path, $comment, $allow_merge);
    }
    public function addControllerCSS($path, $cname = ''){
        if(!$cname){$cname = $this->controller->name;}
        $path = "/controllers/{$cname}/css/{$path}.css";
        $path = 'templates/'.(file_exists(cmsConfig::getInstance()->root_path.'templates/'.$this->name.$path) ? $this->name : 'default').$path;
        return $this->addCSS($path);
    }

	public function insertJS($file, $comment=''){

        $file = (strpos($file, '://') !== false) ? $file : cmsConfig::get('root') . $file;
        $comment = $comment ? "<!-- {$comment} !-->" : '';
        echo '<script type="text/javascript" src="'.$file.'">'.$comment.'</script>';

	}

    public function insertCSS($file){

        $file = (strpos($file, '://') !== false) ? $file : cmsConfig::get('root') . $file;
		echo '<link rel="stylesheet" type="text/css" href="'.$file.'">';

    }

    public function getJS($file){

        ob_start();
        $this->insertJS($file);
        return ob_get_clean();

    }

    public function getCSS($file){

        ob_start();
        $this->insertCSS($file);
        return ob_get_clean();

    }

    public function getLangJS($phrases){

        if (func_num_args()==1 && !is_array($phrases)){ $phrases = array($phrases); }
        if (func_num_args()>1) { $phrases = func_get_args(); }

        $output = '';

        foreach($phrases as $phrase){
            $value = htmlspecialchars(constant($phrase));
            $output .= "var {$phrase} = '{$value}';";
        }

        return $output;

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Объединяет все подключенные к данной странице JS-файлы в один файл,
     * сохраняет его и возвращает путь к нему
     *
     * Если такой файл уже существует - сразу возвращает путь
     *
     * @return string
     */
    public function getMergedJSPath(){

        $config = cmsConfig::getInstance();

        $files = array_merge($this->head_main_js, $this->head_js);

        $cache_hash = md5(serialize($files));
        $cache_file = "cache/static/js/scripts.{$cache_hash}.js";
        $cache_file_path = $config->root_path . $cache_file;

        if (file_exists($cache_file_path)) { return $cache_file; }

        $merged_contents = '';

        foreach($files as $file){
            if (in_array($file, $this->head_js_no_merge)) { continue; }
            $file_path = $config->root_path . $file;
            $contents = file_get_contents($file_path);
            $merged_contents .= $contents;
        }

        file_put_contents($cache_file_path, $merged_contents);

        return $cache_file;

    }

    /**
     * Объединяет все подключенные к данной странице CSS-файлы в один файл,
     * сохраняет его и возвращает путь к нему
     *
     * Если такой файл уже существует - сразу возвращает путь
     *
     * @return string
     */
    public function getMergedCSSPath(){

        $config = cmsConfig::getInstance();

        $files = array_merge($this->head_main_css, $this->head_css);

        $cache_hash = md5(serialize($files));
        $cache_file = "cache/static/css/styles.{$cache_hash}.css";
        $cache_file_path = $config->root_path . $cache_file;

        if (file_exists($cache_file_path)) { return $cache_file; }

        $merged_contents = '';

        foreach($files as $file){
            $file_path = $config->root_path . $file;
            $contents = file_get_contents($file_path);
            $contents = $this->convertCSSUrlsToAbsolute($contents, $file);
            $contents = string_compress($contents);
            $merged_contents .= $contents;
        }

        file_put_contents($cache_file_path, $merged_contents);

        return $cache_file;

    }

    /**
     * Находит в переданном CSS-коде из указанного CSS-файла выражения url(*)
     * и заменяет все пути в них на абсолютные
     * @param string $css
     * @param string $css_file
     * @return string
     */
    public function convertCSSUrlsToAbsolute($css, $css_file){

        $matches = array();

        preg_match_all('/url\((.+)\)/i', $css, $matches);

        if ($matches){

            $config = cmsConfig::getInstance();

            $css_rel_url = $config->root . dirname($css_file);

            list($fulls, $urls) = $matches;

            foreach($urls as $i => $url){

                $abs_url = trim($url, '" ');

                $is_root = mb_substr($abs_url, 0, 1) == '/';
                $is_http = mb_substr($abs_url, 0, 7) == 'http://';
                $is_data = mb_substr($abs_url, 0, 10) == 'data:image';

                if ($is_data) { continue; }

                if ($is_root){

                    $abs_url = $config->host . $abs_url;

                } else

                if ($is_http){

                    continue;

                } else {

                    $abs_url = $config->host . '/' . files_normalize_path($css_rel_url . '/' . $abs_url);

                }

                $tag = 'url("'.$abs_url.'")';

                $css = str_replace($fulls[$i], $tag, $css);

            }

        }

        return $css;

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Устанавливает шаблон скелета
     * @param string $layout
     */
    public function setLayout($layout){
        $this->layout = $layout;
    }

    /**
     * Возвращает название шаблона скелета
     * @param string $layout
     */
    public function getLayout(){
        return $this->layout;
    }

    /**
     * Возвращает HTML-разметку схемы позиций виджетов
     * @return string
     */
    public function getSchemeHTML(){

        $config = cmsConfig::getInstance();

        $scheme_file = $config->root_path . 'templates/'.$this->name.'/scheme.html';

        if (!file_exists($scheme_file)) { return false; }

        return file_get_contents($scheme_file);

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Возвращает название глобального шаблона
     * @return string
     */
    public function getName(){
        return $this->name;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Сохраняет ссылку на текущий контроллер
     * @param string $controller_obj
     */
    public function setContext($controller_obj){
        if ($this->controller) { $this->controllers_queue[] = $this->controller; }
        $this->controller = $controller_obj;
    }

    /**
     * Восстанавливает ссылку на предыдущий контроллер
     */
    public function restoreContext(){

        if (!sizeof($this->controllers_queue)) { return false; }

        $this->controller = array_pop($this->controllers_queue);

        return true;

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Возвращает путь к tpl-файлу, определяя его наличие в собственном шаблоне
     * @param str $filename
     * @return string
     */
    public function getTemplateFileName($filename, $is_check=false){

        $config = cmsConfig::getInstance();

        $default    = $config->root_path . 'templates/default/'.$filename.'.tpl.php';
        $tpl_file   = $config->root_path . 'templates/'.$this->name.'/'.$filename.'.tpl.php';

        if (!file_exists($tpl_file)) { $tpl_file = $default; }

        if (!file_exists($tpl_file)){
            if (!$is_check){
                cmsCore::error(ERR_TEMPLATE_NOT_FOUND . ': ' . $tpl_file);
            } else {
                return false;
            }
        }

        return $tpl_file;

    }

    /**
     * Возвращает путь к CSS-файлу контроллера, определяя его наличие в собственном шаблоне
     * @param string $controller_name Имя контроллера
     * @param string $subfolder Подпапка в папке шаблонов контроллера
     * @return string
     */
    public function getStylesFileName($controller_name='', $subfolder='') {

        $config = cmsConfig::getInstance();

        if (!$controller_name) { $controller_name = $this->controller->name; }
        $subfolder = $subfolder ? $subfolder.'/' : '';

        $default    = 'templates/default/controllers/'.$controller_name.'/'.$subfolder.'styles.css';
        $tpl_file   = 'templates/'.$this->name.'/controllers/'.$controller_name.'/'.$subfolder.'styles.css';

        if (!file_exists($config->root_path . $tpl_file)) { $tpl_file = $default; }

        if (!file_exists($config->root_path . $tpl_file)){ return false; }

        return $tpl_file;

    }

    /**
     * Возвращает путь к CSS-файлу, определяя его наличие в собственном шаблоне
     * @param str $filename
     * @return string
     */
    public function getJavascriptFileName($filename){

        $config = cmsConfig::getInstance();

        $default    = 'templates/default/js/'.$filename.'.js';
        $js_file   = 'templates/'.$this->name.'/js/'.$filename.'.js';

        if (!file_exists($config->root_path . $js_file)) { $js_file = $default; }

        if (!file_exists($config->root_path . $js_file)){ return false; }

        return $js_file;

    }

//============================================================================//
//============================================================================//

    public function renderText($text){

        echo $this->addOutput($text);

    }

    public function renderJSON($data){

        echo json_encode($data);

        if ($this->controller->request->isAjax()) { $this->controller->halt(); }

    }

    public function renderInternal($controller, $tpl_file, $data=array()){

        $this->setContext($controller);

        $result = $this->render($tpl_file, $data, new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $this->restoreContext($result);

        return $result;

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function render($tpl_file, $data=array(), $request=false){

        $css_file = $this->getStylesFileName();

        if ($css_file){ $this->addCSS($css_file); }

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        return $this->processRender($tpl_file, $data, $request);

    }

    public function renderPlain($tpl_file, $data=array()){

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        ob_start();

        extract($data); include($tpl_file);

        echo ob_get_clean();

        $this->controller->halt();

    }

    public function processRender($tpl_file, $data=array(), $request=false){

        if (!$request) { $request = $this->controller->request; }

        ob_start();

        extract($data); include($tpl_file);

        $html = ob_get_clean();

        if ($request->isAjax()) {
            echo $html;
            $this->controller->halt();
        }

        if ($request->isStandard()){
            $this->addOutput( $html );
            return $html;
        }

        if ($request->isInternal()){
            return $html;
        }

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов текущего компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function renderChild($tpl_file, $data=array()){

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        extract($data); include($tpl_file);

    }

    public function renderControllerChild($controller, $tpl_file, $data=array()){

        $tpl_file = $this->getTemplateFileName('controllers/'.$controller.'/'.$tpl_file);

        extract($data); include($tpl_file);

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов текущего компонента)
	 * и возвращает полученный html-код в виде строки
     * @param string $tpl_file
     * @param array $data
     */
	public function getRenderedChild($tpl_file, $data=array()){

		$tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

		extract($data); ob_start(); include($tpl_file);

		return ob_get_clean();

	}

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function renderForm($form, $data, $attributes=array(), $errors=false){

        $tpl_file = $this->getTemplateFileName('assets/ui/form');

        include($tpl_file);

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $source_url
     * @param array $grid
     */
    public function renderGrid($source_url, $grid){

        $this->addJS( $this->getJavascriptFileName('datagrid') );

        if ($grid['options']['is_pagination']){
            $this->addJS( $this->getJavascriptFileName('datagrid-pagination') );
        }

        if ($grid['options']['is_draggable']){
            $this->addJS( $this->getJavascriptFileName('datagrid-drag') );
        }

        $tpl_file = $this->getTemplateFileName('assets/ui/grid-data');

        extract($grid);

        include($tpl_file);

    }

    public function renderGridRowsJSON($grid, $dataset, $total=1, $pages_count=1){

        $rows = array();
        $row_index = 0;

        //
        // проходим по всем строкам из набора данных
        //
        if ($total && $dataset){
            foreach($dataset as $row){

                $cell_index = 0;

                // вычисляем содержимое для каждой колонки таблицы
                foreach($grid['columns'] as $field => $column){

                    if (!is_array($row[$field])){
                        $value = htmlspecialchars($row[$field]);
                    } else {
                        $value = $row[$field];
                    }

                    if (!$value) { $value = ''; }

                    if (isset($column['flag']) && $column['flag']){

						if (isset($column['flag_on'])){
							$is_flag_on = $value == $column['flag_on'];
						} else {
							$is_flag_on = (bool)$value;
						}

                        $flag_class = $column['flag']===true ? 'flag' : $column['flag'];

						$flag_toggle_url = isset($column['flag_toggle']) ? $column['flag_toggle'] : false;

						if ($flag_toggle_url){
							$flag_toggle_url = string_replace_keys_values($flag_toggle_url, $row);
						}

						$flag_content = $flag_toggle_url ? '<a href="'.$flag_toggle_url.'"></a>' : '';

                        $value = '<div class="flag_trigger '.($is_flag_on ? "{$flag_class}_on" : "{$flag_class}_off").'" data-class="'.$flag_class.'">'.$flag_content.'</div>';

                    }

                    if (isset($column['handler'])){
                        $value = $column['handler']($value, $row);
                    }

                    // если из значения нужно сделать ссылку, то парсим шаблон
                    // адреса, заменяя значения полей
                    if (isset($column['href'])){
						$column['href'] = string_replace_keys_values($column['href'], $row);
                        $value = '<a href="'.$column['href'].'">'.$value.'</a>';
                    }

                    $rows[$row_index][] = $value;

                    $cell_index++;

                }

                // если есть колонка действий, то формируем набор ссылок
                // для текущей строки
                if ($grid['actions']){

                    $actions_html = '<div class="actions">';

                    foreach($grid['actions'] as $action){

                        $confirm_attr = '';

                        if (isset($action['handler'])){
                            $is_active = $action['handler']($row);
                        } else {
                            $is_active = true;
                        }

                        if ($is_active){
                            foreach($row as $cell_id=>$cell_value){

                                if (is_array($cell_value) || is_object($cell_value)) { continue; }

                                // парсим шаблон адреса, заменяя значения полей
                                if (isset($action['href'])){
                                    $action['href'] = str_replace('{'.$cell_id.'}', $cell_value, $action['href']);
                                }

                                // парсим шаблон запроса подтверждения, заменяя значения полей
                                if (isset($action['confirm'])){
                                    $action['confirm'] = str_replace('{'.$cell_id.'}', $cell_value, $action['confirm']);
                                    $confirm_attr = 'onclick="if(!confirm(\''.htmlspecialchars($action['confirm']).'\')){ return false; }"';
                                }

                            }

                            $actions_html .= '<a class="'.$action['class'].'" href="'.$action['href'].'" title="'.$action['title'].'" '.$confirm_attr.'></a>';
                        }

                    }

                    $actions_html .= '</div>';

                    $rows[$row_index][] = $actions_html;

                    $cell_index++;

                }

                $row_index++;
            }
        }

        $result = array(
            'rows' => $rows,
            'pages_count' => $pages_count,
            'total' => $total
        );

        echo json_encode($result);

    }

    /**
     * Выводит таблицу прав доступа по группам пользователей
     * @param array $rules Массив правил
     * @param array $groups Массив групп пользователей
     * @param array $values Массив значений
     * @param string $submit_url URL для сохранения формы
     */
    public function renderPermissionsGrid($rules, $groups, $values, $submit_url){

        $this->addJS( $this->getJavascriptFileName('datagrid') );

        $tpl_file = $this->getTemplateFileName('assets/ui/grid-perms');

        include($tpl_file);

    }

    /**
     * Выводит меню
     * @param array $items
     * @param int $active_id
     */
    public function renderMenu($menu, $active_ids=array(), $css_class='menu', $max_items=0){

        $tpl_file = $this->getTemplateFileName('assets/ui/menu');

        include($tpl_file);

    }

    public function renderAsset($tpl_file, $data=array()){

        $tpl_file = $this->getTemplateFileName('assets/' . $tpl_file);

        extract($data); include($tpl_file);

    }

    public function renderFormField($field_type, $data=array()){

        $tpl_file = $this->getTemplateFileName('assets/fields/'.$field_type);

        ob_start();

        extract($data); include($tpl_file);

        return ob_get_clean();

    }

//============================================================================//
//============================================================================//

    public function getAvailableContentListStyles(){

        $dir = 'templates/'.$this->name.'/content';
        $files = cmsCore::getFilesList($dir, 'default_list*.tpl.php', true);

        if (!$files) { return false; }

        $styles = array();

        foreach($files as $file){

            preg_match('/^default_list_([a-z0-9_\-]*)\.tpl$/i', $file, $matches);

            if (!$matches){
                $styles[''] = 'default_list (' . LANG_CP_LISTVIEW_STYLE_BASIC .')';
            } else {
                $title = constant('LANG_CP_LISTVIEW_STYLE_'.mb_strtoupper($matches[1]));
                if ($title) { $title = " ({$title})"; }
                $styles[$matches[1]] = pathinfo($file, PATHINFO_FILENAME) . $title;
            }

        }

        return $styles;

    }

    /**
     * Рендерит шаблон списка записей контента
     */
    public function renderContentList($ctype, $data=array(), $request=false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype['name'].'_list', true);

        if (!$tpl_file){

            $style = !empty($ctype['options']['list_style']) ? '_'.$ctype['options']['list_style'] : '';

            $tpl_file = $this->getTemplateFileName("content/default_list{$style}", true);

        }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

    /**
     * Рендерит шаблон просмотра записи контента
     */
    public function renderContentItem($ctype_name, $data=array(), $request=false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype_name.'_item', true);

        if (!$tpl_file){ $tpl_file = $this->getTemplateFileName('content/default_item', true); }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

//============================================================================//
//============================================================================//

    /**
     * Выводит окончательный вид страницы в браузер
     */
    public function renderPage(){

        $config = cmsConfig::getInstance();

        $layout = $this->getLayout();

        $template_file = $this->path . '/' . $layout . '.tpl.php';

        if(file_exists($template_file)){

            if (!$config->min_html){
                include($template_file);
            }

            if ($config->min_html){
                ob_start();
                include($template_file);
                echo html_minify(ob_get_clean());
            }

        } else {
            cmsCore::error(ERR_TEMPLATE_NOT_FOUND. ': '. $this->name.':'.$layout);
        }

    }

//============================================================================//
//============================================================================//

    public function renderWidget($widget, $data=array()){

        $tpl_path = cmsCore::getWidgetPath($widget->name, $widget->controller);

        $tpl_file = $this->getTemplateFileName($tpl_path . '/' . $widget->getTemplate());

        extract($data);

        ob_start(); include($tpl_file);

        $html = ob_get_clean();

        if (!$html){ return true; }

        if (empty($widget->is_tab_prev)){
            $this->widgets_group_index++;
        }

        $this->widgets[$widget->position][$this->widgets_group_index][] = array(
            'id' => $widget->id,
            'title' => $widget->is_title ? $widget->title : false,
            'links' => isset($widget->links) ? $widget->links : false,
            'wrapper' => $widget->getWrapper(),
            'class' => isset($widget->css_class) ? $widget->css_class : false,
            'class_title' => isset($widget->css_class_title) ? $widget->css_class_title : false,
            'class_wrap' => isset($widget->css_class_wrap) ? $widget->css_class_wrap : false,
            'body' => $html
        );

    }

//============================================================================//
//============================================================================//

    public function hasOptions(){
        return file_exists($this->path . '/options.form.php');
    }

    public function getOptionsForm(){

        if (!$this->hasOptions()){ return false; }

        cmsCore::loadTemplateLanguage($this->name);

        $form_file = $this->path . '/options.form.php';

        $form_name = 'template_options';

        $form = cmsForm::getForm($form_file, $form_name);

        if (!$form) { $form = new cmsForm(); }

        return $form;

    }

    public function getOptions(){

		if (!$this->hasOptions()){ return false; }

        $options = $this->loadOptions();

        $form = $this->getOptionsForm();

        return $form->parse(new cmsRequest($options));

    }

    public function loadOptions(){

        if (!$this->hasOptions()){ return false; }

        $options_file = cmsConfig::get('root_path') . "system/config/theme_{$this->name}.yml";

        if (!file_exists($options_file)){ return array(); }

        $options_yaml = @file_get_contents($options_file);

        return cmsModel::yamlToArray($options_yaml);

    }

    public function saveOptions($options){

        $options_file = cmsConfig::get('root_path') . "system/config/theme_{$this->name}.yml";

        $options_yaml = cmsModel::arrayToYaml($options);

        return @file_put_contents($options_file, $options_yaml);

    }


//============================================================================//
//============================================================================//

    public function hasProfileThemesSupport(){
        return file_exists($this->path . '/profiles/styler.php');
    }

    public function hasProfileThemesOptions(){
        return file_exists($this->path . '/profiles/options.form.php');
    }

    public function getProfileOptionsForm(){

        if (!$this->hasProfileThemesOptions()){ return false; }

        $form_file = $this->path . '/profiles/options.form.php';

        $form_name = 'template_profile_options';

        $form = cmsForm::getForm($form_file, $form_name);

        if (!$form) { $form = new cmsForm(); }

        return $form;

    }

    public function applyProfileStyle($profile){

        if (!$this->hasProfileThemesSupport()){ return false; }

        $config = cmsConfig::getInstance();

        $theme = $profile['theme'];

        cmsCore::loadTemplateLanguage($this->name);

        if ($this->hasProfileThemesOptions()){

            $form = $this->getProfileOptionsForm();
            $theme = $form->parse(new cmsRequest($profile['theme']), true);

        }

        ob_start();

        extract($theme);

        include $this->path . '/profiles/styler.php';

        $style = ob_get_clean();

        $this->addHead($style);

        return true;

    }

//============================================================================//
//============================================================================//

}
