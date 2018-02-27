<?php

class actionAdminMenuItemDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $this->model_menu->deleteMenuItem($id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('menu');

    }

}
