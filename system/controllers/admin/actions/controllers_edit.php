<?php

class actionAdminControllersEdit extends cmsAction {

    public function run($controller_name){

        if (!$controller_name) { cmsCore::error404(); }

        $controller_info = $this->model->getControllerInfo($controller_name);
        if (!$controller_info) {
            // если компонент имеет несколько контроллеров и один из них использует опции другого
            $controller_info = cmsEventsManager::hook("admin_{$controller_name}_controller_info", false);
            if (!$controller_info) { cmsCore::error404(); }
        }

        cmsCore::loadControllerLanguage($controller_info['name']);

        $controller_title = string_lang($controller_info['name'].'_CONTROLLER', $controller_info['title']);

        $this->cms_template->setPageTitle($controller_title);
        $this->cms_template->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->cms_template->href_to('controllers'));
        $this->cms_template->addBreadcrumb($controller_title, $this->cms_template->href_to('controllers', 'edit/'.$controller_info['name']));

        if (!$controller_info['is_backend']){
            return $this->cms_template->render('controllers_edit', array(
                'is_backend'       => false,
                'ctype'            => false,
                'controller_name'  => $controller_info['name'],
                'controller_title' => $controller_title
            ));
        }

        //
        // Загружаем бакенд выбранного контроллера
        //
        $backend_context = $this->request->isAjax() ? cmsRequest::CTX_AJAX : cmsRequest::CTX_INTERNAL;
        $backend_request = new cmsRequest($this->request->getData(), $backend_context);
        $backend_controller = $this->loadControllerBackend($controller_info['name'], $backend_request);

        // меню компонента в админке
        $backend_menu = $backend_controller->getBackendMenu();

        $backend_menu = cmsEventsManager::hook("backend_{$controller_info['name']}_menu", $backend_menu);

        // связан ли контроллер с типами контента
        $ctype = cmsCore::getModel('content')->getContentTypeByName($backend_controller->maintained_ctype ? $backend_controller->maintained_ctype : $controller_name);

        // Определяем текущий экшен бакенда
        $action_name = sizeof($this->params)>1 ? $this->params[1] : 'index';

        //
        // Удаляем из массива параметров название контроллера и экшен
        //
        if (sizeof($this->params) <= 2) {
            $params = array();
        } else {
            $params = $this->params;
            unset($params[0]);
            unset($params[1]);
        }

        list(
            $backend_controller,
            $action_name,
            $params
            ) = cmsEventsManager::hook("backend_{$controller_info['name']}_before_action", [
                $backend_controller,
                $action_name,
                $params
            ]);

        // Если запрос пришел по AJAX, то выполняем экшен бакенда сразу же
        if ($this->request->isAjax()){
            $backend_controller->runAction($action_name, $params);
            $this->halt();
        }

        // Подключаем CSS бакенда если он есть
        $css_file = $this->cms_template->getStylesFileName($controller_info['name'], 'backend');
        if ($css_file){ $this->cms_template->addCSS($css_file); }

        $backend_sub_menu = $backend_controller->getBackendSubMenu();

        $this->cms_template->setMenuItems('breadcrumb-menu', $backend_sub_menu);

        $this->cms_template->setMenuItems('backend', $backend_menu);

        if($ctype){

            $this->cms_template->addMenuItem('breadcrumb-menu', [
                'title' => LANG_CONTENT_TYPE.' «'.$ctype['title'].'»',
                'url'   => $this->cms_template->href_to('ctypes', array('edit', $ctype['id'])),
                'options' => array(
                    'icon'  => 'icon-settings'
                )
            ]);

        }

        $help_href_const = 'LANG_HELP_URL_COM_'.strtoupper($backend_controller->name);
        if(defined($help_href_const)){
            $this->cms_template->addMenuItem('breadcrumb-menu', [
                'title' => LANG_HELP,
                'url'   => constant($help_href_const),
                'options' => [
                    'target' => '_blank',
                    'icon' => 'icon-question'
                ]
            ]);
        }

        $html = $backend_controller->runAction($action_name, $params);

        return $this->cms_template->render('controllers_edit', array(
            'is_backend'         => true,
            'ctype'              => $ctype,
            'controller_name'    => $controller_info['name'],
            'controller_title'   => $controller_title,
            'html'               => $html
        ));

    }

}
