<?php

class actionAdminMenuItemDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $menu_model = cmsCore::getModel('menu');

        $menu_model->deleteMenuItem($id);

        $this->redirectToAction('menu');

    }

}
