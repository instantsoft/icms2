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

        // Если запрос пришел по AJAX, то выполняем экшен бакенда сразу же
        // иначе он будет выполнен позже, в шаблоне, чтобы тулбары и pathwey бакенда
        // вывелись позже, чем админки
        if ($this->request->isAjax()){
            $backend_controller->runAction($action_name, $params);
            $this->halt();
        }

        // Подключаем CSS бакенда если он есть
        $css_file = $this->cms_template->getStylesFileName($controller_info['name'], 'backend');
        if ($css_file){ $this->cms_template->addCSS($css_file); }

        $this->cms_template->setMenuItems('backend', $backend_controller->getBackendMenu());

        return $this->cms_template->render('controllers_edit', array(
            'is_backend'         => true,
            'ctype'              => cmsCore::getModel('content')->getContentTypeByName($backend_controller->maintained_ctype ? $backend_controller->maintained_ctype : $controller_name),
            'controller_name'    => $controller_info['name'],
            'controller_title'   => $controller_title,
            'params'             => $params,
            'action_name'        => $action_name,
            'backend_controller' => $backend_controller
        ));

    }

}
