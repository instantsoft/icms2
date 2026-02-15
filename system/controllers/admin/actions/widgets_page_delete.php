<?php

class actionAdminWidgetsPageDelete extends cmsAction {

    public function run($id = false) {

        if (!$id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model_backend_widgets->deletePage($id);

        cmsUser::unsetCookie('widgets_tree_path');

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();
    }

}
