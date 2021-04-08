<?php

class actionAdminCtypesPropsUnbind extends cmsAction {

    public function run($ctype_id, $prop_id, $cat_id) {

        if (!$ctype_id || !$prop_id || !$cat_id) {
            cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $this->model_backend_content->unbindContentProp($ctype['name'], $prop_id, $cat_id);

        cmsUser::addSessionMessage(LANG_CP_PROPS_UNBIND_SC, 'success');

        $this->redirectToAction('ctypes', ['props', $ctype_id]);
    }

}
