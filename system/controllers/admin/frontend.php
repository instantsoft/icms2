<?php
class admin extends cmsFrontend {

    const addons_api_key   = '8e13cb202f8bdc27dc765e0448e50d11';
    const addons_api_point = 'https://addons.instantcms.ru/api/method/';

    public $disallow_mapping_redirect = true;

    protected $useOptions = true;

    const perpage = 30;

    public $installer_upload_path = 'installer';

    public $install_folder_exists = false;

	public function routeAction($action_name) {

        if($this->request->isStandard()){

            $result = cmsEventsManager::hook('admin_confirm_login', array(
                'allow'     => true,
                'form'      => null,
                'errors'    => null,
                'pagetitle' => null,
                'title'     => null,
                'hint'      => null
            ));

            if (!$result['allow']){

                unset($result['allow']);

                $this->current_params = $result;

                return 'confirm_login';

            }

        }

		return $action_name;

	}

    protected function validateParamsCount($class, $method_name, $params) {
        // проверка на кол-во параметров в контроллере admin отключена
        return true;
    }

    public function before($action_name) {

        parent::before($action_name);

        if(!$this->request->isInternal()){

            cmsModel::globalLocalizedOff();

            if (!$this->cms_user->is_logged) { cmsCore::errorForbidden('', true); }

            if (!$this->cms_user->is_admin) { cmsCore::error404(); }

            if(!$this->isAllowByIp()){ cmsCore::errorForbidden(LANG_ADMIN_ACCESS_DENIED_BY_IP); }

            $this->install_folder_exists = file_exists($this->cms_config->root_path . 'install/');

            if($this->request->isStandard()){

                $this->cms_template->setLayout('admin');

                $this->cms_template->setMenuItems('cp_main', $this->getAdminMenu($this->cms_template->name === 'admincoreui'));

                $this->cms_template->setLayoutParams(array(
                    'user' => $this->cms_user,
                    'current_lang' => cmsCore::getLanguageName(),
                    'langs' => cmsCore::getLanguages(),
                    'hide_sidebar' => cmsUser::getCookie('hide_sidebar', 'integer'),
                    'close_sidebar' => cmsUser::getCookie('close_sidebar', 'integer'),
                    'su'   => $this->getSystemUtilization(),
                    'update' => ($this->cms_config->is_check_updates ? $this->cms_updater->checkUpdate(true) : array()),
                    'notices_count' => cmsCore::getModel('messages')->getNoticesCount($this->cms_user->id)
                ));

            }

        }

    }

    private function isAllowByIp() {

        $allow_ips = cmsConfig::get('allow_ips');
        if(!$allow_ips){ return true; }

        return string_in_mask_list(cmsUser::getIp(), $allow_ips);

    }

    function getSystemUtilization() {

        $total_size = disk_total_space(PATH);
        $free_space = disk_free_space(PATH);
        $taken_space = ($total_size -$free_space);
        $percent = round($taken_space/$total_size*100);

        $su = [
            'disk' => [
                'title'   => LANG_CP_SU_DISK,
                'hint'    => files_format_bytes($taken_space).'/'.files_format_bytes($total_size),
                'percent' => $percent,
                'style'   => ($percent <= 50 ? 'info' : ($percent <= 75 ? 'warning' : 'danger'))
            ]
        ];

        if(function_exists('sys_getloadavg')){

            $cpu_count = cmsUser::sessionGet('cpu_count');

            if(!$cpu_count){

                // Ну а вдруг ;-)
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $cmd = 'echo %NUMBER_OF_PROCESSORS%';
                } else {
                    $cmd = "grep -P '^physical id' /proc/cpuinfo|wc -l";
                }

                try {
                    $cpu_count = console_exec_command($cmd);

                    if(!empty($cpu_count[0])){
                        $cpu_count = trim($cpu_count[0]) ? trim($cpu_count[0]) : 1;
                    } else {
                        $cpu_count = 1;
                    }

                } catch (Exception $exc) {
                    $cpu_count = 'error';
                }

                cmsUser::sessionSet('cpu_count', $cpu_count);
            }

            if($cpu_count && is_numeric($cpu_count)){

                $la = sys_getloadavg();

                if(isset($la[2])){

                    $current_load_average = round(100*($la[2]/$cpu_count));

                    // вероятно определили неверно
                    if($current_load_average > 110){
                        $cpu_count = round($current_load_average/100);
                        cmsUser::sessionSet('cpu_count', $cpu_count);
                        $current_load_average = round(100*($la[2]/$cpu_count));
                    }

                    $su['cpu'] = [
                        'title'   => LANG_CP_SU_CPU,
                        'hint'    => $current_load_average.'%',
                        'percent' => $current_load_average,
                        'style'   => ($current_load_average <= 50 ? 'info' : ($current_load_average <= 75 ? 'warning' : 'danger'))
                    ];
                }

            }

        }

        return cmsEventsManager::hook('admin_system_utilization', $su);
    }

    public function buildDatasetFieldsList($controller_name, $fields) {

        $fields_list = array();

        foreach($fields as $field){

            if((!$field['handler']->allow_index || $field['handler']->filter_type === false) && $field['type'] != 'parent'){ continue; }

            $fields_list[] = array(
                'value' => $field['name'],
                'type'  => $field['handler']->filter_type,
                'title' => $field['title']
            );

        }

        return cmsEventsManager::hook('admin_'.$controller_name.'_dataset_fields_list', $fields_list);

    }

    public function getAdminMenu($show_submenu = false){

        $menu = []; $ctype_new_count = 0;

        $ctypes = $this->model_backend_content->getContentTypes();

        if($show_submenu){
            foreach ($ctypes as $ctype) {
                $ctype_new_count += $this->model->getTableItemsCount24($this->model->getContentTypeTableName($ctype['name']));
            }
        }

        $menu[] = [
            'title' => LANG_CP_SECTION_CONTENT,
            'url' => href_to($this->name, 'content'),
            'counter' => ($ctypes && $show_submenu && $ctype_new_count) ? '+'.$ctype_new_count : null,
            'options' => array(
                'class' => 'item-content',
                'icon'  => 'nav-icon icon-docs'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_SECTION_CTYPES,
            'url' => href_to($this->name, 'ctypes'),
            'options' => array(
                'class' => 'item-ctypes',
                'icon'  => 'nav-icon icon-equalizer'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_SECTION_MENU,
            'url' => href_to($this->name, 'menu'),
            'options' => array(
                'class' => 'item-menu',
                'icon'  => 'nav-icon icon-menu'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_SECTION_WIDGETS,
            'url' => href_to($this->name, 'widgets'),
            'options' => array(
                'class' => 'item-widgets',
                'icon'  => 'nav-icon icon-grid'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_SECTION_CONTROLLERS,
            'url' => href_to($this->name, 'controllers'),
            'options' => array(
                'class' => 'item-controllers',
                'icon'  => 'nav-icon icon-layers'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_OFICIAL_ADDONS,
            'url' => href_to($this->name, 'addons_list'),
            'options' => array(
                'class' => 'item-addons',
                'icon'  => 'nav-icon icon-puzzle'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_SECTION_USERS,
            'url' => href_to($this->name, 'users'),
            'options' => array(
                'class' => 'item-users',
                'icon'  => 'nav-icon icon-people'
            )
        ];

        $menu[] = [
            'title' => LANG_CP_SECTION_SETTINGS,
            'url' => href_to($this->name, 'settings'),
            'options' => array(
                'class' => 'item-settings',
                'icon'  => 'nav-icon icon-settings'
            )
        ];

        return cmsEventsManager::hook('adminpanel_menu', $menu);
    }

//============================================================================//
//============================================================================//

    public function getCtypeMenu($do='add', $id=null){

        $ctype_menu = array(

            array(
                'title' => LANG_CP_CTYPE_SETTINGS,
                'url' => href_to($this->name, 'ctypes', ($do == 'add' ? array('add') : array('edit', $id)))
            ),
            array(
                'title' => LANG_CP_CTYPE_LABELS,
                'url' => href_to($this->name, 'ctypes', array('labels', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_CP_CTYPE_FIELDS,
                'url' => href_to($this->name, 'ctypes', array('fields', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_CP_CTYPE_PROPS,
                'url' => href_to($this->name, 'ctypes', array('props', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_CP_CTYPE_PERMISSIONS,
                'url' => href_to($this->name, 'ctypes', array('perms', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_CP_CTYPE_DATASETS,
                'url' => href_to($this->name, 'ctypes', array('datasets', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_CP_CTYPE_FILTERS,
                'url' => href_to($this->name, 'ctypes', array('filters', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_MODERATORS,
                'url' => href_to($this->name, 'ctypes', array('moderators', $id)),
                'disabled' => ($do == 'add')
            ),
            array(
                'title' => LANG_CP_CTYPE_RELATIONS,
                'url' => href_to($this->name, 'ctypes', array('relations', $id)),
                'disabled' => ($do == 'add')
            ),
        );

        list($ctype_menu, $do, $id) = cmsEventsManager::hook('admin_ctype_menu', array($ctype_menu, $do, $id));

        if($do != 'add'){

            $ctype = $this->model_backend_content->getContentType($id);

            if($ctype){

                // проверяем, есть ли нативный контроллер и есть ли у него опции
                if(cmsCore::isControllerExists($ctype['name'])){
                    if(cmsCore::getController($ctype['name'])->options){
                        $ctype_menu[] = array(
                            'title' => LANG_CP_CONTROLLERS_OPTIONS,
                            'url'   => href_to($this->name, 'controllers', array('edit', $ctype['name'], 'options')),
                            'options' => array(
                                'icon'  => 'nav-icon icon-settings'
                            )
                        );
                    }
                }

                list($ctype_menu, $ctype) = cmsEventsManager::hook('admin_'.$ctype['name'].'_ctype_menu', array($ctype_menu, $ctype));

            }

        }

        return $ctype_menu;

    }


    public function addCtypeWidgetsPages($ctype){

        $this->model_backend_widgets->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.all",
            'title_const' => 'LANG_WP_CONTENT_ALL_PAGES',
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            )
        ));

        $this->model_backend_widgets->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.list",
            'title_const' => 'LANG_WP_CONTENT_LIST',
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            ),
            'url_mask_not' => array(
                "{$ctype['name']}/*/view-*",
                "{$ctype['name']}/*.html",
                "{$ctype['name']}/add",
                "{$ctype['name']}/add?*",
                "{$ctype['name']}/add/%",
                "{$ctype['name']}/addcat",
                "{$ctype['name']}/addcat/%",
                "{$ctype['name']}/editcat/%",
                "{$ctype['name']}/edit/*",
            )
        ));

        $this->model_backend_widgets->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.item",
            'title_const' => 'LANG_WP_CONTENT_ITEM',
            'url_mask' => "{$ctype['name']}/*.html"
        ));

        $this->model_backend_widgets->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.edit",
            'title_const' => 'LANG_WP_CONTENT_ITEM_EDIT',
            'url_mask' => array(
                "{$ctype['name']}/add",
                "{$ctype['name']}/add/%",
                "{$ctype['name']}/edit/*"
            )
        ));

        return true;
    }

//============================================================================//
//============================================================================//

    public function getSettingsMenu(){

        $template = new cmsTemplate($this->cms_config->template);

        $menu = [];

        $menu[] = [
            'title'   => LANG_BASIC_OPTIONS,
            'url'     => href_to($this->name, 'settings'),
            'level'   => 2,
            'options' => [
                'icon' => 'nav-icon icon-globe'
            ]
        ];
        $menu[] = [
            'title'   => LANG_CP_SCHEDULER,
            'url'     => href_to($this->name, 'settings', ['scheduler']),
            'level'   => 2,
            'options' => [
                'icon' => 'nav-icon icon-clock'
            ]
        ];
        if($template->hasOptions()){
            $menu[] = [
                'title'   => LANG_CP_SETTINGS_TEMPLATE_OPTIONS,
                'url'     => href_to($this->name, 'settings', ['theme', $this->cms_config->template]),
                'level'   => 2,
                'options' => [
                    'icon' => 'nav-icon icon-settings'
                ]
            ];
        }
        $menu[] = [
            'title'   => LANG_CP_CHECK_NESTED,
            'url'     => href_to($this->name, 'settings', ['check_nested']),
            'level'   => 2,
            'options' => [
                'icon' => 'nav-icon icon-organization'
            ]
        ];

        return cmsEventsManager::hook('admin_settings_menu', $menu);
    }

    public function getUserGroupsMenu($action = 'view', $id = 0) {

        return cmsEventsManager::hook('admin_user_groups_menu', array(
            array(
                'title' => LANG_CONFIG,
                'url' => $action != 'add' ? href_to($this->name, 'users', array('group_edit', $id)) : href_to($this->name, 'users', 'group_add'),
                'options' => array(
                    'icon'  => 'nav-icon icon-globe'
                )
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'disabled' => $action == 'add' ? true : null,
                'url' => href_to($this->name, 'users', array('group_perms', $id)),
                'options' => array(
                    'icon'  => 'nav-icon icon-key'
                )
            )
        ));

    }

    public function getAddonsMenu() {

        return cmsEventsManager::hook('admin_addons_menu', array(
            array(
                'title' => LANG_CP_OFICIAL_ADDONS,
                'url' => href_to($this->name, 'addons_list'),
                'options' => array(
                    'icon'  => 'icon-puzzle'
                )
            ),
            array(
                'title' => LANG_CP_INSTALL_PACKAGE,
                'url'   => href_to($this->name, 'install'),
                'options' => array(
                    'icon'  => 'icon-cloud-upload'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_CONTROLLERS,
                'url'   => href_to($this->name, 'controllers'),
                'options' => array(
                    'icon'  => 'icon-layers'
                )
            ),
            array(
                'title' => LANG_EVENTS_MANAGEMENT,
                'url'   => href_to($this->name, 'controllers', 'events'),
                'options' => array(
                    'icon'  => 'icon-bag'
                )
            )
        ));

    }

//============================================================================//
//============================================================================//

    public function loadControllerBackend($controller_name, $request){

        $ctrl_file = $this->cms_config->root_path . 'system/controllers/'.$controller_name.'/backend.php';

        if(!file_exists($ctrl_file)){
            cmsCore::error(sprintf(LANG_CP_ERR_BACKEND_NOT_FOUND, $controller_name));
        }

        include_once($ctrl_file);

        $controller_class = 'backend'.ucfirst($controller_name);

        $backend = new $controller_class($request);

        $backend->controller_admin = $this;

        return $backend;

    }

//============================================================================//
//============================================================================//

    public function parsePackageManifest(){

        $path = $this->cms_config->upload_path . $this->installer_upload_path;

        $ini_file = $path . '/' . "manifest.{$this->cms_config->language}.ini";
        $ini_file_default = $path . '/manifest.ru.ini';

        if (!file_exists($ini_file)){ $ini_file = $ini_file_default; }
        if (!file_exists($ini_file)){ return false; }

        $manifest = parse_ini_file($ini_file, true);

        if (file_exists($this->cms_config->upload_path . $this->installer_upload_path . '/' . 'package')){
            $manifest['contents'] = $this->getPackageContentsList();
            if($manifest['contents']){
                if(!empty($manifest['contents']['system']['core'])){
                    foreach ($manifest['contents']['system']['core'] as $file) {
                        if(file_exists($this->cms_config->root_path . 'system/core/'.$file)){
                            $manifest['notice_system_files'] = LANG_INSTALL_NOTICE_SYSTEM_FILE;
                            break;
                        }
                    }
                }
                if(!empty($manifest['contents']['system']['config'])){
                    foreach ($manifest['contents']['system']['config'] as $file) {
                        if(file_exists($this->cms_config->root_path . 'system/config/'.$file)){
                            $manifest['notice_system_files'] = LANG_INSTALL_NOTICE_SYSTEM_FILE;
                            break;
                        }
                    }
                }
            }
        } else {
			$manifest['contents'] = false;
		}

        if (isset($manifest['info']['image'])){
            $manifest['info']['image'] = $this->cms_config->upload_host . '/' .
                                            $this->installer_upload_path . '/' .
                                            $manifest['info']['image'];
        }

        if (isset($manifest['info']['image_hint'])){
            $manifest['info']['image_hint'] = $this->cms_config->upload_path .
                                            $this->installer_upload_path . '/' .
                                            $manifest['info']['image_hint'];
        }

        if((isset($manifest['install']) || isset($manifest['update']))){

            $action = (isset($manifest['install']) ? 'install' : 'update');

            if(isset($manifest[$action]['type']) && isset($manifest[$action]['name'])){

                $manifest['package'] = array(
                    'type'       => $manifest[$action]['type'],
                    'type_hint'  => constant('LANG_CP_PACKAGE_TYPE_'.strtoupper($manifest[$action]['type']).'_'.strtoupper($action)),
                    'action'     => $action,
                    'name'       => $manifest[$action]['name'],
                    'controller' => (isset($manifest[$action]['controller']) ? $manifest[$action]['controller'] : null),
                );

                // проверяем установленную версию
                if(method_exists($this, $manifest[$action]['type'].'Installed')){
                    $manifest['package']['installed_version'] = call_user_func(array($this, $manifest[$action]['type'].'Installed'), $manifest['package']);
                }
            }


        }

        // проверяем наличие контроллеров и манифестов
        if(!empty($manifest['package_controllers']['controller'])){
            $manifest['package_controllers'] = $manifest['package_controllers']['controller'];
        } else {
            $manifest['package_controllers'] = false;
        }

        $dir = $path.'/package/system/controllers';

        if (!$manifest['package_controllers'] && is_dir($dir)) {

            $dir_context = opendir($dir);
            $controllers = array();

            while ($next = readdir($dir_context)){
                if (in_array($next, array('.', '..'))){ continue; }
                if (strpos($next, '.') === 0){ continue; }
                if (!is_dir($dir.'/'.$next)) { continue; }
                $controllers[] = $next;
            }

            if($controllers){

                asort($controllers);

                $manifest['package_controllers'] = $controllers;

            }

        }

        return $manifest;

    }

    public function componentInstalled($manifest_package) {

        $model = new cmsModel();

        return $model->filterEqual('name', $manifest_package['name'])->getFieldFiltered('controllers', 'version');

    }

    public function widgetInstalled($manifest_package) {

        $model = new cmsModel();

        return $model->filterEqual('name', $manifest_package['name'])->
                filterEqual('controller', $manifest_package['controller'])->
                getFieldFiltered('widgets', 'version');

    }

    private function getPackageContentsList(){

        $path = $this->cms_config->upload_path . $this->installer_upload_path . '/' . 'package';

        if (!is_dir($path)) { return false; }

        return files_tree_to_array($path);

    }

    /**
     * Возвращает массив хуков контроллеров
     * Читаются файлы директории hooks контроллера
     *
     * @return array
     */
    public function getControllersFilesEvents() {

        $events = [];

        $controllers = cmsCore::getDirsList('system/controllers', true);

        $index = 0;

        foreach ($controllers as $controller_name) {

            $hooks = cmsCore::getFilesList('system/controllers/' . $controller_name . '/hooks', '*.php', true, true);
            if (!$hooks) { continue; }

            $controller_object = cmsCore::getController($controller_name);

            foreach ($hooks as $event_name) {

                $hook_class_name = 'on' . string_to_camel('_', $controller_name) . string_to_camel('_', $event_name);

                $hook_object = new $hook_class_name($controller_object);

                // Некоторые хуки не требуют регистрации в базе данных,
                // Например, хуки для CRON или иные, которые вызываются напрямую
                // Свойство $disallow_event_db_register в классе хука регулирует это поведение
                if(empty($hook_object->disallow_event_db_register)){

                    $events[$controller_name][$index] = $event_name;

                    $index++;
                }
            }
        }

        return $events;
    }

    public function getEventsDifferences($event_controller = false) {

        $result = [
            'added'   => [],
            'deleted' => []
        ];

        $manifests_events = $this->getControllersFilesEvents();
        $database_events  = cmsCore::getControllersEvents(false);

        if ($event_controller) {
            if (isset($manifests_events[$event_controller])) {
                $manifests_events = [
                    $event_controller => $manifests_events[$event_controller]
                ];
            } else {
                $manifests_events = [];
            }
            if (isset($database_events[$event_controller])) {
                $database_events = [
                    $event_controller => $database_events[$event_controller]
                ];
            } else {
                $database_events = [];
            }
        }

        // добавленные: есть в $manifests_events, нет в $database_events
        if ($manifests_events) {
            foreach ($manifests_events as $controller => $events) {
                foreach ($events as $event) {
                    if (empty($database_events[$controller])) {
                        $result['added'][$controller][] = $event;
                    }
                    if (!empty($database_events[$controller]) && !in_array($event, $database_events[$controller])) {
                        $result['added'][$controller][] = $event;
                    }
                }
            }
        }

        // удалённые: есть в $database_events, нет в $manifests_events
        if ($database_events) {
            foreach ($database_events as $controller => $events) {
                foreach ($events as $event) {
                    if (empty($manifests_events[$controller])) {
                        $result['deleted'][$controller][] = $event;
                    }
                    if (!empty($manifests_events[$controller]) && !in_array($event, $manifests_events[$controller])) {
                        $result['deleted'][$controller][] = $event;
                    }
                }
            }
        }

        return $result;
    }

    public function getWidgetOptionsForm($widget_name, $controller_name = false, $options = false, $template_name = false, $allow_set_cacheable = true){

        if(!$template_name){
            $template_name = $this->cms_config->template;
        }

        $template = new cmsTemplate($template_name);

		$widget_path = cmsCore::getWidgetPath($widget_name, $controller_name);

        $path = $this->cms_config->system_path . $widget_path;

        $form_file = $path . '/options.form.php';

        $form_name = 'widget' . ($controller_name ? "_{$controller_name}_" : '_') . "{$widget_name}_options";

        $form = cmsForm::getForm($form_file, $form_name, array($options, $template_name));
        if (!$form) { $form = new cmsForm(); }

        $form->is_tabbed = true;

		//
		// Опции внешнего вида
		//
		$design_fieldset_id = $form->addFieldset(LANG_DESIGN, 'design');

            $form->addField($design_fieldset_id, new fieldList('tpl_wrap', array(
                'title' => LANG_WIDGET_WRAPPER_TPL,
				'hint'  => LANG_WIDGET_WRAPPER_TPL_HINT,
				'default' => 'wrapper',
                'generator' => function($item) use ($template_name){
                    return ['' => LANG_WIDGET_WRAPPER_TPL_NO, '-1' => LANG_WIDGET_WRAPPER_TPL_CUST] + $this->cms_template->getAvailableTemplatesFiles('widgets', 'wrapper*.tpl.php', $template_name);
                }
            )));

            // Стили обёрток
            $preset_file = $template->getTplFilePath('widgets/wrapper_styles.php');
            if($preset_file){

                cmsCore::loadTemplateLanguage($template_name);

                $preset_styles = include $preset_file;

                $form->addField($design_fieldset_id, new fieldList('tpl_wrap_style', array(
                    'title' => LANG_CP_WIDGETS_STYLE,
                    'generator' => function($item) use ($preset_styles){
                        $items = ['' => ''];
                        foreach ($preset_styles as $key => $value) {
                            $items['opt'.$key] = [$key];
                            foreach ($value as $k => $v) {
                                $items[$k] = $v;
                            }
                        }
                        return $items;
                    },
                    'visible_depend' => ['tpl_wrap' => ['show' => array_keys($preset_styles)]]
                )));
            }

            $wrap_custom_fields = [
                'title' => LANG_TITLE,
                'id'    => 'ID',
                'body'  => LANG_WIDGET_BODY
            ];

            $form->addField($design_fieldset_id, new fieldHtml('tpl_wrap_custom', [
                'title' => LANG_WIDGET_WRAPPER_TPL_CUST,
                'patterns_hint' => [
                    'patterns' =>  $wrap_custom_fields,
                    'text_panel' => '',
                    'always_show' => true,
                    'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                ],
                'options' => ['editor' => 'ace'],
                'visible_depend' => ['tpl_wrap' => ['show' => ['-1']]]
            ]));

            $form->addField($design_fieldset_id, new fieldList('tpl_body', array(
                'title' => LANG_WIDGET_BODY_TPL,
				'hint' => sprintf(LANG_WIDGET_BODY_TPL_HINT, $widget_path),
                'default' => $widget_name,
                'generator' => function($item) use ($template_name, $widget_path){
                    return $this->cms_template->getAvailableTemplatesFiles($widget_path, '*.tpl.php', $template_name);
               }
            )));

            $form->addField($design_fieldset_id, new fieldString('class_wrap', array(
                'title' => LANG_CSS_CLASS_WRAP,
                'visible_depend' => ['tpl_wrap' => ['hide' => ['-1']]]
            )));

            $form->addField($design_fieldset_id, new fieldString('class_title', array(
                'title' => LANG_CSS_CLASS_TITLE,
                'visible_depend' => ['tpl_wrap' => ['hide' => ['', 'wrapper_plain', '-1']]]
            )));

            $form->addField($design_fieldset_id, new fieldString('class', array(
                'title' => LANG_CSS_CLASS_BODY,
                'visible_depend' => ['tpl_wrap' => ['hide' => ['', 'wrapper_plain', '-1']]]
            )));

        //
        // Опции доступа
        //
        $access_fieldset_id = $form->addFieldset(LANG_PERMISSIONS, 'permissions');

            // Показывать группам
            $form->addField($access_fieldset_id, new fieldListGroups('groups_view', array(
                'title'       => LANG_SHOW_TO_GROUPS,
                'show_all'    => true,
                'show_guests' => true
            )));

            // Не показывать группам
            $form->addField($access_fieldset_id, new fieldListGroups('groups_hide', array(
                'title'       => LANG_HIDE_FOR_GROUPS,
                'show_all'    => false,
                'show_guests' => true
            )));

            $form->addField($access_fieldset_id, new fieldListMultiple('languages', array(
                'title'   => LANG_WIDGET_LANG_SELECT,
                'default' => 0,
                'show_all'=> true,
                'generator'   => function ($item){
                    $langs = cmsCore::getLanguages();
                    $items = array();
                    foreach ($langs as $lang) {
                        $items[$lang] = $lang;
                    }
                    return $items;
                }
            )));

            $form->addField($access_fieldset_id, new fieldListMultiple('device_types', array(
                'title'   => LANG_WIDGET_DEVICE,
                'default' => 0,
                'show_all'=> true,
                'items'   => array(
                    'tablet'  => LANG_TABLET_DEVICES,
                    'mobile'  => LANG_MOBILE_DEVICES,
                    'desktop' => LANG_DESKTOP_DEVICES
                )
            )));

            $form->addField($access_fieldset_id, new fieldListMultiple('template_layouts', array(
                'title'   => LANG_WIDGET_TEMPLATE_LAYOUT,
                'default' => 0,
                'show_all'=> true,
                'generator' => function($item) use ($template_name){
                    $layouts = $this->cms_template->getAvailableTemplatesFiles('', '*.tpl.php', $template_name);
                    $items = [];
                    if ($layouts) {
                        foreach ($layouts as $layout) {
                            if($layout == 'admin'){ continue; }
                            $items[$layout] = string_lang('LANG_'.$template_name.'_THEME_LAYOUT_'.$layout, $layout);
                        }
                    }
                    return $items;
               }
            )));

        //
        // Опции заголовка
        //
        $title_fieldset_id = $form->addFieldsetToBeginning(LANG_BASIC_OPTIONS, 'basic_options');

            // ID виджета
            $form->addField($title_fieldset_id, new fieldNumber('id', array(
                'is_hidden'=>true
            )));
            $form->addField($title_fieldset_id, new fieldString('template', array(
                'is_hidden'=>true,
                'default' => $template_name
            )));

            // Заголовок виджета
            $form->addField($title_fieldset_id, new fieldString('title', array(
                'title' => LANG_TITLE,
                'rules' => array(
                    array('required'),
                    array('min_length', 3),
                    array('max_length', 128)
                )
            )));

            // Флаг показа заголовка
            $form->addField($title_fieldset_id, new fieldCheckbox('is_title', array(
                'title'   => LANG_SHOW_TITLE,
                'default' => true
            )));

            // Флаг объединения с предыдущим виджетом
            $form->addField($title_fieldset_id, new fieldCheckbox('is_tab_prev', array(
                'title'   => LANG_WIDGET_TAB_PREV,
                'default' => false
            )));

            // Управление кэшированием
            if($this->cms_config->cache_enabled && $allow_set_cacheable){
                $form->addField($title_fieldset_id, new fieldCheckbox('is_cacheable', array(
                    'title' => LANG_CP_CACHE
                )));
            }

            // Ссылки в заголовке
            $form->addField($title_fieldset_id, new fieldText('links', array(
                'title' => LANG_WIDGET_TITLE_LINKS,
                'hint'  => LANG_WIDGET_TITLE_LINKS_HINT,
                'is_strip_tags' => true
            )));

		return cmsEventsManager::hook('widget_options_full_form', $form);

    }

    public function getAddonsMethod($name, $params = array(), $cacheable = false) {

        if (!function_exists('curl_init')){
            return false;
        }

        $cache_file = cmsConfig::get('cache_path').md5($name.serialize($params)).'_addons.dat';

        if($cacheable && is_readable($cache_file)){

            $timedif = (time() - filemtime($cache_file));

            if ($timedif < 10800) { // три часа кэша

                $result = include $cache_file;

                if ($result) {
                    return $result;
                } else {
                    unlink($cache_file);
                }

            } else {
                unlink($cache_file);
            }

        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, self::addons_api_point.$name.'?api_key='.self::addons_api_key.'&'.http_build_query($params, '', '&'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_HTTPGET, true);

        $_data = curl_exec($curl);
        if(!$_data){ return false; }

        $data = json_decode($_data, true);

        curl_close($curl);

        if($data === false){
            return json_last_error_msg();
        }

        if($cacheable){
            file_put_contents($cache_file, '<?php return '.var_export($data, true).';');
        }

        return $data;

    }

    public function getContentGridColumnsSettings($ctype_id){

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) { return false; }

        $fields = $this->model_backend_content->getContentFields($ctype['name']);
        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $items = array(
            'system' => array(
                'title' => array('title' => LANG_TITLE,'filters' => array('exact'=>LANG_CP_GRID_COLYMNS_EXACT,'like'=>LANG_CP_GRID_COLYMNS_LIKE)),
                'date_pub' => array('title' => LANG_DATE,'filters' => array('date'=>LANG_CP_GRID_COLYMNS_DATE)),
                'is_approved' => array('title' => LANG_MODERATION),
                'is_pub' => array('title' => LANG_ON,'filters' => array('select'=>LANG_CP_GRID_COLYMNS_SELECT),'filter_select' => array('items'=>array(''=>LANG_SELECT,'1'=>LANG_ON,'0'=>LANG_OFF))),
                'user_nickname' => array('title' => LANG_AUTHOR,'filters' => array('exact'=>LANG_CP_GRID_COLYMNS_EXACT,'like'=>LANG_CP_GRID_COLYMNS_LIKE))
            ),
            'user' => array()
        );
		if($ctype['is_rating']){
			$items['system']['rating'] = array('title' => LANG_RATING,'filters' => array('exact'=>LANG_CP_GRID_COLYMNS_EXACT,'like'=>LANG_CP_GRID_COLYMNS_LIKE));
		}
		if($ctype['is_comments']){
			$items['system']['comments'] = array('title' => LANG_COMMENTS,'filters' => array('exact'=>LANG_CP_GRID_COLYMNS_EXACT,'like'=>LANG_CP_GRID_COLYMNS_LIKE));
		}
		if(!empty($ctype['options']['hits_on'])){
			$items['system']['hits_count'] = array('title' => LANG_HITS,'filters' => array('exact'=>LANG_CP_GRID_COLYMNS_EXACT,'like'=>LANG_CP_GRID_COLYMNS_LIKE));
		}
        foreach($fields as $key => $field){
            if(($field['is_fixed'] && isset($items['system'][$key])) || $key === 'user'){continue;}

            $type = $field['is_fixed'] ? 'system' : 'user';
            $items[$type][$key] = array('title' => $field['title'],'filters' => array(),'handlers' => array());
            if(in_array($field['type'], array('number','string','url','user'))){
                $items[$type][$key]['filters']['exact'] = LANG_CP_GRID_COLYMNS_EXACT;
            }
            if(in_array($field['type'], array('html','number','string','text','url','user'))){
                $items[$type][$key]['filters']['like'] = LANG_CP_GRID_COLYMNS_LIKE;
            }
            if(in_array($field['type'], array('html','text'))){
                $items[$type][$key]['handlers_only'] = LANG_CP_GRID_COLYMNS_CUT_TEXT;
                $items[$type][$key]['handler_only'] = function($value, $item){return mb_substr(strip_tags($value), 0, 100);};
            }
            if(strpos($key, 'date_') === 0){
                $items[$type][$key]['filters']['date'] = LANG_CP_GRID_COLYMNS_DATE;
            }
            if($field['type'] === 'checkbox'){
                $items[$type][$key]['filters']['select'] = LANG_CP_GRID_COLYMNS_SELECT;
                $items[$type][$key]['filter_select'] = array('items'=>array(''=>LANG_SELECT,'1'=>LANG_ON,'0'=>LANG_OFF));
                $items[$type][$key]['handlers']['flag'] = LANG_CP_GRID_COLYMNS_FLAG;
            }else
            if($field['type'] === 'string'){
                $items[$type][$key]['handlers']['to_filter'] = LANG_CP_GRID_COLYMNS_TO_FILTER;
                $items[$type][$key]['handler_to_filter'] = function($value, $item) use($key){return '<a class="ajaxlink" href="#" onclick="return icms.datagrid.fieldValToFilter(this, \''.$key.'\');">'.$value.'</a>';};
            }else
            if($field['type'] === 'images'){
                $items[$type][$key]['handlers_only'] = LANG_CP_GRID_COLYMNS_IMAGES_NMB;
                $items[$type][$key]['handler_only'] = function($value, $item){return $value ? count(!is_array($value) ? cmsModel::yamlToArray($value) : $value) : 0;};
            }else
            if($field['type'] === 'image'){
                $items[$type][$key]['handlers_only'] = LANG_CP_GRID_COLYMNS_PREVIEW;
                $presets = array($field['handler']->getOption('size_teaser'), $field['handler']->getOption('size_full'));
                $items[$type][$key]['handler_only'] = function($value, $item) use($presets){
                    if(!$value){return '';}
                    $value = !is_array($value) ? cmsModel::yamlToArray($value) : $value;
                    return html_image($value, $presets, '', array('class' => 'grid_image_preview img-thumbnail'));
                };
            }else
            if(in_array($field['type'], ['list', 'listbitmask', 'listmultiple'])){
                $items[$type][$key]['handlers_only'] = '&mdash;';
                $items[$type][$key]['handler_only'] = function($value, $item)use($field,$ctype){
                    if(!$value){return '';}
                    $item['ctype'] = $ctype;
                    $item['ctype_name'] = $ctype['name'];
                    $field['handler']->setItem($item);
                    return $field['handler']->parseTeaser($value);
                };
            }else
            if($field['handler']->is_denormalization){
                $items[$type][$key]['handlers_only'] = '&mdash;';
                $items[$type][$key]['handler_only'] = function($value, $item)use($field){
                    if(!$value){return '';}
                    return $item[$field['handler']->getDenormalName()];
                };
            }
        }
        return $items;
    }

    public function getContentGridColumnsSettingsDefault(){
        return array(
            'system' => array(
                'title' => array('enabled' => true,'filter' => 'like'),
                'date_pub' => array('enabled' => true),
                'is_approved' => array('enabled' => true),
                'is_pub' => array('enabled' => true),
                'user_nickname' => array('enabled' => true)
            ),
            'user' => array()
        );
    }

    public function getSchemeColForm($do, $row, $col = []){

        $form = $this->getForm('widgets_cols', [$do, (!empty($col['id']) ? $col['id'] : 0), $row]);

        $col_scheme_options = cmsEventsManager::hookAll('admin_col_scheme_options', ['add', $row, []]);

        if($col_scheme_options){
            foreach ($col_scheme_options as $controller_name => $fields) {
                foreach ($fields as $field) {
                    $form->addField('basic', $field);
                }
            }
        }

        return $form;

    }

    public function getSchemeRowForm($do, $row, $col = []){

        $form = $this->getForm('widgets_rows', [$do]);

        $row_scheme_options = cmsEventsManager::hookAll('admin_row_scheme_options', [$do, $row, $col]);

        if($row_scheme_options){
            foreach ($row_scheme_options as $controller_name => $fields) {
                foreach ($fields as $field) {
                    $form->addField('basic', $field);
                }
            }
        }

        return $form;

    }

}
