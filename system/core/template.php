<?php

class cmsTemplate {

    private static $instance;

    /**
     * Путь корневой папки шаблонов (может быть пустым)
     */
    const TEMPLATE_BASE_PATH = 'templates/';

    public $name;
    public $path;
    protected $inherit_names = array();
    protected $layout = 'main';
    protected $layout_params = array();
    protected $output;
    protected $blocks = array();
    protected $options;
    protected $site_config;

	protected $head = array();
	protected $bottom = array();
	protected $head_main_css = array();
	protected $head_css = array();
	protected $head_main_js = array();
	protected $head_js = array();
	protected $insert_js = array();
	protected $insert_css = array();
	protected $head_js_no_merge = array();
	protected $head_css_no_merge = array();
	protected $head_preload = array();
	public $page_h1;
	public $page_h1_item;
	public $title;
	public $title_item;
	public $metadesc;
	public $metadesc_item;
	public $metakeys;
	public $metakeys_item;

    public $breadcrumbs = array();
    public $menus = array();
    protected $db_menus = array();
    protected $menu_loaded = false;
    protected $not_found_tpls = array();

    public $widgets_rendered = false;
    protected $widgets = array();
    protected $widgets_group_index = 0;

    protected $controller;
    protected $controllers_queue = array();

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
            // подключаем хелпер основного шаблона
            if(!cmsCore::includeFile(self::TEMPLATE_BASE_PATH.self::$instance->getName().'/assets/helper.php')){
                cmsCore::loadLib('template.helper');
            }
        }
        return self::$instance;
    }

// ========================================================================== //
// ========================================================================== //

	public function __construct($name=''){

		$this->site_config = cmsConfig::getInstance();

        if($name){

            $this->setName($name);

        } else {

            $device_type = cmsRequest::getDeviceType();
            $template = $this->site_config->template;

            // шаблон в зависимости от девайса
            if($device_type !== 'desktop'){
                $device_template = cmsConfig::get('template_'.$device_type);
                if($device_template){
                    $template = $device_template;
                }
            }
            // шаблон админки, можем определить только тут
            $controller = cmsCore::getControllerNameByAlias(cmsCore::getInstance()->uri_controller);
            $controller = $controller ? $controller : cmsCore::getInstance()->uri_controller;
            if($controller === 'admin' && $this->site_config->template_admin){
                $template = $this->site_config->template_admin;
            }

            $this->setName($template);

        }

        $this->options = $this->getOptions();

        $this->setInheritNames($this->getInheritTemplates());

		$this->title = $this->site_config->sitename;

		$is_no_def_meta = isset($this->site_config->is_no_meta) ? $this->site_config->is_no_meta : false;

		if (!$is_no_def_meta){
			$this->metakeys = $this->site_config->metakeys;
			$this->metadesc = $this->site_config->metadesc;
		}

	}

// ========================================================================== //
// ========================================================================== //

    public function isBody(){
        return !empty($this->output);
    }

	/**
	 * Выводит тело страницы
	 */
	public function body(){
		echo $this->output;
	}

    /**
     * Выводит HTML блока
     * @param string $position
     */
	public function block($position){
		echo !empty($this->blocks[$position]) ? $this->blocks[$position] : '';
	}

    public function hasBlock($position){

        if (func_num_args() > 1){
            $positions = func_get_args();
        } else {
            $positions = array($position);
        }

        $has = false;

        foreach($positions as $pos){
            $has = $has || !empty($this->blocks[$pos]);
        }

        return $has;

    }

    /**
     * Выводит головные теги страницы
     *
     * @param boolean $is_seo_meta Выводить мета теги
     * @param boolean $print_js Выводить javascript теги
     * @param boolean $print_css Выводить CSS теги
     * @return $this
     */
	public function head($is_seo_meta = true, $print_js = true, $print_css = true){

        cmsEventsManager::hook('before_print_head', $this);

        if ($is_seo_meta){
			if (!empty($this->metakeys) && empty($this->site_config->disable_metakeys)){
				echo "\t". '<meta name="keywords" content="'.html((!empty($this->metakeys_item) ? string_replace_keys_values_extended($this->metakeys, $this->metakeys_item) : $this->metakeys), false).'">' . "\n";
			}
			if (!empty($this->metadesc)){
				echo "\t". '<meta name="description" content="'.html((!empty($this->metadesc_item) ? string_replace_keys_values_extended($this->metadesc, $this->metadesc_item) : $this->metadesc), false).'">' ."\n";
			}
        }

		foreach ($this->head as $id => $tag){	echo "\t". $tag . "\n";	}

        if($print_css){
            $this->printCssTags();
        }

        if($print_js){
            $this->printJavascriptTags();
        }

        if(!empty($this->site_config->set_head_preload) && $this->head_preload){
            header('Link: '.implode(', ', $this->head_preload));
        }

        return $this;

	}

    public function bottom(){
        foreach ($this->bottom as $id => $tag){	echo "\t". $tag . "\n";	}
    }

    /**
     * Выводит javascript теги
     * @return $this
     */
    public function printJavascriptTags() {

        $js = array();

        if (!$this->site_config->merge_js){

            $js = array_merge(array_values($this->head_main_js), array_values($this->head_js));

        } else {

            $js[] = $this->getMergedJSPath();

            $js = array_merge($js, array_values($this->head_js_no_merge));

        }

        foreach ($js as $file) {

            $file = $this->getHeadFilePath($file);

            $this->head_preload[] = '<'.$file.'>; rel=preload; as=script';

            echo "\t" . $this->getJSTag($file) . "\n";

        }

        return $this;

    }

    /**
     * Выводит CSS теги
     * @return $this
     */
    public function printCssTags() {

        $css = array();

        if (!$this->site_config->merge_css){

            $css = array_merge(array_values($this->head_main_css), array_values($this->head_css));

        } else {

            $css[] = $this->getMergedCSSPath();

            $css = array_merge($css, array_values($this->head_css_no_merge));

        }

        foreach ($css as $file) {

            $file = $this->getHeadFilePath($file);

            $this->head_preload[] = '<'.$file.'>; rel=preload; as=style';

            echo "\t" . $this->getCSSTag($file) . "\n";

        }

        return $this;

    }

	/**
	 * Выводит заголовок текущей страницы
	 */
	public function title(){

        $t = !empty($this->title_item) ? string_replace_keys_values_extended($this->title, $this->title_item) : $this->title;

        if(!empty($this->site_config->page_num_in_title)){

            $page = cmsCore::getInstance()->request->get('page', 0);

            if($page > 1){
                $t .= ' — '.LANG_PAGE.' №'.$page;
            }

        }

    	html($t);
	}

	/**
	 * Выводит название сайта
	 */
	public function sitename(){
		html($this->site_config->sitename);
	}

    /**
     * Выводит глобальный тулбар
     * @param string $template_name Название шаблона в assets/ui
     * @return
     */
    public function toolbar($template_name = 'menu'){
        if (!$this->isToolbar()){ return; }
        $this->menu('toolbar', false, 'nav-pills', 0, false, $template_name);
    }

    /**
     * Выводит меню действий контроллера
     * @param string $menu_title Название меню
     * @return
     */
    public function actionsToolbar($menu_title){
        if (empty($this->menus['controller_actions_menu'])){ return; }
        $this->menu('controller_actions_menu', false, 'menu', 0, false, 'controller_actions_menu', $menu_title);
    }

    /**
     * Выводит виджеты на указанной позиции
     * @param string $position Название позиции
     * @param boolean $is_titles Выводить заголовки
     * @param string $wrapper Название шаблона обертки
     * @return boolean
     */
	public function widgets($position, $is_titles = true, $wrapper = '') {

        if (!$this->hasWidgetsOn($position)){ return false; }

        foreach($this->widgets[$position] as $group){

            if (sizeof($group)==1){

                $widget = $group[0];
                if ($wrapper){ $widget['wrapper'] = $wrapper; }

                if(!empty($widget['wrapper'])){

                    $tpl_file = $this->getTemplateFileName('widgets/' . $widget['wrapper']);
                    include($tpl_file);

                } else {
                    echo $widget['body'];
                }

            } else {

                $widgets = $group;
                $tpl_file = $this->getTemplateFileName('widgets/wrapper_tabbed');
                include($tpl_file);

            }

        }

	}

    /**
     * Проверяет наличие виджетов на позиции/позициях
     *
     * @param string $position Название позиции
     *                         Можно передавать сколь угодно дополнительных параметров
     * @return boolean
     */
    public function hasWidgetsOn($position){

        if(!$this->widgets_rendered){
            cmsCore::getInstance()->runWidgets();
        }

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

    protected function loadMenus($menu_name=false) {

        if(!$this->menu_loaded){
            $this->db_menus = cmsCore::getModel('menu')->filterEqual('is_enabled', 1)->getAllMenuItemsTree();
            $this->menu_loaded = true;
        }

        if($menu_name && isset($this->db_menus[$menu_name])){
            return modelMenu::buildMenu($this->db_menus[$menu_name]);
        }

        return false;

    }

    /**
     * Проверяет наличие меню
     *
     * @param string $menu_name Название меню
     * @return boolean
     */
    public function hasMenu($menu_name){
        return !empty($this->menus[$menu_name]);
    }

    /**
     * Выводит меню
     *
     * @param string $menu_name Название меню
     * @param boolean $detect_active_id Определять активные пункты меню
     * @param string $css_class CSS класс контейнера пунктов меню
     * @param integer $max_items Максимальное количество видимых пунктов
     * @param boolean $is_allow_multiple_active Определять все активные пункты меню
     * @param string $template Название файла шаблона меню в assets/ui/
     * @param string $menu_title Название(подпись) меню
     */
    public function menu($menu_name, $detect_active_id = true, $css_class = 'menu', $max_items = 0, $is_allow_multiple_active = false, $template = 'menu', $menu_title = '') {

        if (!isset($this->menus[$menu_name])) {

            $menu = $this->loadMenus($menu_name);
            if (!$menu){ return; }

            $this->setMenuItems($menu_name, $menu);

        }

        $menu       = $this->menus[$menu_name];
        $active_ids = array();

        // Для подсчета пунктов меню первого уровня
        $first_level_count = 0;
        $first_level_limit = 0;
        $index = 0;

        $core = cmsCore::getInstance();

        // для определения активного пункта меню
        // оригинальный урл
        $current_url = trim($core->uri_before_remap, '/');
        // подготовленный для работы cms
        $current_ourl = trim($core->uri, '/');

        $href_lang = cmsCore::getLanguageHrefPrefix();

        $root_len = strlen($this->site_config->root);
        $lang_len = $href_lang ? strlen($href_lang) : 0;

        foreach($menu as $id=>$item){

            $menu[$id]['disabled']     = !empty($item['disabled']);
            $menu[$id]['level']        = !isset($item['level']) ? 1 : $item['level'];
            $menu[$id]['childs_count'] = !isset($item['childs_count']) ? 0 : $item['childs_count'];

            if (!isset($item['url']) &&  !empty($item['controller'])) {
                if (!isset($item['action'])) { $item['action'] = ''; }
                if (!isset($item['params'])) { $item['params'] = array(); }
                $item['url'] = href_to($item['controller'], $item['action'], $item['params']);
                $menu[$id]['url'] = $item['url'];
            }

            // Если нужно, считаем количество пунктов первого уровня
            if ($max_items){

                if ($item['level'] == 1){ $first_level_count++; }
                if ($first_level_count > $max_items && !$first_level_limit){ $first_level_limit = $index; }
                $index++;

            }

            // ищем активные пункты меню
            if ($detect_active_id){

                if (!isset($item['url'])) { continue; }

                $url = isset($item['url_mask']) ? $item['url_mask'] : urldecode($item['url']);
                $url = mb_substr($url, $root_len);
                if($href_lang){
                    $url = mb_substr($url, $lang_len);
                }
                $url = trim($url, '/');

                if (!$url) { continue; }

                $url_len = mb_strlen($url);

                //полное совпадение ссылки и адреса?
                if ($current_url == $url){
                    $active_ids[] = $id;
                    $is_strict = true; // не используется нигде
                } else {

                    //частичное совпадение ссылки и адреса (по началу строки)?
                    $url_first_parts = [mb_substr($current_ourl, 0, $url_len), mb_substr($current_url, 0, $url_len)];
                    if (in_array($url, $url_first_parts)){
                        $active_ids[] = $id;
                        $is_strict = false;  // не используется нигде
                    }

                }

            }

        }

        if ($max_items && $first_level_limit){

            //
            // Если на первом уровне больше пунктов, чем нужно то
            // разрезаем массив меню на две части - видимую и скрытую
            //

            $visible_items = array_slice($menu, 0, $first_level_limit, true);
            $more_items    = array_slice($menu, $first_level_limit, sizeof($menu) - $first_level_limit, true);

            $item_more_id = 10000;

            $item_more = array(
                $item_more_id => array(
                    'id'           => $item_more_id,
                    'title'        => LANG_MENU_MORE,
                    'childs_count' => ($first_level_count - $max_items),
                    'level'        => 1,
                    'disabled'     => false,
                    'options'      => array(
                        'class' => 'more'
                    )
                )
            );

            foreach($more_items as $id=>$item){
                if ($item['level']==1){
                    $more_items[$id]['parent_id'] = $item_more_id;
                }
                $more_items[$id]['level']++;
            }

            $menu = $visible_items + $item_more + $more_items;

        }

        if (!$is_allow_multiple_active && (count($active_ids)>1)){
            $active_ids = array($active_ids[count($active_ids)-1]);
        }

        $this->renderMenu($menu, $active_ids, $css_class, $max_items, $template, $menu_title);

    }

    /**
     * Выводит глубиномер
     *
     * @param array $options Опции глубиномера
     */
    public function breadcrumbs($options = array()) {

        $default_options = array(
            'home_url'   => href_to_home(),
            'template'   => 'breadcrumbs',
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

        $this->renderAsset('ui/'.$options['template'], array(
            'breadcrumbs' => $this->breadcrumbs,
            'options' => $options
        ));

    }

    /**
     * Формирует ссылку в контексте текущего контроллера
     * @param string $action Экшен
     * @param string|array $params Параметры экшена
     * @return type
     */
    public function href_to($action, $params = false) {

        if (isset($this->controller)) {
            if (!isset($this->controller->root_url)) {
                return href_to($this->controller->name, $action, $params);
            } else {
                return href_to($this->controller->root_url, $action, $params);
            }
        } else {
            return href_to($this->site_config->root, $action, $params);
        }

    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет переданный код к выводу
     * @param string $html
     */
    public function addOutput($html){
        $this->output .= $html;
    }

    public function addToBlock($position, $html){
        if(isset($this->blocks[$position])){
            $this->blocks[$position] .= $html;
        } else {
            $this->blocks[$position] = $html;
        }
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
     * Проверяет наличие тега h1
     * @return boolean
     */
	public function hasPageH1(){
    	return !empty($this->page_h1);
	}

    /**
     * Печатает значение тега h1 страницы
     */
	public function pageH1(){
    	echo !empty($this->page_h1_item) ? string_replace_keys_values_extended($this->page_h1, $this->page_h1_item) : $this->page_h1;
	}

    /**
     * Устанавливает значение тега h1 страницы
     *
     * @param string $title
     * @return $this
     */
    public function setPageH1($title) {

        if (is_array($title)){ $title = implode(', ', $title); }

        $this->page_h1 = $title;

        return $this;

    }

    /**
     * Добавляет к значению тега h1 строку
     *
     * @param string $title Строка
     * @param string $separator Разделитель
     * @return $this
     */
	public function addToPageH1($title, $separator = ', '){
        if (is_array($title)){ $title = implode($separator, $title); }
        $this->page_h1 .= ($this->page_h1 ? $separator : '').$separator.$title;
        return $this;
	}

	public function setPageH1Item($item){
        $this->page_h1_item = $item; return $this;
	}

	/**
	 * Устанавливает заголовок страницы
     * Если передано несколько аргументов, склеивает их в одну строку
     * через разделитель
     *
	 * @param string $pagetitle Заголовок
	 */
	public function setPageTitle($pagetitle){
        if (func_num_args() > 1){ $pagetitle = implode(' · ', array_filter(func_get_args())); }
        if (is_array($pagetitle)){ $pagetitle = implode(' ', $pagetitle); }
        $this->title = $pagetitle;
        if($this->site_config->is_sitename_in_title){
            $this->title .= ' — '.$this->site_config->sitename;
        }
        return $this;
	}

	public function addToPageTitle($title){
        $this->title .= ' '.$title;
        return $this;
	}

    /**
     * Устанавливает заголовок странице по паттерну в настройках контроллера
     *
     * @param array $item Массив записи
     * @param string $default Ключ по умолчанию, если паттерн не задан
     * @return \cmsTemplate
     */
	public function setPagePatternTitle($item, $default = 'title'){
        if (!empty($this->controller->options['tag_title'])) {
            $this->setPageTitle(string_replace_keys_values_extended($this->controller->options['tag_title'], $item));
        } else {
            $this->setPageTitle($item[$default]);
        }
        return $this;
	}

	public function setPageTitleItem($item){
        $this->title_item = $item; return $this;
	}

	public function setFrontPageTitle($pagetitle){
		$this->title = $pagetitle; return $this;
	}

	/**
	 * Устанавливает ключевые слова и описание страницы
	 * @param string $keywords Ключевые слова
	 * @param string $description Описание
	 */
	public function setMeta($keywords, $description){
		$this->metakeys = $keywords;
		$this->metadesc = $description;
        return $this;
	}

	/**
	 * Устанавливает ключевые слова страницы
	 * @param string $keywords Ключевые слова
	 */
    public function setPageKeywords($keywords){
        $this->metakeys = $keywords; return $this;
    }

	public function setPageKeywordsItem($item){
        $this->metakeys_item= $item; return $this;
	}

	/**
	 * Устанавливает описание страницы
	 * @param string $description Описание
	 */
    public function setPageDescription($description){
        $this->metadesc = $description; return $this;
    }

	public function setPageDescriptionItem($item){
        $this->metadesc_item= $item; return $this;
	}

	public function setPagePatternDescription($item, $default = 'description'){

        if (!empty($this->controller->options['tag_desc'])) {
            $this->setPageDescription(string_replace_keys_values_extended($this->controller->options['tag_desc'], $item));
        } else {
            $this->setPageDescription(string_get_meta_description($item[$default]));
        }

        return $this;

	}

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет кнопку на глобальный тулбар
     * @param array $button
     * @return \cmsTemplate
     */
    public function addToolButton($button){

        $item = array(
            'title' => $button['title'],
            'url' => isset($button['href']) ? $button['href'] : '',
            'level' => isset($button['level']) ? $button['level'] : 1,
            'childs_count' => isset($button['childs_count']) ? $button['childs_count'] : 0,
            'counter' => isset($button['counter']) ? $button['counter'] : null,
            'options' => array(
                'icon' => isset($button['icon']) ? $button['icon'] : null,
                'class' => isset($button['class']) ? $button['class'] : null,
                'target' => isset($button['target']) ? $button['target'] : '',
                'onclick' => isset($button['onclick']) ? $button['onclick'] : null,
                'confirm' => isset($button['confirm']) ? $button['confirm'] : null,
            ),
            'data' => isset($button['data']) ? $button['data'] : '',
        );

        $this->addMenuItem('toolbar', $item);

        return $this;

    }

    /**
     * Добавляет кнопки на глобальный тулбар
     * @param array $buttons
     * @return \cmsTemplate
     */
    public function addToolButtons($buttons){

        if (is_array($buttons)){
            foreach($buttons as $button){
                $this->addToolButton($button);
            }
        }

        return $this;

    }

    /**
     * Проверяет наличие кнопок на тулбаре
     * @return bool
     */
    public function isToolbar(){
        if (empty($this->menus['toolbar'])){ return false; }
        return (bool)sizeof($this->menus['toolbar']);
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет один пункт меню в меню
     * @param string $menu_name Название меню
     * @param array $item Массив данных пункта меню
     */
    public function addMenuItem($menu_name, $item){

        if (!isset($this->menus[$menu_name])){
            $this->menus[$menu_name] = array();
        }

        array_push($this->menus[$menu_name], $item);

        return $this;

    }

    /**
     * Добавляет массив пунктов меню в меню
     * @param string $menu_name Название меню
     * @param array $items Массив пунктов меню
     */
    public function addMenuItems($menu_name, $items){

        if (!isset($this->menus[$menu_name])){
            $this->menus[$menu_name] = array();
        }

        foreach($items as $item){
            if (!isset($item['level'])) { $item['level'] = 1; }
            array_push($this->menus[$menu_name], $item);
        }

        return $this;

    }

    /**
     * Устанавливает массив пунктов меню для меню
     * Если для переданного меню уже были пункты - заменятся заданными
     *
     * @param string $menu_name Название меню
     * @param array $items Массив пунктов меню
     * @return type
     */
    public function setMenuItems($menu_name, $items){

        if ($items) {
            $this->menus[$menu_name] = $items;
        }

        return $this;

    }

    public function applyMenuItemsHook($menu_name, $event_name){

        $this->menus[$menu_name] = cmsEventsManager::hook($event_name, (isset($this->menus[$menu_name]) ? $this->menus[$menu_name] : array()));

        return $this;

    }

    public function applyToolbarHook($event_name){
        return $this->applyMenuItemsHook('toolbar', $event_name);
    }

// ========================================================================== //
// ========================================================================== //
    /**
     * Добавляет пункт в глубиномер
     * @param string $title Название
     * @param string $href Ссылка. Если не передана, устанавливается текущий URI
     */
    public function addBreadcrumb($title, $href = '') {

        if (!$href) { $href = $_SERVER['REQUEST_URI']; }

        $this->breadcrumbs[] = array('title' => $title, 'href' => $href);

        return $this;

    }

    /**
     * Проверяет наличие пунктов в глубиномере
     * @return boolean
     */
    public function isBreadcrumbs(){
        return (bool)$this->breadcrumbs;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет тег в головной раздел страницы
     * @param string $tag
     * @param boolean $is_include_once
     */
	public function addHead($tag, $is_include_once = true) {
        if($is_include_once){
        	$hash = md5($tag);
        } else {
            $hash = count($this->head);
        }
		$this->head[$hash] = $tag;
        return $this;
	}

	public function addBottom($tag, $request = false){
        if(!$request){ $request = cmsCore::getInstance()->request; }
        if($request->isAjax()){
            echo $tag;
        } else {
            $this->bottom[] = $tag;
        }
        return $this;
	}

    public function getTemplateFilePath($path) {
        return $this->site_config->root . self::TEMPLATE_BASE_PATH. $this->name .'/'.$path;
    }

    public function getHeadFilePath($file){

        if(!preg_match('#^([a-z]*)(:?)\/\/#', $file)){

            $arg_separator = strpos($file, '?') !== false ? '&' : '?';

            $file = $this->site_config->root . $file .($this->site_config->production_time ? $arg_separator. $this->site_config->production_time : '');

        }

        return $file;

    }
    /**
     * Возвращает тег <link rel="stylesheet"> для указанного файла
     *
     * @param string $file Путь к файлу без учета корневой директории (начального слеша)
     * @return string
     */
    public function getCSSTag($file){

        if(strpos($file, '/') !== 0){
            $file = $this->getHeadFilePath($file);
        }

        return '<link rel="stylesheet" type="text/css" href="'.$file.'">';

    }

    /**
     * Возвращает тег <script> для указанного файла
     *
     * @param string $file Путь к файлу без учета корневой директории (начального слеша)
     * @param string $comment Комментарий к скрипту
     * @param array $params Параметры тега
     * @return string
     */
    public function getJSTag($file, $comment = '', $params = array()){

        if(strpos($file, '/') !== 0){
            $file = $this->getHeadFilePath($file);
        }

        $comment = $comment ? '<!-- '.$comment.' !-->' : '';

        return '<script src="'.$file.'" '.html_attr_str($params).'>'.$comment.'</script>';

    }

    /**
     * Добавляет CSS файл в головной раздел страницы выше остальных CSS-тегов
     *
     * @param string $file Путь к файлу без указания корня
     * @return boolean
     */
    public function addMainCSS($file) {

        if (!$file) { return false; }

        if(!is_array($file)){

            $hash = md5($file);
            if (isset($this->head_main_css[$hash]) || isset($this->head_css[$hash])) {
                return false;
            }

            $this->head_main_css[$hash] = $file;

            return true;
        }

        foreach($file as $f){
            $this->addMainCSS($f);
        }

        return true;

    }

    /**
     * Добавляет CSS файл в головной раздел страницы
     *
     * @param string $file Путь к файлу без указания корня
     * @param boolean $allow_merge Использовать в объединении
     * @return boolean
     */
	public function addCSS($file, $allow_merge = true) {

        if (!$file) { return false; }

        if(!is_array($file)){

            $hash = md5($file);
            if (isset($this->head_css[$hash]) || isset($this->head_main_css[$hash])) {
                return false;
            }

            $this->head_css[$hash] = $file;

            if (!$allow_merge) {
                $this->head_css_no_merge[$hash] = $file;
            }

            return true;
        }

        foreach($file as $f){
            $this->addCSS($f, $allow_merge);
        }

        return true;

    }

    /**
     * Добавляет JS файл к подключению на странице выше остальных JS-тегов
     *
     * @param string $file Путь к файлу без указания корня
     * @param boolean $at_begin Поместить в самое начало?
     * @return boolean
     */
	public function addMainJS($file, $at_begin = false) {

        if (!$file) { return false; }

        if(!is_array($file)){

            $hash = md5($file);
            if (isset($this->head_main_js[$hash])) {
                return false;
            }

            if($at_begin === true){ // На случай, если здесь "Комментарий к скрипту"
                $this->head_main_js = [$hash => $file] + $this->head_main_js;
            } else {
                $this->head_main_js[$hash] = $file;
            }

            return true;
        }

        if($at_begin === true && count($file) > 1){
            $file = array_reverse($file);
        }

        foreach($file as $f){
            $this->addMainJS($f, $at_begin);
        }

        return true;

    }

    /**
     * Добавляет JS файл к подключению на странице
     *
     * @param string $file Путь к файлу без указания корня
     * @param string $comment Комментарий к скрипту (устаревший параметр)
     * @param boolean $allow_merge Использовать в объединении
     * @return boolean
     */
	public function addJS($file, $comment = '', $allow_merge = true) {

        if (!$file) { return false; }

        if(!is_array($file)){

            $hash = md5($file);
            if (isset($this->head_js[$hash])) {
                return false;
            }

            $this->head_js[$hash] = $file;
            if (!$allow_merge) {
                $this->head_js_no_merge[$hash] = $file;
            }

            return true;
        }

        foreach($file as $f){
            $this->addJS($f, '', $allow_merge);
        }

        return true;

    }

    /**
     * Подключает JS файл из директории шаблона controllers/CNAME/js/
     *
     * @param string $path Путь к файлу относительно TEMPLATE_BASE_PATH.TNAME/controllers/CNAME/js/
     * @param string $cname Название контроллера. Если не указан, берется из текущего контекста
     * @param string $comment Комментарий скрипта
     * @param boolean $allow_merge Использовать в объединении
     * @return boolean
     */
    public function addControllerJS($path, $cname = '', $comment = '', $allow_merge = true){

        if(!$cname){ $cname = $this->controller->name; }

        if(!is_array($path)){
            return $this->addTplJS("controllers/{$cname}/js/{$path}", $comment, $allow_merge);
        }

        foreach($path as $p){
            $this->addControllerJS($p, $cname, $comment, $allow_merge);
        }

        return true;

    }

    public function addControllerJSFromContext($path, $cname = '', $request = false){

        if(!$cname){ $cname = $this->controller->name; }
        if(!$request){ $request = cmsCore::getInstance()->request; }

        if($request->isAjax()){

            if(!is_array($path)){
                return $this->insertJS($this->getTplFilePath("controllers/{$cname}/js/{$path}.js", false));
            }

            foreach($path as $p){
                $this->addControllerJSFromContext($p, $cname, $request);
            }

            return true;
        }

        return $this->addControllerJS($path, $cname, '', false);

    }

    /**
     * Подключает CSS файл из директории шаблона controllers/CNAME/css/
     *
     * @param string $path Путь к файлу относительно TEMPLATE_BASE_PATH.TNAME/controllers/CNAME/css/
     * @param string $cname Название контроллера. Если не указан, берется из текущего контекста
     * @param boolean $allow_merge Использовать в объединении
     * @return boolean
     */
    public function addControllerCSS($path, $cname = '', $allow_merge = true){

        if(!$cname){ $cname = $this->controller->name; }

        if(!is_array($path)){
            return $this->addTplCSS("controllers/{$cname}/css/{$path}", $allow_merge);
        }

        foreach($path as $p){
            $this->addControllerCSS($p, $cname, $allow_merge);
        }

        return true;

    }

    public function addControllerCSSFromContext($path, $cname = '', $request = false){

        if(!$cname){ $cname = $this->controller->name; }
        if(!$request){ $request = cmsCore::getInstance()->request; }

        if($request->isAjax()){

            if(!is_array($path)){
                return $this->insertCSS($this->getTplFilePath("controllers/{$cname}/css/{$path}.css", false));
            }

            foreach($path as $p){
                $this->addControllerCSSFromContext($p, $cname, $request);
            }

            return true;

        }

        return $this->addControllerCSS($path, $cname, false);

    }

    /**
     * Подключает JS файл относительно корня шаблона
     * Ищет, начиная с текущего шаблона и по цепочке до дефолтного
     *
     * @param string $path Путь к файлу относительно TEMPLATE_BASE_PATH.TEMPLATE_NAME/
     * @param string $comment Комментарий скрипта
     * @param boolean $allow_merge Использовать в объединении
     * @return boolean
     */
    public function addTplJS($path, $comment = '', $allow_merge = true) {

        if(!is_array($path)){
            return $this->addJS($this->getTplFilePath($path . '.js', false), $comment, $allow_merge);
        }

        foreach($path as $p){
            $this->addTplJS($p, $comment, $allow_merge);
        }

        return true;

    }

    public function addTplJSFromContext($path, $request = false) {

        if(!$request){ $request = cmsCore::getInstance()->request; }

        if($request->isAjax()){

            if(!is_array($path)){
                return $this->insertJS($this->getTplFilePath($path . '.js', false));
            }

            foreach($path as $p){
                $this->addTplJSFromContext($p, $request);
            }

            return true;

        }

        return $this->addTplJS($path, '', false);

    }

    /**
     * Подключает CSS файл относительно корня шаблона
     * Ищет, начиная с текущего шаблона и по цепочке до дефолтного
     *
     * @param string $path Путь к файлу относительно TEMPLATE_BASE_PATH.TEMPLATE_NAME/
     * @param boolean $allow_merge Использовать в объединении
     * @return boolean
     */
    public function addTplCSS($path, $allow_merge = true) {

        if(!is_array($path)){
            return $this->addCSS($this->getTplFilePath($path . '.css', false), $allow_merge);
        }

        foreach($path as $p){
            $this->addTplCSS($p, $allow_merge);
        }

        return true;

    }

    public function addTplCSSFromContext($path, $request = false) {

        if(!$request){ $request = cmsCore::getInstance()->request; }

        if($request->isAjax()){

            if(!is_array($path)){
                return $this->insertCSS($this->getTplFilePath($path . '.css', false));
            }

            foreach($path as $p){
                $this->addTplCSSFromContext($p, $request);
            }

            return true;
        }

        return $this->addTplCSS($path, false);

    }

    /**
     * Подключает JS файл относительно TEMPLATE_BASE_PATH.TEMPLATE_NAME/js/
     * Ищет, начиная с текущего шаблона и по цепочке до дефолтного
     *
     * @param string $name Имя файла без расширения
     * @return boolean
     */
    public function addTplJSName($name) {
        return $this->addJS($this->getJavascriptFileName($name));
    }
    public function addTplJSNameFromContext($name, $request = false) {
        if(!$request){ $request = cmsCore::getInstance()->request; }
        if($request->isAjax()){
            return $this->insertJS($this->getJavascriptFileName($name));
        }

        return $this->addJS($this->getJavascriptFileName($name), '', false);
    }
    public function addMainTplJSName($name, $at_begin = false) {
        return $this->addMainJS($this->getJavascriptFileName($name), $at_begin);
    }

    /**
     * Подключает CSS файл относительно TEMPLATE_BASE_PATH.TEMPLATE_NAME/css/
     * Ищет, начиная с текущего шаблона и по цепочке до дефолтного
     *
     * @param string $name Имя файла без расширения
     * @return boolean
     */
    public function addTplCSSName($name) {
        return $this->addCSS($this->getTemplateStylesFileName($name));
    }
    public function addTplCSSNameFromContext($name, $request = false) {
        if(!$request){ $request = cmsCore::getInstance()->request; }
        if($request->isAjax()){
            return $this->insertCSS($this->getTemplateStylesFileName($name));
        }

        return $this->addCSS($this->getTemplateStylesFileName($name), false);
    }
    public function addMainTplCSSName($name) {
        return $this->addMainCSS($this->getTemplateStylesFileName($name));
    }

    public function insertJS($file, $comment = ''){

        if (!$file) { return false; }

        if(!is_array($file)){
            $hash = md5($file);
            if (isset($this->insert_js[$hash])) { return false; }
            $this->insert_js[$hash] = $file;

            // атрибут rel="forceLoad" добавлен для nyroModal
            echo $this->getJSTag($file, $comment, array('rel' => 'forceLoad'));

            return true;
        }

        foreach($file as $f){
            $this->insertJS($f, $comment);
        }

        return true;
	}

    public function insertCSS($file){

        if (!$file) { return false; }

        if(!is_array($file)){
            $hash = md5($file);
            if (isset($this->insert_css[$hash])) { return false; }
            $this->insert_css[$hash] = $file;

            echo $this->getCSSTag($file);

            return true;
        }

        foreach($file as $f){
            $this->insertCSS($f);
        }

        return true;

    }

    /**
     * Подключает js файл на страницу в зависимости от контекста исходного запроса
     * @param string $file
     * @param string $comment
     * @return bool
     */
    public function addJSFromContext($file, $comment='', $request = false) {

        if(!$request){ $request = cmsCore::getInstance()->request; }

        if($request->isAjax()){
            return $this->insertJS($file, $comment);
        }

        return $this->addJS($file, $comment, false);
    }

    /**
     * Подключает css файл на страницу в зависимости от контекста исходного запрос
     * @param string $file
     * @return bool
     */
    public function addCSSFromContext($file, $request = false) {

        if(!$request){ $request = cmsCore::getInstance()->request; }

        if($request->isAjax()){
            return $this->insertCSS($file);
        }

        return $this->addCSS($file, false);
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
            $value = var_export(htmlspecialchars(constant($phrase)), true);
            $output .= "var {$phrase} = {$value};";
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

        $files = array_merge($this->head_main_js, $this->head_js);

        $cache_hash = md5(serialize($files));
        $cache_file = "cache/static/js/scripts.{$cache_hash}.js";
        $cache_file_path = $this->site_config->root_path . $cache_file;

        if (file_exists($cache_file_path)) { return $cache_file; }

        $merged_contents = '';

        foreach($files as $file){
            if (in_array($file, $this->head_js_no_merge)) { continue; }
            $file_path = $this->site_config->root_path . strtok($file, '?');
            $contents = file_get_contents($file_path);
            $merged_contents .= "\n".$contents;
        }

        $merged_contents = preg_replace('@/\\*[\\s\\S]*?\\*/@', '', $merged_contents);
        $merged_contents = str_replace(["\t"], '', $merged_contents);
        $merged_contents = preg_replace('/ {2,}/', '', $merged_contents);

        file_put_contents($cache_file_path, trim($merged_contents));

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

        $files = array_merge($this->head_main_css, $this->head_css);

        $cache_hash = md5(serialize($files));
        $cache_file = "cache/static/css/styles.{$cache_hash}.css";
        $cache_file_path = $this->site_config->root_path . $cache_file;

        if (file_exists($cache_file_path)) { return $cache_file; }

        $merged_contents = '';

        foreach($files as $file){
            if (in_array($file, $this->head_css_no_merge)) { continue; }
            $file_path = $this->site_config->root_path . strtok($file, '?');
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

        preg_match_all('/url\(([^)]+)\)/si', $css, $matches);

        if ($matches){

            $css_rel_url = $this->site_config->root . dirname($css_file);

            list($fulls, $urls) = $matches;

            foreach($urls as $i => $url){

                $abs_url = trim($url, '\'" ');

                $is_root = strpos($abs_url, '/') === 0;
                $is_http = strpos($abs_url, 'http') === 0;
                $is_data = strpos($abs_url, 'data:image') === 0;

                if ($is_data) { continue; }

                if ($is_root){

                    $abs_url = $this->site_config->host . $abs_url;

                } else

                if ($is_http){

                    continue;

                } else {

                    $abs_url = $this->site_config->host . '/' . files_normalize_path($css_rel_url . '/' . $abs_url);

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
     * @return $this
     */
    public function setLayout($layout){

        $this->layout = $layout;

        return $this;

    }
    /**
     * Устанавливает параметры шаблон скелета
     * @param array $layout_params
     * @return $this
     */
    public function setLayoutParams($layout_params){

        $this->layout_params = $layout_params;

        return $this;

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
     * @param string $name Имя шаблона
     * @return boolean|string
     */
    public function getSchemeHTML($name = '') {

        $scheme_file = $this->getSchemeHTMLFile($name);
        if (!$scheme_file) { return false; }

        ob_start();

        include($scheme_file);

        return ob_get_clean();

    }

    /**
     * Возвращает путь к файлу схемы позиций виджетов
     * @param string $name
     * @return boolean|string
     */
    public function getSchemeHTMLFile($name = '') {

        $name = $name ? $name : $this->name;

        $scheme_file = $this->site_config->root_path.self::TEMPLATE_BASE_PATH.$name.'/scheme.html';

        if (!is_readable($scheme_file)) { return false; }

        return $scheme_file;

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

    /**
     * Устанавливает название глобального шаблона
     * @param string $name
     * @return \cmsTemplate
     */
    public function setName($name){

        $this->name = $name;

        $this->path = $this->site_config->root_path.self::TEMPLATE_BASE_PATH.$this->name;

        return $this;

    }

    /**
     * Устанавливает цепочку наследования шаблона
     * @param array $names Массив названий шаблонов в приоритетном порядке от меньшего к большему
     * @return \cmsTemplate
     */
    public function setInheritNames($names = array()) {

        $this->inherit_names = array('default');

        if($names){
            foreach ($names as $name) {
                $this->inherit_names[] = $name;
            }
        }

        if($this->name !== 'default'){
            $this->inherit_names[] = $this->name;
        }

        $this->inherit_names = array_reverse($this->inherit_names);

        return $this;

    }

    /**
     * Возвращает путь к файлу шаблона
     * @param string $relative_path Путь относительно корня шаблона. Без первого слеша
     * @param boolean $return_abs_path Возвращать полный путь в файловой системе, по умолчанию true
     * @return string | boolean
     */
    public function getTplFilePath($relative_path, $return_abs_path = true) {

        if(!is_array($relative_path)){

            $exists = false;

            foreach ($this->inherit_names as $name) {
                $file = self::TEMPLATE_BASE_PATH.$name.'/'.$relative_path;
                if(is_readable($this->site_config->root_path.$file)){
                    if($return_abs_path){
                        $exists = $this->site_config->root_path.$file;
                    } else {
                        $exists = $file;
                    }
                    break;
                }
            }

            if(!$exists){
                $this->not_found_tpls[] = $file;
            }

            return $exists;
        }

        foreach($relative_path as $key => $value){
            $relative_path[$key] = $this->getTplFilePath($value, $return_abs_path);
        }

        return $relative_path;

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Сохраняет ссылку на текущий контроллер
     * @param object $controller_obj
     */
    public function setContext($controller_obj){
        if ($this->controller) { $this->controllers_queue[] = $this->controller; }
        $this->controller = $controller_obj;
        return $this;
    }

    /**
     * Возвращает объект текущего контроллера
     * @return object
     */
    public function getContext(){
        return $this->controller;
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
     * @param string $filename Путь относительно корня шаблона
     * @param boolean $is_check Если true, то не выдаст фатальную ошибку в случае отсутствия файла
     * @return string
     */
    public function getTemplateFileName($filename, $is_check = false){

        $tpl_file = $this->getTplFilePath($filename.'.tpl.php');

        if (!$tpl_file){
            if (!$is_check){
                $last_not_found_tpl = end($this->not_found_tpls);
                cmsCore::error(ERR_TEMPLATE_NOT_FOUND . ': ' . $this->site_config->root.$last_not_found_tpl);
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
    public function getStylesFileName($controller_name = '', $subfolder = '') {

        if (!$controller_name) { $controller_name = $this->controller->name; }
        if ($subfolder) { $subfolder = $subfolder.'/'; }

        return $this->getTplFilePath('controllers/'.$controller_name.'/'.$subfolder.'styles.css', false);

    }

    /**
     * Возвращает путь к JavaScript-файлу, определяя его наличие в собственном шаблоне
     * @param string $filename
     * @return string
     */
    public function getJavascriptFileName($filename){

        if(!is_array($filename)){
            return $this->getTplFilePath('js/'.$filename.'.js', false);
        }

        foreach($filename as $key => $value){
            $filename[$key] = $this->getJavascriptFileName($value);
        }

        return $filename;

    }

    /**
     * Возвращает путь к CSS-файлу, определяя его наличие в собственном шаблоне
     * @param string|array $filename Название файла (массив файлов) без расширения
     * @return string
     */
    public function getTemplateStylesFileName($filename){

        if(!is_array($filename)){
            return $this->getTplFilePath('css/'.$filename.'.css', false);
        }

        foreach($filename as $key => $value){
            $filename[$key] = $this->getTemplateStylesFileName($value);
        }

        return $filename;

    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет текст к выводу
     *
     * @param string $text
     */
    public function renderText($text){

        echo $this->addOutput($text);

    }

    /**
     * Выводит JSON строку и завершает работу
     *
     * @param array $data Массив для вывода
     * @param boolean $with_header Вывод вместе с хидером Content-type
     */
    public function renderJSON($data, $with_header = false) {

        if(ob_get_length()) { ob_end_clean(); }

    	if ($with_header) {
            header('Content-type: application/json; charset=utf-8');
        }

        $json = json_encode($data);

        if($json === false){
            $json = json_encode(array(
                'success' => false,
                'errors'  => true,
                'error'   => json_last_error_msg()
            ));
        }

        $this->controller->halt($json);

    }

    /**
     * Формирует HTML код файла шаблона (в папке шаблонов текущего компонента)
     * И добавляет его в заданный блок
     *
     * @param string $position Название позиции
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @return $this
     */
    public function renderBlock($position, $tpl_file, $data = array()) {

        $result = $this->render($tpl_file, $data, new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $this->addToBlock($position, $result);

        return $this;

    }

    /**
     * Формирует и возвращает HTML код файла шаблона
     * Меняя контекст текущего контроллера на переданный
     *
     * @param object $controller
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @return string HTML код
     */
    public function renderInternal($controller, $tpl_file, $data = array()) {

        $this->setContext($controller);

        $result = $this->render($tpl_file, $data, new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $this->restoreContext();

        return $result;

    }

    /**
     * Формирует HTML код файла шаблона (в папке шаблонов текущего компонента)
     * И подключает css файл контроллера (если есть)
     * Если $tpl_file массив, то название шаблона равно названию текущего экшена
     *
     * @param string|array $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @param object $request Объект запроса
     * @return string
     */
    public function render($tpl_file, $data = [], $request = false) {

        if(is_array($tpl_file)){
            $data = $tpl_file; $tpl_file = $this->controller->current_template_name;
        }

        if(empty($this->controller->template_disable_auto_insert_css)){

            $css_file = $this->getStylesFileName();

            if ($css_file){ $this->addCSSFromContext($css_file, $request); }
        }

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        return $this->processRender($tpl_file, $data, $request);

    }

    /**
     * Печатает HTML код шаблона и завершает работу
     *
     * @param string|array $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     */
    public function renderPlain($tpl_file, $data = array()) {

        if(is_array($tpl_file)){
            $data = $tpl_file; $tpl_file = $this->controller->current_template_name;
        }

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        $this->processRender($tpl_file, $data, new cmsRequest($this->controller->request->getData(), cmsRequest::CTX_AJAX));

    }

    /**
     * Формирует HTML код файла шаблона,
     * учитывая контекст вызова
     *
     * @param string $tpl_file Полный путь к файлу шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @param object $request Объект запроса
     * @return mixed
     */
    public function processRender($tpl_file, $data = array(), $request = false) {

        if (!$request) { $request = $this->controller->request; }

        $hook_name = 'process_render_'.$this->controller->name.'_'.basename(str_replace('-', '_', $tpl_file), '.tpl.php');

        list($tpl_file, $data, $request) = cmsEventsManager::hook($hook_name, [$tpl_file, $data, $request]);

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
     * Печатает HTML код шаблона $tpl_file (в папке шаблонов текущего компонента)
     * Предполагается, что вызов этого метода выполняется
     * из другого шаблона текущего контроллера
     *
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     */
    public function renderChild($tpl_file, $data = array()) {
        $this->renderControllerChild($this->controller->name, $tpl_file, $data);
    }

    /**
     * Печатает HTML код шаблона $tpl_file (в папке шаблонов $controller_name компонента)
     * Предполагается, что вызов этого метода выполняется
     * из другого шаблона текущего контроллера
     *
     * @param string $controller_name Имя контроллера
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @param object $request Объект запроса
     */
    public function renderControllerChild($controller_name, $tpl_file, $data = array(), $request = false) {

        if (!$request) { $request = $this->controller->request; }

        $tpl_file = $this->getTemplateFileName('controllers/'.$controller_name.'/'.$tpl_file);

        $hook_name = 'process_render_'.$controller_name.'_'.basename(str_replace('-', '_', $tpl_file), '.tpl.php');

        list($tpl_file, $data, $request) = cmsEventsManager::hook($hook_name, [$tpl_file, $data, $request]);

        extract($data); include($tpl_file);

    }

    /**
     * Формирует HTML код шаблона и возвращает его
     * в виде строки
	 *
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @return string
     */
	public function getRenderedChild($tpl_file, $data = array()) {

        ob_start();

        $this->renderControllerChild($this->controller->name, $tpl_file, $data);

		return ob_get_clean();

	}

    /**
     * Печатает HTML код формы
     *
     * @param object $form Объект формы
     * @param array $data Массив данных формы
     * @param array $attributes Атрибуты формы
     * @param mixed $errors Массив ошибок полей
     */
    public function renderForm($form, $data, $attributes = [], $errors = false) {

        $attributes = array_replace_recursive([
            'is_ajax'      => false,
            'submit'       => ['title' => LANG_SAVE, 'show' => true],
            'cancel'       => ['title' => LANG_CANCEL, 'href' => href_to_home(), 'show' => false],
            'action'       => '',
            'append_html'  => '',
            'prepend_html' => '',
            'form_id'      => md5(microtime(true)),
            'method'       => 'post'
        ], $attributes);

        if($attributes['method'] === 'ajax'){
            $attributes['method']  = 'post';
            $attributes['is_ajax'] = true;
        }

        $form_tpl_file = 'form';

        if(!empty($attributes['form_tpl_file'])){
            $form_tpl_file = $attributes['form_tpl_file'];
        }

        $this->renderAsset('ui/'.$form_tpl_file, [
            'form_tpl_file' => $form_tpl_file,
            'form'          => $form,
            'data'          => $data,
            'attributes'    => $attributes,
            'errors'        => $errors
        ]);

    }

    /**
     * Печатает шаблон Grid таблицы
     *
     * @param string $source_url URL ajax запроса списка данных
     * @param array $grid Данные Grid таблицы
     */
    public function renderGrid($source_url, $grid){

        $this->addTplJSName('datagrid');

        if ($grid['options']['is_pagination']){
            $this->addTplJSName('datagrid-pagination');
        }

        if ($grid['options']['is_draggable']){
            $this->addTplJSName('datagrid-drag');
        }

        $grid['source_url'] = $source_url;

        $this->renderAsset('ui/grid-data', $grid);

    }

    public function renderGridRowsJSON($grid, $dataset, $total = 1, $pages_count = 1) {

        $rows = $titles = $classes = [];
        $row_index = 0;

        //
        // проходим по всем строкам из набора данных
        //
        if ($total && $dataset){
            foreach($dataset as $row){

                $cell_index = 0;
                $editable_index = 1;
                $editable_count = count(array_filter($grid['columns'], function($element) { return isset($element['editable']); }));

                // вычисляем содержимое для каждой колонки таблицы
                foreach($grid['columns'] as $field => $column){

                    $titles[$cell_index] = isset($column['title']) ? $column['title'] : '';
                    $classes[$cell_index] = isset($column['class']) ? $column['class'] : '';

                    if (isset($column['key_alias'])){
                        $field = $column['key_alias'];
                    }

                    if (!is_array($row[$field])){
                        $value = html($row[$field], false);
                    } else {
                        $value = $row[$field];
                    }

                    if ($value === null) { $value = ''; }

                    if (isset($column['flag']) && $column['flag']){

                        if (isset($column['flag_handler'])){
                            $value = $column['flag_handler']($value, $row);
                        }

						if (isset($column['flag_on'])){
							$is_flag_on = $value == $column['flag_on'];
						} else {
							$is_flag_on = (int)$value;
						}

                        $flag_class = $column['flag']===true ? 'flag' : $column['flag'];

						$flag_toggle_url = isset($column['flag_toggle']) ? $column['flag_toggle'] : false;

						if ($flag_toggle_url){
							$flag_toggle_url = string_replace_keys_values($flag_toggle_url, $row);
						}

						$flag_content = $flag_toggle_url ? '<a href="'.$flag_toggle_url.'"></a>' : '';

                        $value = '<div class="flag_trigger '.($is_flag_on > 0 ? "{$flag_class}_on" : ($is_flag_on < 0 ? "{$flag_class}_middle" : "{$flag_class}_off")).'" data-class="'.$flag_class.'">'.$flag_content.'</div>';

                    }

                    if (isset($column['handler'])){
                        $value = $column['handler']($value, $row);
                    }

                    // если из значения нужно сделать ссылку, то парсим шаблон
                    // адреса, заменяя значения полей
                    if (isset($column['href'])){
                        if (isset($column['href_handler'])){
                            $is_active = $column['href_handler']($row);
                        } else {
                            $is_active = true;
                        }
                        if($is_active){
                            $column['href'] = string_replace_keys_values_extended($column['href'], $row);
                            $value = '<a href="'.$column['href'].'">'.$value.'</a>';
                        }
                    }

                    if(!empty($column['editable']['table'])){
                        if(!empty($row['id'])){
                            $save_action = href_to('admin', 'inline_save', array(urlencode($column['editable']['table']), $row['id']));
                        }
                        if(!empty($column['editable']['save_action'])){
                            $save_action = string_replace_keys_values_extended($column['editable']['save_action'], $row);
                        }
                        $attributes = array('autocomplete' => 'off');
                        if(!empty($column['editable']['attributes'])){
                            foreach ($column['editable']['attributes'] as $akey => $avalue) {
                                if(is_string($avalue)){
                                    $attributes[$akey] = string_replace_keys_values_extended($avalue, $row);
                                } else {
                                    $attributes[$akey] = $avalue;
                                }
                            }
                        }
                        if(!empty($save_action)){
                            $value = '<div class="grid_field_value '.$field.'_grid_value '.((isset($column['href']) ? 'edit_by_click' : '')).'">'.$value.'</div>';
                            $value .= '<div class="grid_field_edit '.((isset($column['href']) ? 'edit_by_click' : '')).'">'.html_input('text', $field, $row[$field], $attributes);
                            if($editable_index == $editable_count){
                                $value .= html_button(LANG_SAVE, '', '', array('data-action'=>$save_action, 'class'=>'inline_submit'));
                            }
                            $value .= '</div>';

                            $editable_index++;

                        }
                    }

                    $rows[$row_index][] = $value;

                    $cell_index++;

                }

                // если есть колонка действий, то формируем набор ссылок
                // для текущей строки
                if ($grid['actions']){

                    $titles[$cell_index] = LANG_CP_ACTIONS;
                    $classes[$cell_index] = '';

                    $actions_html = '<div class="actions">';

                    foreach($grid['actions'] as $action){

                        $confirm_attr = '';

                        if (isset($action['handler'])){
                            $is_active = $action['handler']($row);
                        } else {
                            $is_active = true;
                        }

                        if ($is_active){

                            // парсим шаблон адреса, заменяя значения полей
                            if (isset($action['href'])){
                                $action['href'] = string_replace_keys_values_extended($action['href'], $row);
                            }

                            // парсим шаблон запроса подтверждения, заменяя значения полей
                            if (isset($action['confirm'])){
                                $action['confirm'] = string_replace_keys_values_extended($action['confirm'], $row);
                                $confirm_attr = 'onclick="if(!confirm(\''.html($action['confirm'], false).'\')){ return false; }"';
                            }

                            // все действия с подтверждением снабжаем csrf_token
                            if ($confirm_attr && !empty($action['href'])){
                                $action['href'] .= (strpos($action['href'], '?') !== false ? '&' : '?').'csrf_token='.cmsForm::getCSRFToken();
                            }

                            $actions_html .= '<a data-toggle="tooltip" data-placement="top" class="'.$action['class'].'" href="'.$action['href'].'" title="'.$action['title'].'" '.$confirm_attr.'></a>';

                        }

                    }

                    $actions_html .= '</div>';

                    $rows[$row_index][] = $actions_html;

                    $cell_index++;

                }

                $row_index++;
            }
        }

        $columns = array();
        if($grid['options']['load_columns']){
            $clear_filter = '<a class="clear_filter" href="#" onclick="return icms.datagrid.resetFilter(this)"></a>';
            foreach($grid['columns'] as $name=>$column){
                if(!empty($column['filter']) && $column['filter'] !== 'none'){
                    $filter_attributes = !empty($column['filter_attributes']) ? $column['filter_attributes'] : array();
                    if(strpos($name, 'date_') === 0){
                        $filter = html_datepicker('filter_'.$name, (isset($grid['filter'][$name]) ? $grid['filter'][$name] : ''), array_merge($filter_attributes, array('id'=>'filter_'.$name, 'rel'=>$name, 'class' => 'input form-control-sm')), array('minDate'=>date(cmsConfig::get('date_format'), 86400))).$clear_filter;
                    }
                    elseif(!empty($column['filter_select'])){
                        $filter = html_select('filter_'.$name, (is_array($column['filter_select']['items']) ? $column['filter_select']['items'] : $column['filter_select']['items']($name)), (isset($grid['filter'][$name]) ? $grid['filter'][$name] : ''), array_merge($filter_attributes, array('id'=>'filter_'.$name, 'rel'=>$name, 'class'=>'custom-select custom-select-sm')));
                    } else {
                        $filter = html_input('text', 'filter_'.$name, (isset($grid['filter'][$name]) ? $grid['filter'][$name] : ''), array_merge($filter_attributes, array('id'=>'filter_'.$name, 'rel'=>$name, 'class' => 'form-control-sm'))).$clear_filter;
                    }
                } else {
                    $filter = '';
                }
                $columns[] = array(
                    'sortable'  => $grid['options']['is_sortable'],
                    'width'     => isset($column['width']) ? $column['width'] : '',
                    'title'     => isset($column['title']) ? $column['title'] : '',
                    'name'      => $name,
                    'filter'    => $filter,
                    'order_to'  => !empty($grid['filter']['order_by']) && $grid['filter']['order_by'] === $name && !empty($grid['filter']['order_to']) ? $grid['filter']['order_to'] : ''
                );
            }
            if($grid['actions']){
                $columns[] = array(
                    'sortable'  => false,
                    'width'     => sizeof($grid['actions']) * 30,
                    'title'     => LANG_CP_ACTIONS,
                    'name'      => 'dg_actions',
                    'filter'    => ''
                );
            }
        }

        $result = array(
            'classes'     => $classes,
            'titles'      => $titles,
            'rows'        => $rows,
            'pages_count' => $pages_count,
            'total'       => $total,
            'columns'     => $columns
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

        $this->addTplJSName('datagrid');

        $this->renderAsset('ui/grid-perms', [
            'rules'      => $rules,
            'groups'     => $groups,
            'values'     => $values,
            'submit_url' => $submit_url
        ]);

    }

    /**
     * Выводит меню
     * @param array $menu Массив пунктов меню
     * @param array $active_ids Массив активных пунктов меню
     * @param string $css_class CSS класс контейнера пунктов меню
     * @param integer $max_items Максимальное количество видимых пунктов
     * @param string $template Название файла шаблона меню в assets/ui/
     * @param string $menu_title Название(подпись) меню
     */
    public function renderMenu($menu, $active_ids = array(), $css_class = 'menu', $max_items = 0, $template = 'menu', $menu_title = '') {

        $this->renderAsset('ui/'.$template, [
            'menu'       => $menu,
            'active_ids' => $active_ids,
            'css_class'  => $css_class,
            'max_items'  => $max_items,
            'template'   => $template,
            'menu_title' => $menu_title
        ]);

    }

    /**
     * Формирует и печатает HTML assets шаблон
     *
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @param object $request Объект запроса
     */
    public function renderAsset($tpl_file, $data = array(), $request = false) {

        $tpl_file = $this->getTemplateFileName('assets/' . $tpl_file);

        $file_name = basename($tpl_file, '.tpl.php');

        $hook_name = str_replace('-', '_', 'render_asset_'.basename(str_replace($file_name.'.tpl.php', '', $tpl_file)).'_'.$file_name);

        list($tpl_file, $data, $request) = cmsEventsManager::hook($hook_name, [$tpl_file, $data, $request]);

        extract($data); include($tpl_file);

        if($request){
            if ($request->isAjax()) {
                exit();
            }
        }

    }

    /**
     * Формирует и возвращает в виде строки HTML код assets шаблона
     *
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @return string
     */
    public function getRenderedAsset($tpl_file, $data = array()) {

        ob_start();

        $this->renderAsset($tpl_file, $data);

        return ob_get_clean();

    }

    /**
     * Формирует и возвращает в виде строки HTML код поля формы
     *
     * @param string $field_type Имя поля
     * @param array $data Массив параметров, передаваемых в шаблон
     * @return string
     */
    public function renderFormField($field_type, $data = array()) {
        $input_js_file = $this->getJavascriptFileName('fields/'.$field_type.'_input');
        if($input_js_file){
            $this->addJSFromContext($input_js_file);
        }
        return $this->getRenderedAsset('fields/'.$field_type, $data);
    }

//============================================================================//
//============================================================================//
    /**
     * Возвращает массив шаблонов по маске,
     * учитывая наследование
     *
     * @param string $path Путь относительно директории шаблона
     * @param string $pattern Паттерн поиска файлов
     * @param string $template_name Название шаблона
     * @return array
     */
    public function getAvailableTemplatesFiles($path, $pattern='*.*', $template_name = false) {

        if(!$template_name){
            $template_name = $this->site_config->template;
        }

        $files = $__files = [];

        $inherit_names = array('default');
        if(file_exists($this->site_config->root_path.self::TEMPLATE_BASE_PATH.$template_name.'/inherit.php')){
            $names = include $this->site_config->root_path.self::TEMPLATE_BASE_PATH.$template_name.'/inherit.php';
            if($names){
                foreach ($names as $name) {
                    $inherit_names[] = $name;
                }
            }
        }
        if($template_name !== 'default'){
            $inherit_names[] = $template_name;
        }
        $inherit_names = array_reverse($inherit_names);

        foreach ($inherit_names as $name) {
            $_files = cmsCore::getFilesList(self::TEMPLATE_BASE_PATH.$name.'/'.$path, $pattern, true);
            $files = array_merge($files, $_files);
        }

        $files = array_unique($files);

        if($files){
            foreach ($files as $file) {
                $k = str_replace('.tpl', '', $file);
                $__files[$k] = $k;
            }
            $files = $__files; asort($files);
        }

        return $files;

    }


    /**
     * Возвращает все названия шаблонов для списка записей типов контента
     * @return array
     */
    public function getAvailableContentListStyles(){

        $files = $this->getAvailableTemplatesFiles('content', 'default_list*.tpl.php');
        if (!$files) { return []; }

        $styles = [];

        foreach($files as $file){

            preg_match('/^default_list_([a-z0-9_\-]*)$/i', $file, $matches);

            if (!$matches){
                $styles[''] = 'default_list (' . LANG_CP_LISTVIEW_STYLE_BASIC .')';
            } else {
                $constant_name = 'LANG_CP_LISTVIEW_STYLE_'.mb_strtoupper($matches[1]);
                $title = defined($constant_name) ? '('.constant($constant_name).')' : '';
                $styles[$matches[1]] = pathinfo($file, PATHINFO_FILENAME).$title;
            }

        }

        return $styles;

    }

    /**
     * Возвращает все названия шаблонов для просмотра записи типа контента
     * Такие файлы должны называться по принципу: CTYPENAME_item_TPLNAME.tpl.php
     *
     * @param string $ctype_name Имя типа контента
     * @return array
     */
    public function getAvailableContentItemStyles($ctype_name){

        $files = $this->getAvailableTemplatesFiles('content', $ctype_name.'_item_*.tpl.php');
        if (!$files) { return []; }

        $styles = [];

        foreach($files as $file){

            preg_match('/^'.$ctype_name.'_item_([a-z0-9_\-]*)$/i', $file, $matches);

            if(!empty($matches[1])){
                $styles[$matches[1]] = pathinfo($file, PATHINFO_BASENAME);
            }

        }

        return $styles;

    }

    /**
     * Рендерит шаблон списка записей контента
     * @param array $ctype Массив данных типа контента
     * @param array $data Массив данных для шаблона
     * @param mixed $request Объект запроса
     * @return string
     */
    public function renderContentList($ctype, $data = array(), $request = false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype['name'].'_list', true);

        if (!$tpl_file){

            $style = '';

            if(!empty($ctype['options']['list_style'])){
                if(is_array($ctype['options']['list_style'])){
                    $style = $ctype['options']['list_style'][0] ? '_'.$ctype['options']['list_style'][0] : '';
                } else {
                    $style = '_'.$ctype['options']['list_style'];
                }
            }

            $list_type = $this->controller->getListContext();

            if(isset($ctype['options']['context_list_style'][$list_type])){
                $style = $ctype['options']['context_list_style'][$list_type] ? '_'.$ctype['options']['context_list_style'][$list_type] : '';
            }

            $tpl_file = $this->getTemplateFileName('content/default_list'.$style);

        }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

    /**
     * Рендерит шаблон просмотра записи контента
     * @param string $ctype_name Имя типа контента
     * @param array $data Массив данных для шаблона
     * @param mixed $request Объект запроса
     * @return string
     */
    public function renderContentItem($ctype_name, $data = array(), $request = false){

        // опеределен ли в записи шаблон
        if(!empty($data['item']['template'])){
            $template_name = $ctype_name.'_item_'.$data['item']['template'];
        } else {
            // или есть шаблон для типа контента
            $template_name = $ctype_name.'_item';
        }

        $tpl_file = $this->getTemplateFileName('content/'.$template_name, true);

        if (!$tpl_file){ $tpl_file = $this->getTemplateFileName('content/default_item'); }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

//============================================================================//
//============================================================================//

    /**
     * Выводит окончательный вид страницы в браузер
     */
    public function renderPage(){

        $core = cmsCore::getInstance();

        $config = $this->site_config;

        $layout = $this->getLayout();

        $template_file = $this->getTplFilePath($layout.'.tpl.php');

        $device_type = cmsRequest::getDeviceType();

        if($template_file){

            if($this->layout_params){
                extract($this->layout_params);
            }

            ob_start();

            include($template_file);

            $html = cmsEventsManager::hook('render_page', ob_get_clean());

            if (!$config->min_html){
                echo $html;
            } else {
                echo html_minify($html);
            }

        } else {
            cmsCore::error(ERR_TEMPLATE_NOT_FOUND. ': '. $this->name.':'.$layout);
        }

    }

//============================================================================//
//============================================================================//

    public function renderWidget($widget, $data = array()) {

        $tpl_path = cmsCore::getWidgetPath($widget->name, $widget->controller);

        $tpl_file = $this->getTemplateFileName($tpl_path . '/' . $widget->getTemplate());

        $hook_name = 'render_widget_'.($widget->controller ? $widget->controller.'_' : '').$widget->name.'_'.basename(str_replace('-', '_', $tpl_file), '.tpl.php');

        list($widget, $tpl_file, $data) = cmsEventsManager::hook($hook_name, [$widget, $tpl_file, $data]);

        extract($data);

        ob_start(); include($tpl_file);

        $html = ob_get_clean();

        if (!$html){ return $this; }

        if (empty($widget->is_tab_prev)){
            $this->widgets_group_index++;
        }

        $this->widgets[$widget->position][$this->widgets_group_index][] = array(
            'id'          => $widget->id,
            'bind_id'     => $widget->bind_id,
            'title'       => $widget->is_title ? $widget->title : false,
            'links'       => isset($widget->links) ? $widget->links : false,
            'wrapper'     => $widget->getWrapper(),
            'class'       => isset($widget->css_class) ? $widget->css_class : false,
            'class_title' => isset($widget->css_class_title) ? $widget->css_class_title : false,
            'class_wrap'  => isset($widget->css_class_wrap) ? $widget->css_class_wrap : false,
            'body'        => $html
        );

        return $this;

    }

    /**
     * Добавляет произвольный HTML код на позицию виджета
     *
     * @param string $position
     * @param string $html
     * @return $this
     */
    public function renderWidgetHtml($position, $html) {

        $this->widgets_group_index++;

        $this->widgets[$position][$this->widgets_group_index][] = array(
            'id'          => false,
            'bind_id'     => false,
            'title'       => false,
            'links'       => false,
            'wrapper'     => false,
            'class'       => false,
            'class_title' => false,
            'class_wrap'  => false,
            'body'        => $html
        );

        return $this;

    }


//============================================================================//
//============================================================================//

    public function getInheritTemplates(){
        if(file_exists($this->path . '/inherit.php')){
            return include $this->path . '/inherit.php';
        }
        return array();
    }

    public function hasOptions(){
        return file_exists($this->path . '/options.form.php');
    }

    public function getOptionsForm(){

        if (!$this->hasOptions()){ return false; }

        cmsCore::loadTemplateLanguage($this->name);

        $form_file            = $this->path . '/options.form.php';
        $deprecated_form_name = 'template_options';
        $form_name            = $this->name . '_template_options';
        $form                 = null;

        // $form = cmsForm::getForm($form_file, $form_name);
        // для совместимости форм шаблонов делаем здесь то, что делается в cmsForm::getForm, но с проверкой класса
        // убрать через пару релизов. http://docs.instantcms.ru/dev/templates/options

        include_once $form_file;

        $form_class = 'form' . string_to_camel('_', $form_name);

        if(!class_exists($form_class)){
            $form_class = 'form' . string_to_camel('_', $deprecated_form_name);
        }

        if(class_exists($form_class)){

            $form = new $form_class();

            $form->setStructure( $form->init() );

        }

        if ($form === null) { $form = new cmsForm(); }

        return $form;

    }

    public function getOptions(){

		if (!$this->hasOptions()){ return false; }

        cmsCore::loadTemplateLanguage($this->name);

        return $this->loadOptions();

    }

    public function loadOptions(){

        if (!$this->hasOptions()){ return false; }

        $options_file = $this->site_config->root_path . "system/config/theme_{$this->name}.yml";

        if (!is_readable($options_file)){ return array(); }

        $options_yaml = file_get_contents($options_file);

        return cmsModel::yamlToArray($options_yaml);

    }

    public function saveOptions($options){

        $options_file = $this->site_config->root_path . "system/config/theme_{$this->name}.yml";

        if(file_exists($options_file)){
            if(!is_writable($options_file)){
                return false;
            }
        } else {
            if(!is_writable(dirname($options_file))){
                return false;
            }
        }

        $options_yaml = cmsModel::arrayToYaml($options);

        $success = file_put_contents($options_file, $options_yaml);

        if ($success && function_exists('opcache_invalidate')) { @opcache_invalidate($options_file, true); }

        return $success;

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

        $config = $this->site_config;

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

}
