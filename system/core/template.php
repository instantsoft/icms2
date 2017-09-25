<?php

class cmsTemplate {

    private static $instance;

    public $name;
    public $path;
    protected $inherit_names = array();
    protected $layout = 'main';
    protected $output;
    protected $options;
    protected $site_config;

	protected $head = array();
	protected $head_main_css = array();
	protected $head_css = array();
	protected $head_main_js = array();
	protected $head_js = array();
	protected $insert_js = array();
	protected $insert_css = array();
	protected $head_js_no_merge = array();
	protected $head_css_no_merge = array();
	protected $title;
	protected $title_item;
	protected $metadesc;
	protected $metadesc_item;
	protected $metakeys;
	protected $metakeys_item;

    protected $breadcrumbs = array();
    protected $menus = array();
    protected $db_menus = array();
    protected $menu_loaded = false;
    protected $not_found_tpls = array();

    protected $widgets = array();
    protected $widgets_group_index = 0;

    protected $controller;
    protected $controllers_queue;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
            // подключаем хелпер основного шаблона
            if(!cmsCore::includeFile('templates/'.self::$instance->getName().'/assets/helper.php')){
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
            $controller = cmsCore::getInstance()->uri_controller;
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
     * Выводит головные теги страницы
     * @param boolean $is_seo_meta Выводить мета теги
     * @param boolean $print_js Выводить javascript теги
     * @param boolean $print_css Выводить CSS теги
     */
	public function head($is_seo_meta=true, $print_js = true, $print_css = true){

        cmsEventsManager::hook('before_print_head', $this);

        if ($is_seo_meta){
			if (!empty($this->metakeys)){
				echo "\t". '<meta name="keywords" content="'.htmlspecialchars(!empty($this->metakeys_item) ? string_replace_keys_values_extended($this->metakeys, $this->metakeys_item) : $this->metakeys).'">' . "\n";
			}
			if (!empty($this->metadesc)){
				echo "\t". '<meta name="description" content="'.htmlspecialchars(!empty($this->metadesc_item) ? string_replace_keys_values_extended($this->metadesc, $this->metadesc_item) : $this->metadesc).'">' ."\n";
			}
        }

		foreach ($this->head as $id=>$tag){	echo "\t". $tag . "\n";	}

        if($print_css){
            $this->printCssTags();
        }

        if($print_js){
            $this->printJavascriptTags();
        }

	}

    /**
     * Выводит javascript теги
     */
    public function printJavascriptTags() {

        if (!$this->site_config->merge_js){
            foreach ($this->head_main_js as $id=>$file){ echo "\t". $this->getJSTag($file) . "\n";	}
            foreach ($this->head_js as $id=>$file){	echo "\t". $this->getJSTag($file) . "\n";	}
        } else {
            $tag = "\t". $this->getJSTag( $this->getMergedJSPath() ) . "\n";
            echo $tag;
            foreach ($this->head_js_no_merge as $id=>$file){ echo "\t". $this->getJSTag($file) . "\n";	}
        }

    }

    /**
     * Выводит CSS теги
     */
    public function printCssTags() {

        if (!$this->site_config->merge_css){
            foreach ($this->head_main_css as $id=>$file){	echo "\t". $this->getCSSTag($file) . "\n";	}
            foreach ($this->head_css as $id=>$file){	echo "\t". $this->getCSSTag($file) . "\n";	}
        } else {
            $tag = "\t". $this->getCSSTag( $this->getMergedCSSPath() ) . "\n";
            echo $tag;
            foreach ($this->head_css_no_merge as $id=>$file){ echo "\t". $this->getCSSTag($file) . "\n";	}
        }

    }

	/**
	 * Выводит заголовок текущей страницы
	 */
	public function title(){
    	echo htmlspecialchars(!empty($this->title_item) ? string_replace_keys_values($this->title, $this->title_item) : $this->title);
	}

	/**
	 * Выводит название сайта
	 */
	public function sitename(){
		echo htmlspecialchars($this->site_config->sitename);
	}

    /**
     * Выводит глобальный тулбар
     */
    public function toolbar(){
        if (!$this->isToolbar()){ return; }
        $this->menu('toolbar', false);
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

    protected function loadMenus($menu_name=false) {

        if(!$this->menu_loaded){
            $this->db_menus = cmsCore::getModel('menu')->getAllMenuItemsTree();
            $this->menu_loaded = true;
        }

        if($menu_name && isset($this->db_menus[$menu_name])){
            return modelMenu::buildMenu($this->db_menus[$menu_name]);
        }

        return false;

    }

    public function hasMenu($menu_name){
        return !empty($this->menus[$menu_name]);
    }

    /**
     * Выводит меню
     * @param string $menu_name Название меню
     * @param bool $detect_active_id Определять активные пункты меню
     * @param string $css_class CSS класс контейнера пунктов меню
     * @param int $max_items Максимальное количество видимых пунктов
     * @param bool $is_allow_multiple_active Определять все активные пункты меню
     * @param string $template Название файла шаблона меню в assets/ui/
     * @param string $menu_title Название(подпись) меню
     */
    public function menu($menu_name, $detect_active_id=true, $css_class='menu', $max_items=0, $is_allow_multiple_active=false, $template='menu', $menu_title=''){

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

        // для определения активного пункта меню
        $current_url = trim(cmsCore::getInstance()->uri_before_remap, '/');
        $href_lang = cmsCore::getLanguageHrefPrefix();

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

                $url = isset($item['url_mask']) ? $item['url_mask'] : $item['url'];
                $url = mb_substr($url, mb_strlen($this->site_config->root));
                if($href_lang){
                    $url = mb_substr($url, mb_strlen($href_lang));
                }
                $url = trim($url, '/');

                if (!$url) { continue; }

                //полное совпадение ссылки и адреса?
                if ($current_url == $url){
                    $active_ids[] = $id;
                    $is_strict = true; // не используется нигде
                } else {

                    //частичное совпадение ссылки и адреса (по началу строки)?
                    $url_first_part = mb_substr($current_url, 0, mb_strlen($url));
                    if ($url_first_part == $url){
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
     * @param array $options Опции глубиномера
     */
    public function breadcrumbs($options=array()){

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
     * @param string $html
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
        if (func_num_args() > 1){ $pagetitle = implode(' · ', func_get_args()); }
        if (is_array($pagetitle)){ $pagetitle = implode(' ', $pagetitle); }
        $this->title = $pagetitle;
        if($this->site_config->is_sitename_in_title){
            $this->title .= ' — '.$this->site_config->sitename;
        }
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
        if (!isset($this->menus['toolbar'])){ return false; }
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
    public function addBreadcrumb($title, $href=''){

        if (!$href) { $href = $_SERVER['REQUEST_URI']; }

        $this->breadcrumbs[] = array('title'=>$title, 'href'=>$href);

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
	public function addHead($tag, $is_include_once=true){
        if($is_include_once){
        	$hash = md5($tag);
        } else {
            $hash = count($this->head);
        }
		$this->head[$hash] = $tag;
        return $this;
	}

    /**
     * Возвращает тег <link rel="stylesheet"> для указанного файла
     * @param string $file Путь к файлу без учета корневой директории (начального слеша)
     * @return string
     */
    public function getCSSTag($file){
        $file = (strpos($file, '://') !== false) ? $file : $this->site_config->root . $file;
        return '<link rel="stylesheet" type="text/css" href="'.$file.'">';
    }

    /**
     * Возвращает тег <script> для указанного файла
     * @param string $file Путь к файлу без учета корневой директории (начального слеша)
     * @param string $comment Комментарий к скрипту
     * @return string
     */
    public function getJSTag($file, $comment=''){
        $file = (strpos($file, '://') !== false) ? $file : $this->site_config->root . $file;
        $comment = $comment ? "<!-- {$comment} !-->" : '';
        return '<script type="text/javascript" src="'.$file.'">'.$comment.'</script>';
    }

	/**
	 * Добавляет CSS файл в головной раздел страницы выше остальных CSS-тегов
	 * @param string $file
	 */
    public function addMainCSS($file){
        $hash = md5($file);
        if (isset($this->head_main_css[$hash]) || isset($this->head_css[$hash])) { return false; }
		$this->head_main_css[$hash] = $file;
        return true;
    }

	/**
	 * Добавляет CSS файл в головной раздел страницы
	 * @param string $file
	 */
	public function addCSS($file, $allow_merge = true){
        $hash = md5($file);
        if (isset($this->head_css[$hash]) || isset($this->head_main_css[$hash])) { return false; }
		$this->head_css[$hash] = $file;
        if (!$allow_merge){
            $this->head_css_no_merge[$hash] = $file;
        }
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

    public function addControllerJS($path, $cname = '', $comment = '', $allow_merge = true){

        if(!$cname){ $cname = $this->controller->name; }

        $js_file = $this->getTplFilePath("controllers/{$cname}/js/{$path}.js", false);

        if($js_file){
            return $this->addJS($js_file, $comment, $allow_merge);
        }

        return false;

    }
    public function addControllerCSS($path, $cname = '', $allow_merge = true){

        if(!$cname){ $cname = $this->controller->name; }

        $css_file = $this->getTplFilePath("controllers/{$cname}/css/{$path}.css", false);

        if($css_file){
            return $this->addCSS($css_file, $allow_merge);
        }

        return false;

    }

	public function insertJS($file, $comment=''){

        $hash = md5($file);
        if (isset($this->insert_js[$hash])) { return false; }
		$this->insert_js[$hash] = $file;

        $file = (strpos($file, '://') !== false) ? $file : $this->site_config->root . $file;
        $comment = $comment ? "<!-- {$comment} !-->" : '';
        // атрибут rel="forceLoad" добавлен для nyroModal
        echo '<script type="text/javascript" rel="forceLoad" src="'.$file.'">'.$comment.'</script>';

        return true;

	}

    public function insertCSS($file){

        $hash = md5($file);
        if (isset($this->insert_css[$hash])) { return false; }
		$this->insert_css[$hash] = $file;

        $file = (strpos($file, '://') !== false) ? $file : $this->site_config->root . $file;
		echo '<link rel="stylesheet" type="text/css" href="'.$file.'">';

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
        } else {
            return $this->addJS($file, $comment, false);
        }
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
        } else {
            return $this->addCSS($file, false);
        }
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

                $is_root = mb_substr($abs_url, 0, 1) == '/';
                $is_http = mb_substr($abs_url, 0, 4) == 'http';
                $is_data = mb_substr($abs_url, 0, 10) == 'data:image';

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
    public function getSchemeHTML($name=''){

        $name = $name ? $name : $this->name;

        $scheme_file = $this->site_config->root_path . 'templates/'.$name.'/scheme.html';

        if (!file_exists($scheme_file)) { return false; }

        ob_start();

        include($scheme_file);

        return ob_get_clean();

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

        $this->path = $this->site_config->root_path.'templates/'.$this->name;

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

        $exists = false;

        foreach ($this->inherit_names as $name) {
            $file = 'templates/'.$name.'/'.$relative_path;
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
        return $this->getTplFilePath('js/'.$filename.'.js', false);
    }

    /**
     * Возвращает путь к CSS-файлу, определяя его наличие в собственном шаблоне
     * @param string $filename
     * @return string
     */
    public function getTemplateStylesFileName($filename){
        return $this->getTplFilePath('css/'.$filename.'.css', false);
    }

//============================================================================//
//============================================================================//

    public function renderText($text){

        echo $this->addOutput($text);

    }

    /**
     * Выводит json строку
     * @param array $data Массив для вывода
     * @param bool $with_header Вывод вместе с хидером Content-type
     */
    public function renderJSON($data, $with_header=false){

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

    public function renderInternal($controller, $tpl_file, $data=array()){

        $this->setContext($controller);

        $result = $this->render($tpl_file, $data, new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $this->restoreContext();

        return $result;

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function render($tpl_file, $data=array(), $request=false){

        $css_file = $this->getStylesFileName();

        if ($css_file){ $this->addCSSFromContext($css_file, $request); }

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

        $request = $this->controller->request;

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

        $form_tpl_file = 'form';

        if(!empty($attributes['form_tpl_file'])){
            $form_tpl_file = $attributes['form_tpl_file'];
        }

        $tpl_file = $this->getTemplateFileName('assets/ui/'.$form_tpl_file);

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
                $editable_index = 1;
                $editable_count = count(array_filter($grid['columns'], function($element) { return isset($element['editable']); }));

                // вычисляем содержимое для каждой колонки таблицы
                foreach($grid['columns'] as $field => $column){

                    if (!is_array($row[$field])){
                        $value = htmlspecialchars($row[$field]);
                    } else {
                        $value = $row[$field];
                    }

                    if ($value === null) { $value = ''; }

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
                        if (isset($column['href_handler'])){
                            $is_active = $column['href_handler']($row);
                        } else {
                            $is_active = true;
                        }
                        if($is_active){
                            $column['href'] = string_replace_keys_values($column['href'], $row);
                            $value = '<a href="'.$column['href'].'">'.$value.'</a>';
                        }
                    }

                    if(!empty($column['editable']['table'])){
                        if(!empty($row['id'])){
                            $save_action = href_to('admin', 'inline_save', array(urlencode($column['editable']['table']), $row['id']));
                        }
                        if(!empty($column['editable']['save_action'])){
                            $save_action = string_replace_keys_values($column['editable']['save_action'], $row);
                        }
                        $attributes = array();
                        if(!empty($column['editable']['attributes'])){
                            foreach ($column['editable']['attributes'] as $akey => $avalue) {
                                if(is_string($avalue)){
                                    $attributes[$akey] = string_replace_keys_values($avalue, $row);
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

                            // все действия с подтверждением снабжаем csrf_token
                            if ($confirm_attr && !empty($action['href'])){
                                $action['href'] .= (strpos($action['href'], '?') !== false ? '&' : '?').'csrf_token='.cmsForm::getCSRFToken();
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

        $columns = array();
        if($grid['options']['load_columns']){
            foreach($grid['columns'] as $name=>$column){
                if ($name==='id' && !$grid['options']['show_id']){continue;}
                $columns[] = array(
                    'sortable'  => $grid['options']['is_sortable'],
                    'width'     => isset($column['width']) ? $column['width'] : '',
                    'title'     => $column['title'],
                    'name'      => $name,
                    'filter'    => (isset($column['filter']) && $column['filter'] != 'none' && $column['filter'] != false) ?
                    html_input('text', 'filter_'.$name, (isset($grid['filter'][$name]) ? $grid['filter'][$name] : ''), array('id'=>'filter_'.$name, 'rel'=>$name)) : ''
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

        $this->addJS( $this->getJavascriptFileName('datagrid') );

        $tpl_file = $this->getTemplateFileName('assets/ui/grid-perms');

        include($tpl_file);

    }

    /**
     * Выводит меню
     * @param array $menu Массив пунктов меню
     * @param array $active_ids Массив активных пунктов меню
     * @param string $css_class CSS класс контейнера пунктов меню
     * @param int $max_items Максимальное количество видимых пунктов
     * @param string $template Название файла шаблона меню в assets/ui/
     * @param string $menu_title Название(подпись) меню
     */
    public function renderMenu($menu, $active_ids=array(), $css_class='menu', $max_items=0, $template = 'menu', $menu_title=''){

        $tpl_file = $this->getTemplateFileName('assets/ui/'.$template);

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
    /**
     * Возвращает все названия шаблонов для списка записей типов контента
     * @return array
     */
    public function getAvailableContentListStyles(){

        $styles = $files = array();

        $inherit_names = array('default');
        if(file_exists($this->site_config->root_path.'templates/'.$this->site_config->template . '/inherit.php')){
            $names = include $this->site_config->root_path.'templates/'.$this->site_config->template . '/inherit.php';
            if($names){
                foreach ($names as $name) {
                    $inherit_names[] = $name;
                }
            }
        }
        if($this->site_config->template !== 'default'){
            $inherit_names[] = $this->site_config->template;
        }
        $inherit_names = array_reverse($inherit_names);

        foreach ($inherit_names as $name) {
            $_files = cmsCore::getFilesList('templates/'.$name.'/content', 'default_list*.tpl.php', true);
            $files = array_merge($files, $_files);
        }

        $files = array_unique($files);
        if (!$files) { return $styles; }

        foreach($files as $file){

            preg_match('/^default_list_([a-z0-9_\-]*)\.tpl$/i', $file, $matches);

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
     * Рендерит шаблон списка записей контента
     */
    public function renderContentList($ctype, $data=array(), $request=false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype['name'].'_list', true);

        if (!$tpl_file){

            $style = !empty($ctype['options']['list_style']) ? '_'.$ctype['options']['list_style'] : '';

            $tpl_file = $this->getTemplateFileName("content/default_list{$style}");

        }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

    /**
     * Рендерит шаблон просмотра записи контента
     */
    public function renderContentItem($ctype_name, $data=array(), $request=false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype_name.'_item', true);

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

        $config = $this->site_config;

        $layout = $this->getLayout();

        $template_file = $this->getTplFilePath($layout.'.tpl.php');

        $device_type = cmsRequest::getDeviceType();

        if($template_file){

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

        if (!$html){ return $this; }

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
