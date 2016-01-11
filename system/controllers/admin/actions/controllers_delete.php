<?php

class actionAdminControllersDelete extends cmsAction {

    public function run( $controller_name ){

        if (!$controller_name) { cmsCore::error404(); }

        $controller_info = $this->model->getControllerInfo($controller_name);
        if (!$controller_info || !$controller_info['is_external']) { cmsCore::error404(); }

        if ($controller_info['is_backend']){

            $backend_context = $this->request->isAjax() ? cmsRequest::CTX_AJAX : cmsRequest::CTX_INTERNAL;
            $backend_request = new cmsRequest($this->request->getData(), $backend_context);
            $backend_controller = $this->loadControllerBackend($controller_info['name'], $backend_request);

            // смотрим специальный экшен
            if($backend_controller->isActionExists('delete_component')){
                $backend_controller->redirectToAction('delete_component');
            }

        }

        // нет бэкэенда или экшена, удаляем через метод модели контроллера
        // если в модели контроллера нет метода deleteController
        // будет использоваться из основной модели
        // который просто удалит запись в cms_controllers
        if(cmsCore::isModelExists($controller_info['name'])){
            cmsCore::getModel($controller_info['name'])->deleteController($controller_info['id']);
        } else {
            $model = new cmsModel();
            $model->deleteController($controller_info['id']);
        }

        cmsUser::addSessionMessage(sprintf(LANG_CP_COMPONENT_IS_DELETED, $controller_info['title']), 'success');

        $this->redirectToAction('controllers');

    }

}