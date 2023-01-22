<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsDelete extends cmsAction {

    public function run($ctype_id = null, $prop_id = null) {

        if (!$ctype_id || !$prop_id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model_backend_content->deleteContentProp($ctype_id, $prop_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('ctypes', ['props', $ctype_id]);
    }

}
