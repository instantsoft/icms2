<?php

class actionAdminMenuItemDelete extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model_menu->deleteMenuItem($id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('menu');
    }

}
