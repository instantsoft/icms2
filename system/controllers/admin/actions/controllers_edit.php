<?php

class actionAdminControllersEdit extends cmsAction {

    public function run($controller_name) {

        if (!$controller_name) {
            return cmsCore::error404();
        }

        $controller_info = $this->model->getControllerInfo($controller_name);
        if (!$controller_info) {

            // если компонент имеет несколько контроллеров и один из них использует опции другого
            $controller_info = cmsEventsManager::hook("admin_{$controller_name}_controller_info", false);
            if (!$controller_info) {
                return cmsCore::error404();
            }
        }

        cmsCore::loadControllerLanguage($controller_info['name']);

        $controller_title = string_lang($controller_info['name'] . '_CONTROLLER', $controller_info['title']);

        $this->cms_template->setPageTitle($controller_title);
        $this->cms_template->addBreadcrumb(LANG_CP_SECTION_CONTROLLERS, $this->cms_template->href_to('controllers'));
        $this->cms_template->addBreadcrumb($controller_title, $this->cms_template->href_to('controllers', 'edit/' . $controller_info['name']));

        if (!$controller_info['is_backend']) {

            return $this->cms_template->render('controllers_edit', [
                'is_backend'       => false,
                'ctype'            => false,
                'controller_name'  => $controller_info['name'],
                'controller_title' => $controller_title
            ]);
        }

        //
        // Загружаем бэкенд выбранного контроллера
        //
        $backend_context    = $this->request->isAjax() ? cmsRequest::CTX_AJAX : cmsRequest::CTX_INTERNAL;
        $backend_request    = new cmsRequest($this->request->getData(), $backend_context);
        $backend_controller = $this->loadControllerBackend($controller_info['name'], $backend_request);

        // меню компонента в админке
        $backend_menu = $backend_controller->getBackendMenu();

        $backend_menu = cmsEventsManager::hook("backend_{$controller_info['name']}_menu", $backend_menu);

        // связан ли контроллер с типами контента
        $ctype = cmsCore::getModel('content')->getContentTypeByName($backend_controller->maintained_ctype ? $backend_controller->maintained_ctype : $controller_name);

        // Определяем текущий экшен бэкенда
        $action_name = count($this->params) > 1 ? $this->params[1] : 'index';

        // Сразу включаем экшен опций, чтобы не писать редиректы
        if($action_name === 'index' &&
            !$backend_controller->isActionExists('index') &&
            $backend_controller->isActionExists('options')){

            $action_name = 'options';
        }

        //
        // Удаляем из массива параметров название контроллера и экшен
        //
        if (count($this->params) <= 2) {
            $params = [];
        } else {
            $params = array_slice($this->params, 2);
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

        // Если запрос пришел по AJAX, то выполняем экшен бэкенда сразу же
        if ($this->request->isAjax()) {

            $backend_controller->runAction($action_name, $params);

            return $this->halt();
        }

        // Подключаем CSS бэкенда если он есть
        $css_file = $this->cms_template->getStylesFileName($controller_info['name'], 'backend');
        if ($css_file) {
            $this->cms_template->addCSS($css_file);
        }

        $backend_sub_menu = $backend_controller->getBackendSubMenu();

        $this->cms_template->setMenuItems('breadcrumb-menu', $backend_sub_menu);

        $this->cms_template->setMenuItems('backend', $backend_menu);

        if ($ctype) {

            $this->cms_template->addMenuItem('breadcrumb-menu', [
                'title'   => LANG_CONTENT_TYPE . ' «' . $ctype['title'] . '»',
                'url'     => $this->cms_template->href_to('ctypes', ['edit', $ctype['id']]),
                'options' => [
                    'icon' => 'retweet'
                ]
            ]);
        }

        $help_href_const = 'LANG_HELP_URL_COM_' . strtoupper($backend_controller->name);

        if (defined($help_href_const)) {

            $this->cms_template->addMenuItem('breadcrumb-menu', [
                'title'   => LANG_HELP,
                'url'     => constant($help_href_const),
                'options' => [
                    'target' => '_blank',
                    'icon'   => 'question-circle'
                ]
            ]);
        }

        $html = $backend_controller->runAction($action_name, $params);

        return $this->cms_template->render('controllers_edit', [
            'is_backend'       => true,
            'ctype'            => $ctype,
            'controller_name'  => $controller_info['name'],
            'controller_title' => $controller_title,
            'html'             => $html
        ]);
    }

}
