<?php
class admin extends cmsFrontend {

    const addons_api_key   = '8e13cb202f8bdc27dc765e0448e50d11';
    const addons_api_point = 'http://addons.instantcms.ru/api/method/';

    public $disallow_mapping_redirect = true;

    protected $useOptions = true;

    const perpage = 30;

    public $installer_upload_path = 'installer';

    public $install_folder_exists = false;

	public function routeAction($action_name) {

        if(!$this->request->isInternal()){

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

    public function before($action_name) {

        parent::before($action_name);

        if(!$this->request->isInternal()){

            if (!cmsUser::isLogged()) { cmsCore::errorForbidden('', true); }

            if (!cmsUser::isAdmin()) { cmsCore::error404(); }

            if(!$this->isAllowByIp()){ cmsCore::errorForbidden(LANG_ADMIN_ACCESS_DENIED_BY_IP); }

            $this->cms_template->setMenuItems('cp_main', $this->getAdminMenu());

            $this->cms_template->setLayout('admin');

            $this->install_folder_exists = file_exists($this->cms_config->root_path . 'install/');

        }


    }

    private function isAllowByIp() {

        $allow_ips = cmsConfig::get('allow_ips');
        if(!$allow_ips){ return true; }

        return string_in_mask_list(cmsUser::getIp(), $allow_ips);

    }

//============================================================================//
//============================================================================//

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

    public function getAdminMenu(){

        return cmsEventsManager::hook('adminpanel_menu', array(

            array(
                'title' => LANG_CP_SECTION_CONTENT,
                'url' => href_to($this->name, 'content'),
                'options' => array(
                    'class' => 'item-content'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_CTYPES,
                'url' => href_to($this->name, 'ctypes'),
                'options' => array(
                    'class' => 'item-ctypes'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_MENU,
                'url' => href_to($this->name, 'menu'),
                'options' => array(
                    'class' => 'item-menu'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_WIDGETS,
                'url' => href_to($this->name, 'widgets'),
                'options' => array(
                    'class' => 'item-widgets'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_CONTROLLERS,
                'url' => href_to($this->name, 'controllers'),
                'options' => array(
                    'class' => 'item-controllers'
                )
            ),
            array(
                'title' => LANG_CP_OFICIAL_ADDONS,
                'url' => href_to($this->name, 'addons_list'),
                'options' => array(
                    'class' => 'item-addons'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_USERS,
                'url' => href_to($this->name, 'users'),
                'options' => array(
                    'class' => 'item-users'
                )
            ),
            array(
                'title' => LANG_CP_SECTION_SETTINGS,
                'url' => href_to($this->name, 'settings'),
                'options' => array(
                    'class' => 'item-settings'
                )
            )

        ));

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

            $ctype = cmsCore::getModel('content')->getContentType($id);

            if($ctype){

                // проверяем, есть ли нативный контроллер и есть ли у него опции
                if(cmsCore::isControllerExists($ctype['name'])){
                    if(cmsCore::getController($ctype['name'])->options){
                        $ctype_menu[] = array(
                            'title' => LANG_CP_CONTROLLERS_OPTIONS,
                            'url'   => href_to($this->name, 'controllers', array('edit', $ctype['name'], 'options'))
                        );
                    }
                }

                list($ctype_menu, $ctype) = cmsEventsManager::hook('admin_'.$ctype['name'].'_ctype_menu', array($ctype_menu, $ctype));

            }

        }

        return $ctype_menu;

    }

//============================================================================//
//============================================================================//

    public function getSettingsMenu(){

        return cmsEventsManager::hook('admin_settings_menu', array(

            array(
                'title' => LANG_BASIC_OPTIONS,
                'url' => href_to($this->name, 'settings')
            ),
            array(
                'title' => LANG_CP_SCHEDULER,
                'url' => href_to($this->name, 'settings', array('scheduler'))
            ),

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

        // Устанавливаем корень для URL внутри бакенда
        $admin_controller_url = $this->name;
        $controller_alias = cmsCore::getControllerAliasByName($admin_controller_url);
        if ($controller_alias) { $admin_controller_url = $controller_alias; }
        $backend->setRootURL($admin_controller_url.'/controllers/edit/'.$controller_name);

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
                $manifest['package']['installed_version'] = call_user_func(array($this, $manifest[$action]['type'].'Installed'), $manifest['package']);

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

    private function systemInstalled($manifest_package) {
        return cmsCore::getVersion();
    }

    private function getPackageContentsList(){

        $path = $this->cms_config->upload_path . $this->installer_upload_path . '/' . 'package';

        if (!is_dir($path)) { return false; }

        return files_tree_to_array($path);

    }

    public function getEventsDifferences($event_controller = false) {

        $result = array(
            'added'   => array(),
            'deleted' => array()
        );

        $manifests_events = cmsCore::getManifestsEvents();
        $database_events  = cmsCore::getControllersManifests(false, false);

        if($event_controller){
            if(isset($manifests_events[$event_controller])){
                $manifests_events = array(
                    $event_controller => $manifests_events[$event_controller]
                );
            } else {
                $manifests_events = array();
            }
            if(isset($database_events[$event_controller])){
                $database_events = array(
                    $event_controller => $database_events[$event_controller]
                );
            } else {
                $database_events = array();
            }
        }

        // добавленные: есть в $manifests_events, нет в $database_events
        if($manifests_events){
            foreach ($manifests_events as $controller => $events){
                foreach ($events as $event){
                    if(empty($database_events[$controller])){
                        $result['added'][$controller][] = $event;
                    }
                    if(!empty($database_events[$controller]) && !in_array($event, $database_events[$controller])){
                        $result['added'][$controller][] = $event;
                    }
                }
            }
        }

        // удалённые: есть в $database_events, нет в $manifests_events
        if($database_events){
            foreach ($database_events as $controller => $events){
                foreach ($events as $event){
                    if(empty($manifests_events[$controller])){
                        $result['deleted'][$controller][] = $event;
                    }
                    if(!empty($manifests_events[$controller]) && !in_array($event, $manifests_events[$controller])){
                        $result['deleted'][$controller][] = $event;
                    }
                }
            }
        }

        return $result;

    }

    public function getWidgetOptionsForm($widget_name, $controller_name = false, $options = false, $template = false){

        if(!$template){
            $template = $this->cms_config->template;
        }

		$widget_path = cmsCore::getWidgetPath($widget_name, $controller_name);

        $path = $this->cms_config->system_path . $widget_path;

        $form_file = $path . '/options.form.php';

        $form_name = 'widget' . ($controller_name ? "_{$controller_name}_" : '_') . "{$widget_name}_options";

        $form = cmsForm::getForm($form_file, $form_name, array($options, $template));
        if (!$form) { $form = new cmsForm(); }

        $form->is_tabbed = true;

		//
		// Опции внешнего вида
		//
		$design_fieldset_id = $form->addFieldset(LANG_DESIGN);

            $form->addField($design_fieldset_id, new fieldString('class_wrap', array(
                'title' => LANG_CSS_CLASS_WRAP
            )));

            $form->addField($design_fieldset_id, new fieldString('class_title', array(
                'title' => LANG_CSS_CLASS_TITLE
            )));

            $form->addField($design_fieldset_id, new fieldString('class', array(
                'title' => LANG_CSS_CLASS_BODY
            )));

            $form->addField($design_fieldset_id, new fieldList('tpl_wrap', array(
                'title' => LANG_WIDGET_WRAPPER_TPL,
				'hint'  => LANG_WIDGET_WRAPPER_TPL_HINT,
                'generator' => function($item) use ($template){
                    $current_tpls = cmsCore::getFilesList('templates/'.$template.'/widgets', '*.tpl.php');
                    $default_tpls = cmsCore::getFilesList('templates/default/widgets', '*.tpl.php');
                    $tpls = array_unique(array_merge($current_tpls, $default_tpls));
                    $items = array();
                    if ($tpls) {
                        foreach ($tpls as $tpl) {
                            $items[str_replace('.tpl.php', '', $tpl)] = str_replace('.tpl.php', '', $tpl);
                        }
                    }
                    return $items;
                }
            )));

            $form->addField($design_fieldset_id, new fieldList('tpl_body', array(
                'title' => LANG_WIDGET_BODY_TPL,
				'hint' => sprintf(LANG_WIDGET_BODY_TPL_HINT, $widget_path),
                'default' => $widget_name,
                'generator' => function($item) use ($template){
                    $w_path = cmsCore::getWidgetPath($item['name'], $item['controller']);
                    $current_tpls = cmsCore::getFilesList('templates/'.$template.'/'.$w_path, '*.tpl.php');
                    $default_tpls = cmsCore::getFilesList('templates/default/'.$w_path, '*.tpl.php');
                    $tpls = array_unique(array_merge($current_tpls, $default_tpls));
                    $items = array();
                    if ($tpls) {
                        foreach ($tpls as $tpl) {
                            $items[str_replace('.tpl.php', '', $tpl)] = str_replace('.tpl.php', '', $tpl);
                        }
                        asort($items);
                    }
                    return $items;
               }
            )));

        //
        // Опции доступа
        //
        $access_fieldset_id = $form->addFieldset(LANG_PERMISSIONS);

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
                'generator' => function($item) use ($template){
                    $layouts = cmsCore::getFilesList('templates/'.$template.'/', '*.tpl.php');
                    $items = array();
                    if ($layouts) {
                        foreach ($layouts as $layout) {
                            $name = str_replace('.tpl.php', '', $layout);
                            if($name == 'admin'){ continue; }
                            $items[$name] = string_lang('LANG_'.$template.'_THEME_LAYOUT_'.$name, $name);
                        }
                        asort($items);
                    }
                    return $items;
               }
            )));

        //
        // Опции заголовка
        //
        $title_fieldset_id = $form->addFieldsetToBeginning(LANG_BASIC_OPTIONS);

            // ID виджета
            $form->addField($title_fieldset_id, new fieldNumber('id', array(
                'is_hidden'=>true
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

            // Ссылки в заголовке
            $form->addField($title_fieldset_id, new fieldText('links', array(
                'title' => LANG_WIDGET_TITLE_LINKS,
                'hint'  => LANG_WIDGET_TITLE_LINKS_HINT
            )));

		return cmsEventsManager::hook('widget_options_full_form', $form);

    }

    public function getAddonsMethod($name, $params = array(), $cacheable = false) {

        if (!function_exists('curl_init')){
            return false;
        }

        $cache_file = cmsConfig::get('cache_path').md5($name.serialize($params));

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

}
