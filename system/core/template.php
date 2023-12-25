<?php

class cmsTemplate {

    private static $instance;

    /**
     * Путь корневой папки шаблонов (может быть пустым)
     */
    const TEMPLATE_BASE_PATH = 'templates/';

    /**
     * Название шаблона
     * @var string
     */
    public $name;
    /**
     * Полный путь к директории шаблона
     * @var string
     */
    public $path;
    /**
     * Порядок наследования шаблона
     * @var array
     */
    protected $inherit_names = ['default'];
    /**
     * Параметры шаблона
     * Задаются в файле manifest.php в директории шаблона
     * Пример файла templates/modern/manifest.php
     * @var array
     */
    protected $manifest = null;
    /**
     * Название файла шаблона скелета страницы
     * @var string
     */
    protected $layout = 'main';
    /**
     * Параметры, передаваемые в шаблон скелета
     * @var array
     */
    protected $layout_params = [];
    /**
     * Вывод результата работы контроллера
     * @var string
     */
    protected $output;
    /**
     * Флаг, что тело страницы уже было выведено
     * @var boolean
     */
    protected $output_is_displayed = false;
    /**
     * Массив кастомных блоков страницы
     * @var array
     */
    protected $blocks = [];
    /**
     * Опции шаблона
     * @var array|null
     */
    protected $options = null;
    /**
     * Объект конфигурации сайта cmsConfig
     * @var object
     */
    protected $site_config;
    /**
     * Массив путей к js/css для загрузки со страницы по требованию
     * @var array
     */
    protected $on_demand = ['root' => '', 'css' => [], 'js' => []];
    /**
     * Массив головных (<head>) тегов страницы
     * @var array
     */
    protected $head = [];
    /**
     * Массив тегов, выводящихся в самом низу страницы, перед </body>
     * @var array
     */
    protected $bottom = [];
    /**
     * Массив CSS файлов, подключаемых выше всех остальных
     * @var array
     */
    protected $head_main_css = [];
    /**
     * Массив подключаемых CSS файлов
     * @var array
     */
    protected $head_css = [];
    /**
     * Массив JS файлов к подключению на странице выше остальных JS-тегов
     * @var array
     */
    protected $head_main_js = [];
    /**
     * Массив подключаемых JS файлов
     * @var array
     */
    protected $head_js = [];
    /**
     * Массив JS файлов, которые при подключении сразу печатаются на странице
     * @var array
     */
    protected $insert_js = [];
    /**
     * Массив CSS файлов, которые при подключении сразу печатаются на странице
     * @var array
     */
    protected $insert_css = [];
    /**
     * Массив подключаемых JS файлов, которые не участвуют в объединении
     * @var array
     */
    protected $head_js_no_merge = [];
    /**
     * Массив подключаемых CSS файлов, которые не участвуют в объединении
     * @var array
     */
    protected $head_css_no_merge = [];
    /**
     * Массив предзагрузки контента
     * https://developer.mozilla.org/ru/docs/Web/HTML/Preloading_content
     * @var array
     */
    protected $head_preload = [];
    /**
     * Тег <h1> страницы
     * @var string
     */
    public $page_h1;
    /**
     * Массив данных, которые используются в SEO паттернах
     * @var array
     */
    public $page_h1_item;
    /**
     * Тег <title> страницы
     * @var string
     */
    public $title;
    /**
     * Массив данных, которые используются в SEO паттернах
     * @var array
     */
    public $title_item;
    /**
     * Тег <meta name="description"> страницы
     * @var string
     */
    public $metadesc;
    /**
     * Массив данных, которые используются в SEO паттернах
     * @var array
     */
    public $metadesc_item;
    /**
     * Тег <meta name="keywords"> страницы
     * @var string
     */
    public $metakeys;
    /**
     * Массив данных, которые используются в SEO паттернах
     * @var array
     */
    public $metakeys_item;
    /**
     * Хлебные крошки
     * @var array
     */
    public $breadcrumbs = [];
    /**
     * Пункты меню, разнесённые по меню
     * @var array
     */
    public $menus = [];
    /**
     * Массив имён файлов шаблонов, не найденных на диске
     * @var array
     */
    protected $not_found_tpls = [];
    /**
     * Флаг, что виджеты отрендерены
     * @var boolean
     */
    public $widgets_rendered = false;
    /**
     * Массив виджетов страницы
     * @var array
     */
    protected $widgets = [];
    /**
     * Индекс последнего объединённого виджета
     * @var integer
     */
    protected $widgets_group_index = 0;
    /**
     * Объект контроллера контекста
     * @var cmsController
     */
    protected $controller;
    /**
     * Массив ссылок объектов контроллеров
     * при смене контекста
     * @var array
     */
    protected $controllers_queue = [];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
            // подключаем хелпер основного шаблона
            if (!cmsCore::includeFile(self::TEMPLATE_BASE_PATH . self::$instance->getName() . '/assets/helper.php')) {
                cmsCore::loadLib('template.helper');
            }
        }
        return self::$instance;
    }

    public function __construct($name = '') {

        $this->site_config = cmsConfig::getInstance();

        if (!$name) {

            $device_type = cmsRequest::getDeviceType();

            $name = $this->site_config->template;

            // шаблон в зависимости от девайса
            if ($device_type !== 'desktop') {
                $device_template = cmsConfig::get('template_' . $device_type);
                if ($device_template) {
                    $name = $device_template;
                }
            }
        }

        $this->setBaseTemplate($name);

        $this->title = $this->site_config->sitename;

        $is_no_def_meta = isset($this->site_config->is_no_meta) ? $this->site_config->is_no_meta : false;

        if (!$is_no_def_meta) {
            $this->metakeys = $this->site_config->metakeys;
            $this->metadesc = $this->site_config->metadesc;
        }
    }

    /**
     * Проверяет, есть ли что-то для тела страницы
     * @return boolean
     */
    public function isBody() {
        return !empty($this->output);
    }

    /**
     * Возвращает, выведено ли уже тело страницы
     * @return boolean
     */
    public function isBodyDisplayed() {
        return $this->output_is_displayed;
    }

    /**
     * Выводит тело страницы
     */
    public function body() {
        $this->output_is_displayed = true;
        $this->printOutput();
    }

    /**
     * Принудительно печатает тело страницы
     */
    public function printOutput() {
        echo $this->output;
    }

    /**
     * Добавляет переданный код к выводу тела страницы
     * @param string $html
     */
    public function addOutput($html){
        $this->output .= $html;
    }

    /**
     * Заменяет вывод тела страницы переданным кодом
     * @param string $html
     */
    public function setOutput($html){
        $this->output = $html;
    }

    /**
     * Добавляет HTML на позицию блока
     *
     * @param string $position  Название позиции блок
     * @param string $html      HTML блока
     * @param boolean $begining Добавить в начало блока
     */
    public function addToBlock($position, $html, $begining = false){
        if(isset($this->blocks[$position])){
            if($begining){
                $this->blocks[$position] = $html.$this->blocks[$position];
            } else {
                $this->blocks[$position] .= $html;
            }
        } else {
            $this->blocks[$position] = $html;
        }
    }

    /**
     * Выводит HTML блока
     * @param string $position
     */
    public function block($position) {
        echo !empty($this->blocks[$position]) ? $this->blocks[$position] : '';
    }

    /**
     * Проверяет, есть ли блок на заданной позиции (позициях)
     * @param string $position Название позиции
     * @return boolean
     */
    public function hasBlock($position) {

        if (func_num_args() > 1) {
            $positions = func_get_args();
        } else {
            $positions = [$position];
        }

        $has = false;

        foreach ($positions as $pos) {
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
    public function head($is_seo_meta = true, $print_js = true, $print_css = true) {

        cmsEventsManager::hook('before_print_head', $this);

        if ($is_seo_meta) {
            if (!empty($this->metakeys) && empty($this->site_config->disable_metakeys)) {
                echo '<meta name="keywords" content="' . html((!empty($this->metakeys_item) ? string_replace_keys_values_extended($this->metakeys, $this->metakeys_item) : $this->metakeys), false) . '">' . "\n\t\t";
            }
            if (!empty($this->metadesc)) {

                $num_postfix = '';
                if (!empty($this->site_config->page_num_in_title)) {
                    $page = cmsCore::getInstance()->request->get('page', 0);
                    if ($page > 1) {
                        $num_postfix = ' ' . LANG_PAGE . ' №' . $page;
                    }
                }

                echo '<meta name="description" content="' . html((!empty($this->metadesc_item) ? string_replace_keys_values_extended($this->metadesc, $this->metadesc_item) : $this->metadesc), false) . $num_postfix . '">' . "\n\t\t";
            }
        }

        foreach ($this->head as $id => $tag) {
            echo $tag . "\n\t\t";
        }

        if ($print_css) {
            $this->printCssTags();
        }

        if ($print_js) {
            $this->printJavascriptTags();
        }

        if (!empty($this->site_config->set_head_preload) && $this->head_preload) {
            header('X-DNS-Prefetch-Control: on');
            header('Link: ' . implode(', ', $this->head_preload));
        }

        return $this;
    }

    /**
     * Выводит теги внизу страницы, перед закрывающим тегом </body>
     */
    public function bottom() {
        foreach ($this->bottom as $id => $tag) {
            echo "\t" . $tag . "\n";
        }
    }

    /**
     * Выводит javascript теги
     * @return $this
     */
    public function printJavascriptTags() {

        $js = [];

        if (!$this->site_config->merge_js) {

            $js = array_merge(array_values($this->head_main_js), array_values($this->head_js));
        } else {

            $js[] = $this->getMergedJSPath();

            $js = array_merge($js, array_values($this->head_js_no_merge));
        }

        foreach ($js as $file) {

            $file = $this->getHeadFilePath($file);

            $this->head_preload[] = '<' . $file . '>; rel=preload; as=script';

            echo $this->getJSTag($file) . "\n\t\t";
        }

        return $this;
    }

    /**
     * Выводит CSS теги
     * @return $this
     */
    public function printCssTags() {

        $css = [];

        if (!$this->site_config->merge_css) {

            $css = array_merge(array_values($this->head_main_css), array_values($this->head_css));
        } else {

            $css[] = $this->getMergedCSSPath();

            $css = array_merge($css, array_values($this->head_css_no_merge));
        }

        foreach ($css as $file) {

            $file = $this->getHeadFilePath($file);

            $this->head_preload[] = '<' . $file . '>; rel=preload; as=style';

            echo $this->getCSSTag($file) . "\n\t\t";
        }

        return $this;
    }

    /**
     * Выводит заголовок текущей страницы
     */
    public function title() {

        $t = !empty($this->title_item) ? string_replace_keys_values_extended($this->title, $this->title_item) : $this->title;

        if (!empty($this->site_config->page_num_in_title)) {

            $page = cmsCore::getInstance()->request->get('page', 0);

            if ($page > 1) {
                $t .= ' — ' . LANG_PAGE . ' №' . $page;
            }
        }

        html($t);
    }

    /**
     * Выводит название сайта
     */
    public function sitename() {
        html($this->site_config->sitename);
    }

    /**
     * Выводит глобальный тулбар
     * @param string $template_name Название шаблона в assets/ui
     * @return
     */
    public function toolbar($template_name = 'menu') {
        if (!$this->isToolbar()) {
            return;
        }
        $this->menu('toolbar', false, 'nav nav-pills', 0, false, $template_name);
    }

    /**
     * Выводит меню действий контроллера
     * @param string $menu_title Название меню
     * @return
     */
    public function actionsToolbar($menu_title) {
        if (empty($this->menus['controller_actions_menu'])) {
            return;
        }
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

        if (!$this->hasWidgetsOn($position)) {
            return false;
        }

        $device_type = cmsRequest::getDeviceType();

        foreach ($this->widgets[$position] as $group) {

            if (count($group) == 1) {

                $widget = $group[0];
                if ($wrapper) {
                    $widget['wrapper'] = $wrapper;
                }

                if (empty($widget['wrapper'])) {
                    if ($widget['class_wrap']) {
                        echo '<div class="' . $widget['class_wrap'] . '">' . $widget['body'] . '</div>';
                    } else {
                        echo $widget['body'];
                    }
                } else if ($widget['wrapper'] == '-1') {
                    echo string_replace_keys_values(string_replace_svg_icons($widget['tpl_wrap_custom']), $widget);
                } else {
                    include($this->getTemplateFileName('widgets/' . $widget['wrapper']));
                }
            } else {

                $widgets = $group;

                include($this->getTemplateFileName('widgets/wrapper_tabbed'));
            }
        }
    }

    /**
     * Выводит виджеты на указанной позиции
     * И выводит их, заменяя {position} в HTML обёртки
     *
     * @param string $position Название позиции
     * @param string $wrapper_html HTML шаблона обёртки позиции
     */
    public function widgetsInHtml($position, $wrapper_html) {

        ob_start();

        $this->widgets($position);

        echo str_replace('{position}', ob_get_clean(), $wrapper_html);
    }

    /**
     * Проверяет наличие виджетов на позиции/позициях
     *
     * @param string|array $positions Название позиции/позиций
     * Можно передавать сколь угодно дополнительных параметров как название позиции
     * @return boolean
     */
    public function hasWidgetsOn($positions) {

        if (!$this->widgets_rendered) {
            cmsCore::getInstance()->runWidgets();
        }

        if (func_num_args() > 1) {
            $positions = func_get_args();
        } elseif (!is_array($positions)) {
            $positions = array($positions);
        }

        $has = false;

        foreach ($positions as $pos) {
            $has = $has || !empty($this->widgets[$pos]);
        }

        return $has;
    }

    /**
     * Проверяет наличие меню
     *
     * @param string $menu_name Название меню
     * @return boolean
     */
    public function hasMenu($menu_name) {
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
    public function menu(
        $menu_name,
        $detect_active_id = true,
        $css_class = 'menu nav',
        $max_items = 0,
        $is_allow_multiple_active = false,
        $template = 'menu',
        $menu_title = ''
    ) {

        if (!$this->hasMenu($menu_name)) {
            return;
        }

        $menu = $this->menus[$menu_name];
        $active_ids = [];

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

        foreach ($menu as $id => $item) {

            // Строим атрибуты ссылок
            $menu[$id]['attributes'] = [];

            $onclick = isset($item['options']['onclick']) ? $item['options']['onclick'] : false;
            $onclick = isset($item['options']['confirm']) ? "return confirm('{$item['options']['confirm']}')" : $onclick;
            if($onclick){
                $menu[$id]['attributes']['onclick'] = $onclick;
            }

            if(!empty($item['options']['target'])){
                $menu[$id]['attributes']['target'] = $item['options']['target'];
            }

            if (!empty($item['data'])) {
                foreach ($item['data'] as $key => $val) {
                    $menu[$id]['attributes']['data-' . $key] = html($val, false);
                }
            }

            $menu[$id]['disabled']     = !empty($item['disabled']);
            $menu[$id]['level']        = !isset($item['level']) ? 1 : $item['level'];
            $menu[$id]['childs_count'] = !isset($item['childs_count']) ? 0 : $item['childs_count'];

            if (!isset($item['url']) &&  !empty($item['controller'])) {
                if (!isset($item['action'])) { $item['action'] = ''; }
                if (!isset($item['params'])) { $item['params'] = []; }
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
                if ($current_url === $url){
                    $active_ids[] = $id;
                } else {

                    //частичное совпадение ссылки и адреса (по началу строки)?
                    $url_first_parts = [mb_substr($current_ourl, 0, $url_len), mb_substr($current_url, 0, $url_len)];
                    if (in_array($url, $url_first_parts)){
                        $active_ids[] = $id;
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
            $more_items    = array_slice($menu, $first_level_limit, count($menu) - $first_level_limit, true);

            $item_more_id = 10000;

            $item_more = [
                $item_more_id => [
                    'id'           => $item_more_id,
                    'title'        => LANG_MENU_MORE,
                    'childs_count' => ($first_level_count - $max_items),
                    'level'        => 1,
                    'disabled'     => false,
                    'attributes'   => [],
                    'options'      => [
                        'class' => 'more'
                    ]
                ]
            ];

            foreach($more_items as $id=>$item){
                if ($item['level']==1){
                    $more_items[$id]['parent_id'] = $item_more_id;
                }
                $more_items[$id]['level']++;
            }

            $menu = $visible_items + $item_more + $more_items;

        }

        if (!$is_allow_multiple_active && (count($active_ids)>1)){
            $active_ids = [$active_ids[count($active_ids)-1]];
        }

        if($css_class){
            $css_class .= ' menu-'.$menu_name;
        } else {
            $css_class = 'nav menu menu-'.$menu_name;
        }

        $this->renderMenu($menu, $active_ids, $css_class, $max_items, $template, $menu_title);
    }

    /**
     * Выводит глубиномер
     *
     * @param array $options Опции глубиномера
     */
    public function breadcrumbs($options = []) {

        $default_options = [
            'home_url'   => href_to_home(),
            'template'   => 'breadcrumbs',
            'strip_last' => true
        ];

        $options = array_merge($default_options, $options);

        if ($this->breadcrumbs) {
            if ($options['strip_last']) {
                unset($this->breadcrumbs[count($this->breadcrumbs) - 1]);
            } else {
                $this->breadcrumbs[count($this->breadcrumbs) - 1]['is_last'] = true;
            }
        }

        $this->renderAsset('ui/' . $options['template'], [
            'breadcrumbs' => $this->breadcrumbs,
            'options'     => $options
        ]);
    }

    /**
     * Формирует ссылку в контексте текущего контроллера
     *
     * @param string $action       Экшен
     * @param string|array $params Параметры экшена
     * @param array $query         Параметры строки запроса
     * @return string
     */
    public function href_to($action, $params = false, $query = []) {

        if (isset($this->controller)) {
            if (!isset($this->controller->root_url)) {
                return href_to($this->controller->name, $action, $params, $query);
            } else {
                return href_to($this->controller->root_url, $action, $params, $query);
            }
        } else {
            return href_to($this->site_config->root, $action, $params, $query);
        }

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Проверяет наличие тега h1
     * @return boolean
     */
    public function hasPageH1() {
        return !empty($this->page_h1);
    }

    /**
     * Печатает значение тега h1 страницы
     */
    public function pageH1() {
        echo $this->getPageH1();
    }

    /**
     * Возвращает значение тега h1 страницы
     */
    public function getPageH1() {
        return !empty($this->page_h1_item) ? string_replace_keys_values_extended($this->page_h1, $this->page_h1_item) : $this->page_h1;
    }

    /**
     * Устанавливает значение тега h1 страницы
     *
     * @param string $title
     * @return $this
     */
    public function setPageH1($title) {

        if (is_array($title)) {
            $title = implode(', ', $title);
        }

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
    public function addToPageH1($title, $separator = ', ') {
        if (is_array($title)) {
            $title = implode($separator, $title);
        }
        $this->page_h1 .= ($this->page_h1 ? $separator : '') . $separator . $title;
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
    public function setPageTitle($pagetitle) {
        if (func_num_args() > 1) {
            $pagetitle = implode(' · ', array_filter(func_get_args()));
        }
        if (is_array($pagetitle)) {
            $pagetitle = implode(' ', $pagetitle);
        }
        $this->title = $pagetitle;
        if ($this->site_config->is_sitename_in_title) {
            $this->title .= ' — ' . $this->site_config->sitename;
        }
        return $this;
    }

    public function addToPageTitle($title) {
        $this->title .= ' ' . $title;
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
    public function setMeta($keywords, $description) {
        $this->metakeys = $keywords;
        $this->metadesc = $description;
        return $this;
    }

    /**
     * Устанавливает ключевые слова страницы
     * @param string $keywords Ключевые слова
     */
    public function setPageKeywords($keywords) {
        $this->metakeys = $keywords;
        return $this;
    }

    public function setPageKeywordsItem($item) {
        $this->metakeys_item = $item;
        return $this;
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

    public function setPagePatternDescription($item, $default = 'description') {

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

        $item = [
            'title'        => $button['title'],
            'url'          => isset($button['href']) ? $button['href'] : (isset($button['url']) ? $button['url'] : ''),
            'level'        => isset($button['level']) ? $button['level'] : 1,
            'childs_count' => isset($button['childs_count']) ? $button['childs_count'] : 0,
            'counter'      => isset($button['counter']) ? $button['counter'] : null,
            'options'      => [
                'icon'    => isset($button['icon']) ? $button['icon'] : null,
                'class'   => isset($button['class']) ? $button['class'] : null,
                'target'  => isset($button['target']) ? $button['target'] : '',
                'onclick' => isset($button['onclick']) ? $button['onclick'] : null,
                'confirm' => isset($button['confirm']) ? $button['confirm'] : null,
            ],
            'data'         => isset($button['data']) ? $button['data'] : ''
        ];

        return $this->addMenuItem('toolbar', $item);
    }

    /**
     * Добавляет кнопки на глобальный тулбар
     * @param array $buttons
     * @return \cmsTemplate
     */
    public function addToolButtons($buttons) {

        if (is_array($buttons)) {
            foreach ($buttons as $button) {
                $this->addToolButton($button);
            }
        }

        return $this;
    }

    /**
     * Проверяет наличие кнопок на тулбаре
     * @return boolean
     */
    public function isToolbar() {
        if (empty($this->menus['toolbar'])) {
            return false;
        }
        return (bool) count($this->menus['toolbar']);
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет один пункт меню в меню
     * @param string $menu_name Название меню
     * @param array $item Массив данных пункта меню
     */
    public function addMenuItem($menu_name, $item) {

        if (!isset($this->menus[$menu_name])) {
            $this->menus[$menu_name] = [];
        }

        array_push($this->menus[$menu_name], $item);

        return $this;
    }

    /**
     * Добавляет массив пунктов меню в меню
     * @param string $menu_name Название меню
     * @param array $items Массив пунктов меню
     */
    public function addMenuItems($menu_name, $items) {

        if (!isset($this->menus[$menu_name])) {
            $this->menus[$menu_name] = [];
        }

        foreach ($items as $item) {
            if (!isset($item['level'])) {
                $item['level'] = 1;
            }
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
    public function setMenuItems($menu_name, $items) {

        if ($items) {
            $this->menus[$menu_name] = $items;
        }

        return $this;
    }

    public function applyMenuItemsHook($menu_name, $event_name) {

        $this->menus[$menu_name] = cmsEventsManager::hook($event_name, (isset($this->menus[$menu_name]) ? $this->menus[$menu_name] : []));

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

        if (!$href) {
            $href = $_SERVER['REQUEST_URI'];
        }

        $this->breadcrumbs[] = ['title' => $title, 'href' => $href];

        return $this;
    }

    /**
     * Проверяет наличие пунктов в глубиномере
     * @return boolean
     */
    public function isBreadcrumbs() {
        return (bool) $this->breadcrumbs;
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

    public function getTemplateFilePath($path, $with_inheritance = false) {
        if($with_inheritance){
            return $this->site_config->root . $this->getTplFilePath($path, false);
        }
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

            $hash = $file;
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

            $hash = $file;
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

            $hash = $file;
            if (isset($this->head_main_js[$hash])) {
                return false;
            }

            if (isset($this->head_js[$hash])) {
                unset($this->head_js[$hash]);
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

            $hash = $file;
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

    protected function onDemandTplName($name, $type) {

        if($type === 'js'){
            $files = $this->getJavascriptFileName($name);
        } else {
            $files = $this->getTemplateStylesFileName($name);
        }

        if(!is_array($files)){
            $files = [$files];
        }

        foreach($files as $key => $f){
            $this->on_demand[$type][$key] = $f;
        }

        return $this;
    }

    public function onDemandTplCSSName($name) {
        return $this->onDemandTplName($name, 'css');
    }

    public function onDemandTplJSName($name) {
        return $this->onDemandTplName($name, 'js');
    }

    public function onDemandPrint() {
        $this->on_demand['root'] = $this->site_config->root;
        echo '<script> icms.head.on_demand = '.json_encode($this->on_demand).';</script>'."\n";
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
            $hash = $file;
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
            $hash = $file;
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
    public function setLayout($layout) {

        $this->layout = $layout;

        return $this;
    }

    /**
     * Устанавливает параметры шаблон скелета
     * @param array $layout_params
     * @return $this
     */
    public function setLayoutParams($layout_params) {

        $this->layout_params = $layout_params;

        return $this;
    }

    /**
     * Возвращает название шаблона скелета
     * @param string $layout
     */
    public function getLayout() {
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

        $scheme_file = $this->site_config->root_path.self::TEMPLATE_BASE_PATH.$name.'/scheme.';

        if (is_readable($scheme_file.'php')) { return $scheme_file.'php'; }
        if (is_readable($scheme_file.'html')) { return $scheme_file.'html'; }

        return false;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Устанавливает основной шаблон сайта
     *
     * @param string $name
     * @return $this
     */
    public function setBaseTemplate($name) {

        $this->setName($name);

        $this->applyManifest();

        $this->options = $this->getOptions();

        return $this;
    }

    /**
     * Возвращает название глобального шаблона
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Устанавливает название глобального шаблона
     * @param string $name
     * @return \cmsTemplate
     */
    public function setName($name) {

        $this->name = $name;

        $this->path = $this->site_config->root_path . self::TEMPLATE_BASE_PATH . $this->name;

        return $this;
    }

    /**
     * Применяет манифест текущего шаблона
     * @return $this
     */
    public function applyManifest() {

        $this->loadManifest();

        // Манифест загружен?
        if($this->manifest === null){

            // Совместимость. Пробуем загрузить наследование по-старому
            $this->manifest = [
                'inherit' => $this->getInheritTemplates()
            ];
        }

        if(!isset($this->manifest['inherit'])){
            $this->manifest['inherit'] = [];
        }

        $this->setInheritNames($this->manifest['inherit']);

        return $this;
    }

    /**
     * Устанавливает цепочку наследования шаблона
     * @param array $names Массив названий шаблонов в приоритетном порядке от меньшего к большему
     * @return \cmsTemplate
     */
    public function setInheritNames($names = []) {

        if ($names) {
            foreach ($names as $name) {
                $this->inherit_names[] = $name;
            }
        }

        if ($this->name !== 'default') {
            $this->inherit_names[] = $this->name;
        }

        $this->inherit_names = array_reverse($this->inherit_names);

        return $this;
    }

    public function getInheritNames() {
        return $this->inherit_names;
    }

    /**
     * Возвращает путь к файлу шаблона
     * @param string $relative_path Путь относительно корня шаблона. Без первого слеша
     * @param boolean $return_abs_path Возвращать полный путь в файловой системе, по умолчанию true
     * @param boolean $return_current_name Возвращать путь файла и имя шаблона, в котором файл нашелся, по умолчанию false
     * @return string|array|boolean
     */
    public function getTplFilePath($relative_path, $return_abs_path = true, $return_current_name = false) {

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
                    if($return_current_name){
                        $exists = [$name, $exists];
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
    public function setContext($controller_obj) {
        if ($this->controller) {
            $this->controllers_queue[] = $this->controller;
        }
        $this->controller = $controller_obj;
        return $this;
    }

    /**
     * Возвращает объект текущего контроллера
     * @return object
     */
    public function getContext() {
        return $this->controller;
    }

    /**
     * Восстанавливает ссылку на предыдущий контроллер
     */
    public function restoreContext() {

        if (!count($this->controllers_queue)) {
            return false;
        }

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
    public function getTemplateFileName($filename, $is_check = false) {

        $tpl_file = $this->getTplFilePath($filename . '.tpl.php');

        if (!$tpl_file) {
            if (!$is_check) {
                $last_not_found_tpl = end($this->not_found_tpls);
                cmsCore::error(ERR_TEMPLATE_NOT_FOUND . ': ' . $this->site_config->root . $last_not_found_tpl);
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

        if (!$controller_name) {
            $controller_name = $this->controller->name;
        }
        if ($subfolder) {
            $subfolder .= '/';
        }

        return $this->getTplFilePath('controllers/' . $controller_name . '/' . $subfolder . 'styles.css', false);
    }

    /**
     * Возвращает путь к JavaScript-файлу, определяя его наличие в собственном шаблоне
     * @param string $filename
     * @return string
     */
    public function getJavascriptFileName($filename) {

        if (!is_array($filename)) {
            return $this->getTplFilePath('js/' . $filename . '.js', false);
        }

        $filenames = [];
        foreach ($filename as $key => $value) {
            $filenames[$value] = $this->getJavascriptFileName($value);
        }

        return $filenames;
    }

    /**
     * Возвращает путь к CSS-файлу, определяя его наличие в собственном шаблоне
     * @param string|array $filename Название файла (массив файлов) без расширения
     * @return string
     */
    public function getTemplateStylesFileName($filename) {

        if (!is_array($filename)) {
            return $this->getTplFilePath('css/' . $filename . '.css', false);
        }

        $filenames = [];
        foreach ($filename as $key => $value) {
            $filenames[$value] = $this->getTemplateStylesFileName($value);
        }

        return $filenames;
    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет текст к выводу
     *
     * @param string $text
     */
    public function renderText($text) {
        echo $this->addOutput($text);
    }

    /**
     * Выводит JSON строку и завершает работу
     *
     * @param array $data Массив для вывода
     * @param boolean $with_header Вывод вместе с хидером Content-type
     */
    public function renderJSON($data, $with_header = false) {

        if (ob_get_length()) { ob_end_clean(); }

        if ($with_header) {
            header('Content-type: application/json; charset=utf-8');
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            $json = json_encode([
                'success' => false,
                'errors'  => true,
                'error'   => json_last_error_msg()
            ]);
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
    public function renderBlock($position, $tpl_file, $data = []) {

        $result = $this->render($tpl_file, $data, new cmsRequest([], cmsRequest::CTX_INTERNAL));

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
    public function renderInternal($controller, $tpl_file, $data = []) {

        $this->setContext($controller);

        $result = $this->render($tpl_file, $data, new cmsRequest([], cmsRequest::CTX_INTERNAL));

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

        if (is_array($tpl_file)) {
            $data = $tpl_file;
            $tpl_file = $this->controller->current_template_name;
        }

        $tpl_file = $this->getTemplateFileName('controllers/' . $this->controller->name . '/' . $tpl_file);

        return $this->processRender($tpl_file, $data, $request, true);
    }

    /**
     * Печатает HTML код шаблона и завершает работу
     *
     * @param string|array $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     */
    public function renderPlain($tpl_file, $data = []) {

        if (is_array($tpl_file)) {
            $data = $tpl_file;
            $tpl_file = $this->controller->current_template_name;
        }

        $tpl_file = $this->getTemplateFileName('controllers/' . $this->controller->name . '/' . $tpl_file);

        $this->processRender($tpl_file, $data, new cmsRequest($this->controller->request->getData(), cmsRequest::CTX_AJAX));
    }

    /**
     * Формирует HTML код файла шаблона,
     * учитывая контекст вызова
     *
     * @param string $tpl_file Полный путь к файлу шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @param object $request Объект запроса
     * @param boolean $add_controller_css Нужно ли подключать CSS контроллера
     * @return mixed
     */
    public function processRender($tpl_file, $data = [], $request = false, $add_controller_css = false) {

        cmsDebugging::pointStart('template');

        if (!$request) { $request = $this->controller->request; }

        $hook_name = 'process_render_' . $this->controller->name . '_' . basename(str_replace('-', '_', $tpl_file), '.tpl.php');

        list($tpl_file, $data, $request) = cmsEventsManager::hook($hook_name, [$tpl_file, $data, $request]);

        ob_start();

        $device_type = cmsRequest::getDeviceType();

        extract($data); include($tpl_file);

        // Регулировать подключение CSS контроллера можно
        // Определив в самом шаблоне переменную $disable_auto_insert_css
        if ($add_controller_css &&
                !isset($disable_auto_insert_css) &&
                empty($this->controller->template_disable_auto_insert_css)) {

            $css_file = $this->getStylesFileName();
            if ($css_file) { $this->addCSSFromContext($css_file, $request); }
        }

        $html = ob_get_clean();

        cmsDebugging::pointProcess('template', function () use($tpl_file) {
            return [
                'data' => $this->controller->name.' :: '.$this->name.' :: processRender => '.str_replace($this->site_config->root_path, '', $tpl_file)
            ];
        }, 2);

        if ($request->isAjax()) {
            echo $html;
            $this->controller->halt();
        }

        if ($request->isStandard()) {
            $this->addOutput($html);
            return $html;
        }

        if ($request->isInternal()) {
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

        cmsDebugging::pointStart('template');

        if (!$request) { $request = $this->controller->request; }

        $tpl_file = $this->getTemplateFileName('controllers/'.$controller_name.'/'.$tpl_file);

        $hook_name = 'process_render_'.$controller_name.'_'.basename(str_replace('-', '_', $tpl_file), '.tpl.php');

        list($tpl_file, $data, $request) = cmsEventsManager::hook($hook_name, [$tpl_file, $data, $request]);

        extract($data); include($tpl_file);

        cmsDebugging::pointProcess('template', function () use($tpl_file, $controller_name) {
            return [
                'data' => $controller_name.' :: '.$this->name.' :: renderControllerChild => '.str_replace($this->site_config->root_path, '', $tpl_file)
            ];
        }, 2);
    }

    /**
     * Формирует HTML код шаблона и возвращает его
     * в виде строки
     *
     * @param string $tpl_file Название файла шаблона
     * @param array $data Массив параметров, передаваемых в шаблон
     * @return string
     */
    public function getRenderedChild($tpl_file, $data = []) {

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
            'is_ajax'       => false,
            'cookie_prefix' => '',
            'only_fields'   => false,
            'submit'        => ['title' => LANG_SAVE, 'show' => true],
            'cancel'        => ['title' => LANG_CANCEL, 'href' => href_to_home(), 'show' => false],
            'action'        => '',
            'append_html'   => '',
            'prepend_html'  => '',
            'form_id'       => md5(microtime(true)),
            'form_class'    => '',
            'method'        => 'post'
        ], $attributes);

        if($attributes['method'] === 'ajax'){
            $attributes['method']  = 'post';
            $attributes['is_ajax'] = true;
        }

        $form_tpl_file = 'form';

        if(!empty($attributes['form_tpl_file'])){
            $form_tpl_file = $attributes['form_tpl_file'];
        }

        $cookie_tab_key = $attributes['cookie_prefix'] . $form->getName();

        $active_tab = cmsUser::getCookie($cookie_tab_key);

        $this->renderAsset('ui/'.$form_tpl_file, [
            'form_tpl_file' => $form_tpl_file,
            'form'          => $form,
            'data'          => $data,
            'attributes'    => $attributes,
            'errors'        => $errors,
            'cookie_tab_key'=> $cookie_tab_key,
            'active_tab'    => $active_tab
        ]);
    }

    /**
     * Печатает шаблон Grid таблицы
     * И загружает данные по ajax
     *
     * @param string|false $source_url URL ajax запроса списка данных
     * @param cmsGrid $grid Данные Grid таблицы
     */
    public function renderGrid($source_url, cmsGrid $grid) {

        $grid->source_url = $source_url;

        $this->renderAsset('ui/grid-data', [
            'grid' => $grid,
            'rows' => $grid->makeGridRows() // без данных
        ]);
    }

    /**
     * Печатает JSON сформированные данные грида
     *
     * @param cmsGrid $grid Объект грида
     * @param array $dataset Данные из таблицы БД
     * @param integer $total Сколько всего записей
     * @param integer $pages_count @deprecated
     * @return void
     */
    public function renderGridRowsJSON(cmsGrid $grid, $dataset, $total = 0, $pages_count = 1) {

        $this->renderJSON($grid->makeGridRows(($dataset ?: []), $total));
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
    public function renderMenu($menu, $active_ids = [], $css_class = 'menu', $max_items = 0, $template = 'menu', $menu_title = '') {

        $this->renderAsset('ui/'.$template, [
            'menu'       => $menu,
            'menu_id'    => preg_replace('/[0-9]+/', '', md5($css_class.microtime(true))),
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
    public function renderAsset($tpl_file, $data = [], $request = false) {

        cmsDebugging::pointStart('template');

        $tpl_file = $this->getTemplateFileName('assets/' . $tpl_file);

        $file_name = basename($tpl_file, '.tpl.php');

        $hook_name = str_replace('-', '_', 'render_asset_' . basename(str_replace($file_name . '.tpl.php', '', $tpl_file)) . '_' . $file_name);

        list($tpl_file, $data, $request) = cmsEventsManager::hook($hook_name, [$tpl_file, $data, $request]);

        $device_type = cmsRequest::getDeviceType();

        extract($data); include($tpl_file);

        cmsDebugging::pointProcess('template', function () use($tpl_file) {
            return [
                'data' => $this->name . ' :: renderAsset => ' . str_replace($this->site_config->root_path, '', $tpl_file)
            ];
        }, 3);

        if ($request) {
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
     * @param object $request Объект запроса
     * @param boolean $min_html Сжимать HTML
     * @return string
     */
    public function getRenderedAsset($tpl_file, $data = [], $request = false, $min_html = false) {

        ob_start();

        $this->renderAsset($tpl_file, $data, $request);

        $html = ob_get_clean();

        return ($min_html && $this->site_config->min_html) ? html_minify($html) : $html;
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
     * @param array $excluded Исключения
     * @return array
     */
    public function getAvailableTemplatesFiles($path, $pattern='*.*', $template_name = false, $excluded = []) {

        if(!$template_name){
            $template_instance = new cmsTemplate($this->site_config->template);
        } else {
            $template_instance = new cmsTemplate($template_name);
        }

        $inherit_names = array_reverse($template_instance->getInheritNames());

        $files = $__files = [];

        if(!$template_name){
            $template_name = $this->site_config->template;
        }

        foreach ($inherit_names as $name) {
            $_files = cmsCore::getFilesList(self::TEMPLATE_BASE_PATH.$name.'/'.$path, $pattern, true);
            $files = array_merge($files, $_files);
        }

        $files = array_unique($files);

        if($files){
            foreach ($files as $file) {

                $file_name = $file_title = str_replace('.tpl', '', $file);

                if(in_array($file_name, $excluded)){ continue; }

                $file_path = $template_instance->getTemplateFileName($path.'/'.$file_name, true);
                if(!$file_path){ continue; }

                // Ищем название шаблона внутри файла
                $file_header = [];
                if(preg_match( '|Template Name:(.*)$|umi', file_get_contents($file_path), $file_header) && !empty($file_header[1])){
                    $file_title = string_lang(trim(preg_replace('/\s*(?:\*\/|\?>).*/', '', $file_header[1]))).' ('.$file_name.')';
                }

                $__files[$file_name] = $file_title;
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

        foreach($files as $file => $file_title){

            preg_match('/^default_list_([a-z0-9_\-]*)$/i', $file, $matches);

            if (!$matches){
                $styles[''] = $file_title;
            } else {
                $styles[$matches[1]] = $file_title;
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

        foreach($files as $file => $file_title){

            preg_match('/^'.$ctype_name.'_item_([a-z0-9_\-]*)$/i', $file, $matches);

            if(!empty($matches[1])){
                $styles[$matches[1]] = $file_title;
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
    public function renderContentList($ctype, $data = [], $request = false) {

        $tpl_file = $this->getTemplateFileName('content/' . $ctype['name'] . '_list', true);

        $data['list_opt'] = [];

        if (!$tpl_file) {

            $style = '';

            if (!empty($ctype['options']['list_style'])) {
                if (is_array($ctype['options']['list_style'])) {
                    // Проверка на дефис - совместимость, библиотека yaml обновлена была в 2.16.1
                    $style = ($ctype['options']['list_style'][0] && $ctype['options']['list_style'][0] !== '-') ? '_' . $ctype['options']['list_style'][0] : '';
                } else {
                    $style = '_' . $ctype['options']['list_style'];
                }
            }

            $list_type = $this->controller->getListContext();

            if (isset($ctype['options']['context_list_style'][$list_type])) {
                $style = $ctype['options']['context_list_style'][$list_type] ? '_' . $ctype['options']['context_list_style'][$list_type] : '';
            }

            if(!empty($ctype['options']['list_style_options'])){
                foreach ($ctype['options']['list_style_options'] as $options) {
                    if($options['name'] === ltrim($style, '_') && !empty($options['value'])){
                        $data['list_opt'] = json_decode($options['value'], true); break;
                    }
                }
            }

            $tpl_file = $this->getTemplateFileName('content/default_list' . $style);
        }

        if (!$request) {
            $request = $this->controller->request;
        }

        return $this->processRender($tpl_file, $data, $request);
    }

    /**
     * Рендерит шаблон просмотра записи контента
     * @param string $ctype_name Имя типа контента
     * @param array $data Массив данных для шаблона
     * @param mixed $request Объект запроса
     * @return string
     */
    public function renderContentItem($ctype_name, $data = [], $request = false) {

        // опеределен ли в записи шаблон
        if (!empty($data['item']['template'])) {
            $template_name = $ctype_name . '_item_' . $data['item']['template'];
        } else {
            // или есть шаблон для типа контента
            $template_name = $ctype_name . '_item';
        }

        $tpl_file = $this->getTemplateFileName('content/' . $template_name, true);

        if (!$tpl_file) {
            $tpl_file = $this->getTemplateFileName('content/default_item');
        }

        if (!$request) {
            $request = $this->controller->request;
        }

        return $this->processRender($tpl_file, $data, $request);
    }

//============================================================================//
//============================================================================//

    /**
     * Выводит, зависимый от текущего лайоута, шаблон
     * из директории layout_childs
     *
     * @param string $child
     * @param array $data
     */
    public function renderLayoutChild($child, $data = []){

        $core = cmsCore::getInstance();

        $config = $this->site_config;

        $layout = $this->getLayout();

        $template_file = $this->getTplFilePath('layout_childs/'.$layout.'_'.$child.'.tpl.php');

        $device_type = cmsRequest::getDeviceType();

        if($template_file){

            if($this->layout_params){
                extract($this->layout_params);
            }

            extract($data);

            include($template_file);

        } else {
            cmsCore::error(ERR_TEMPLATE_NOT_FOUND. ': '. $this->name.':'.$layout.'_'.$child);
        }

    }

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

            $rows = [];
            // Есть ли поддержка динамической схемы
            if (!empty($this->manifest['properties']['is_dynamic_layout'])) {
                $rows = cmsCore::getModel('widgets')->getLayoutRows($this->name);
            }

            // CSS классы для тега body
            $body_classes = [];

            $matched_pages = $core->getMatchedPages();
            if($matched_pages && $matched_pages != [0, 1]){
                foreach ($matched_pages as $matched_page) {
                    if(!empty($matched_page['body_css'])){
                        $body_classes[] = $matched_page['body_css'];
                    }
                }
            } else {
                $body_classes[] = 'icms-frontpage';
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

    public function renderWidget($widget, $data = []) {

        cmsDebugging::pointStart('template');

        $tpl_path = cmsCore::getWidgetPath($widget->name, $widget->controller);

        $tpl_file = $this->getTemplateFileName($tpl_path . '/' . $widget->getTemplate());

        $hook_name = 'render_widget_' . ($widget->controller ? $widget->controller . '_' : '') . $widget->name . '_' . basename(str_replace('-', '_', $tpl_file), '.tpl.php');

        list($widget, $tpl_file, $data) = cmsEventsManager::hook($hook_name, [$widget, $tpl_file, $data]);

        $device_type = cmsRequest::getDeviceType();

        extract($data);

        ob_start(); include($tpl_file);

        $html = ob_get_clean();

        if ($html) {

            if (empty($widget->is_tab_prev)) {
                $this->widgets_group_index++;
            }

            if ($widget->controller && $widget->insert_controller_css) {
                $css_file = $this->getStylesFileName($widget->controller);
                if ($css_file) {
                    $this->addCSSFromContext($css_file);
                }
            }

            $this->widgets[$widget->position][$this->widgets_group_index][] = [
                'id'          => $widget->id,
                'bind_id'     => $widget->bind_id,
                'title'       => $widget->is_title ? $widget->title : false,
                'links'       => isset($widget->links) ? $widget->links : false,
                'wrapper'     => $widget->getWrapper(),
                'tpl_wrap_custom' => isset($widget->tpl_wrap_custom) ? $widget->tpl_wrap_custom : '',
                'class'       => isset($widget->css_class) ? $widget->css_class : false,
                'class_title' => isset($widget->css_class_title) ? $widget->css_class_title : false,
                'class_wrap'  => (!empty($widget->tpl_wrap_style) ? $widget->tpl_wrap_style : '') . (!empty($widget->css_class_wrap) ? ' ' . $widget->css_class_wrap : ''),
                'body'        => $html
            ];
        }

        cmsDebugging::pointProcess('template', function () use($tpl_file, $widget) {
            return [
                'data' => ($widget->controller ? $widget->controller . ' :: ' : '') . $widget->name . ' :: ' . $this->name . ' :: renderWidget => ' . str_replace($this->site_config->root_path, '', $tpl_file)
            ];
        }, 1);

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

    public function getManifest(){
        return $this->manifest;
    }

    protected function loadManifest(){
        $this->manifest = self::getTemplateManifest($this->path);
        return $this;
    }

    public static function getTemplateManifest($path) {
        if(file_exists($path . '/manifest.php')){
            return include $path . '/manifest.php';
        }
        return null;
    }

    /**
     * deprecated
     * используйте manifest.php
     * @return array
     */
    public function getInheritTemplates(){
        if(file_exists($this->path . '/inherit.php')){
            return include $this->path . '/inherit.php';
        }
        return [];
    }

    public function getIconListFilePath(){
        return $this->getTplFilePath('icon_list.php', true);
    }

    public function hasIconList(){
        return file_exists($this->getIconListFilePath());
    }

    public function getIconList(){

        $file_path = $this->getIconListFilePath();

        if(!file_exists($file_path)){
            return [];
        }

        return include $file_path;
    }

    public function hasOptions(){
        if (isset($this->manifest['properties'])) {
            return !empty($this->manifest['properties']['has_options']);
        }
        // Совместимость
        return file_exists($this->path . '/options.form.php');
    }

    public function getOptionsForm(){

        if (!$this->hasOptions()){ return false; }

        cmsCore::loadTemplateLanguage($this->inherit_names);

        list($name, $form_file) = $this->getTplFilePath('options.form.php', true, true);

        return cmsForm::getForm($form_file, $name . '_template_options');
    }

    public function setOption($key, $value){
        $this->options[$key] = $value; return $this;
    }

    public function getOption($key, $default = null){
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    public function getOptions(){

        if($this->options !== null){ return $this->options; }

        cmsCore::loadTemplateLanguage($this->inherit_names);

        return $this->loadOptions();
    }

    public function loadOptions(){

        if (!$this->hasOptions()){ return []; }

        $options_file = $this->site_config->root_path . "system/config/theme_{$this->name}.yml";

        if (!is_readable($options_file)){ return []; }

        $options_yaml = file_get_contents($options_file);

        return cmsModel::yamlToArray($options_yaml);

    }

    public function saveOptions($options) {

        $options_file = $this->site_config->root_path . "system/config/theme_{$this->name}.yml";

        if (file_exists($options_file)) {
            if (!is_writable($options_file)) {
                return false;
            }
        } else {
            if (!is_writable(dirname($options_file))) {
                return false;
            }
        }

        $options_yaml = cmsModel::arrayToYaml($options);

        $success = file_put_contents($options_file, $options_yaml);

        return $success;
    }

//============================================================================//
//============================================================================//

    public function hasProfileThemesSupport() {
        if (isset($this->manifest['properties'])) {
            return !empty($this->manifest['properties']['has_profile_themes_support']);
        }
        // Совместимость
        return file_exists($this->path . '/profiles/styler.php');
    }

    public function hasProfileThemesOptions() {
        if (isset($this->manifest['properties'])) {
            return !empty($this->manifest['properties']['has_profile_themes_options']);
        }
        // Совместимость
        return file_exists($this->path . '/profiles/options.form.php');
    }

    public function getProfileOptionsForm() {

        if (!$this->hasProfileThemesOptions()) {
            return false;
        }

        $form_file = $this->path . '/profiles/options.form.php';

        $form_name = 'template_profile_options';

        $form = cmsForm::getForm($form_file, $form_name);

        if (!$form) {
            $form = new cmsForm();
        }

        return $form;
    }

    public function applyProfileStyle($profile) {

        if (!$this->hasProfileThemesSupport()) {
            return false;
        }

        $config = $this->site_config;

        $theme = $profile['theme'];

        cmsCore::loadTemplateLanguage($this->name);

        if ($this->hasProfileThemesOptions()) {

            $form  = $this->getProfileOptionsForm();
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
