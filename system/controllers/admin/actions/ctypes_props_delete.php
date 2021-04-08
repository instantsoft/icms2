<?php

class actionAdminCtypesPropsDelete extends cmsAction {

    public function run($ctype_id, $prop_id) {

        if (!$ctype_id || !$prop_id) {
            cmsCore::error404();
        }

        $this->model_backend_content->deleteContentProp($ctype_id, $prop_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('ctypes', ['props', $ctype_id]);
    }

}
