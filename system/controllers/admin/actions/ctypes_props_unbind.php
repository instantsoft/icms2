<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsUnbind extends cmsAction {

    public function run($ctype_id = null, $prop_id = null, $cat_id = null) {

        if (!$ctype_id || !$prop_id || !$cat_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model_backend_content->unbindContentProp($ctype['name'], $prop_id, $cat_id);

        cmsUser::addSessionMessage(LANG_CP_PROPS_UNBIND_SC, 'success');

        return $this->redirectToAction('ctypes', ['props', $ctype_id]);
    }

}
