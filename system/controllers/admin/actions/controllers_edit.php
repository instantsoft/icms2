<?php

class actionAdminControllersEdit extends cmsAction {

    public function run( $controller_name ){

        if (!$controller_name) { cmsCore::error404(); }

        $controller_info = $this->model->getControllerInfo($controller_name);
        if (!$controller_info) { cmsCore::error404(); }

        cmsCore::loadControllerLanguage($controller_info['name']);

        $controller_title = constant('LANG_'.mb_strtoupper($controller_info['name']).'_CONTROLLER');

        $template = cmsTemplate::getInstance();

        if (!$controller_info['is_backend']){
            return $template->render('controllers_edit', array(
                'is_backend' => false,
                'controller_name' => $controller_info['name'],
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
        $css_file = $template->getStylesFileName($controller_info['name'], 'backend');
        if ($css_file){ $template->addCSS($css_file); }

        $template->setMenuItems('backend', $backend_controller->getBackendMenu());

        return $template->render('controllers_edit', array(
            'is_backend'         => true,
            'controller_name'    => $controller_info['name'],
            'controller_title'   => $controller_title,
            'params'             => $params,
            'action_name'        => $action_name,
            'backend_controller' => $backend_controller
        ));

    }

}