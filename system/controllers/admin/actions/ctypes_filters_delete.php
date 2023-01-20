<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesFiltersDelete extends cmsAction {

    public function run($ctype_id = null, $id = null) {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model_backend_content->deleteContentFilter($ctype, $id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('ctypes', ['filters', $ctype['id']]);
    }

}
